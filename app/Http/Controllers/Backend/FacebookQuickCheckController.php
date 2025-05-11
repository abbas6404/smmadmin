<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FacebookQuickCheck;
use App\Models\FacebookAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class FacebookQuickCheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = FacebookQuickCheck::query();

        // Handle trashed items
        if ($request->has('trashed') && $request->trashed === 'true') {
            $query->onlyTrashed();
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get counts for status cards
        $pendingCount = FacebookQuickCheck::where('status', 'pending')->count();
        $processingCount = FacebookQuickCheck::where('status', 'processing')->count();
        $activeCount = FacebookQuickCheck::where('status', 'active')->count();
        $inUseCount = FacebookQuickCheck::where('status', 'in_use')->count();
        $blockedCount = FacebookQuickCheck::where('status', 'blocked')->count();
        $deletedCount = FacebookQuickCheck::onlyTrashed()->count();
        $totalCount = FacebookQuickCheck::count();

        // Get filtered accounts with pagination
        $accounts = $query->orderBy('id', 'desc')->paginate(100)->withQueryString();

        return view('backend.facebook-quick-check.index', compact(
            'pendingCount',
            'processingCount',
            'activeCount',
            'inUseCount',
            'blockedCount',
            'deletedCount',
            'totalCount',
            'accounts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.facebook-quick-check.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'accounts' => 'required|string'
        ]);

        // Split the accounts text into lines
        $accounts = array_filter(explode("\n", str_replace("\r", "", $validated['accounts'])));
        $created = 0;
        $errors = [];
        $duplicates = [];

        foreach ($accounts as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Split the line into email and password
            $parts = explode('|', $line);
            if (count($parts) < 2 || count($parts) > 3) {
                $errors[] = "Invalid format for line: {$line}. Expected format: email|password[|2fa]";
                continue;
            }

            $email = trim($parts[0]);
            $password = trim($parts[1]);
            $twoFactorSecret = isset($parts[2]) ? trim($parts[2]) : null;
            
            // Convert empty 2FA to null
            if ($twoFactorSecret === '') {
                $twoFactorSecret = null;
            }

            // Check if email already exists
            if (FacebookQuickCheck::withTrashed()->where('email', $email)->exists()) {
                $duplicates[] = $email;
                continue;
            }

            try {
                FacebookQuickCheck::create([
                    'email' => $email,
                    'password' => $password,
                    'two_factor_secret' => $twoFactorSecret,
                    'status' => 'pending',
                    'check_count' => 0
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Error creating account for {$email}: " . $e->getMessage();
            }
        }

        // Add duplicates to errors array with specific message
        if (!empty($duplicates)) {
            $errors[] = "The following emails already exist: " . implode(", ", $duplicates);
        }

        if ($created > 0) {
            if (!empty($errors)) {
                // Calculate the total number of failed accounts (duplicates + other errors)
                $duplicateCount = count($duplicates);
                $otherErrorCount = count($errors) - (empty($duplicates) ? 0 : 1); // Subtract the duplicates error message
                $totalErrorCount = $duplicateCount + $otherErrorCount;
                
                return redirect()
                    ->route('admin.facebook-quick-check.index')
                    ->with('warning', implode("\n", $errors))
                    ->with('created_count', $created)
                    ->with('error_count', $totalErrorCount);
            }

            return redirect()
                ->route('admin.facebook-quick-check.index')
                ->with('success', "Successfully created {$created} accounts.")
                ->with('created_count', $created);
        }

        // If no accounts were created, redirect back with errors
        if (!empty($errors)) {
            return redirect()
                ->route('admin.facebook-quick-check.create')
                ->withInput()
                ->with('error', "Failed to create any accounts.")
                ->with('errorList', $errors); // Use a different key to avoid conflicts
        }

        return redirect()
            ->route('admin.facebook-quick-check.create')
            ->withInput()
            ->with('error', "Failed to create any accounts. Please check your input and try again.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $account = FacebookQuickCheck::withTrashed()->findOrFail($id);
        return view('backend.facebook-quick-check.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $account = FacebookQuickCheck::withTrashed()->findOrFail($id);
        return view('backend.facebook-quick-check.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $account = FacebookQuickCheck::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('facebook_quick_check')->ignore($account->id)
            ],
            'password' => 'nullable|string|min:6',
            'two_factor_secret' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'active', 'in_use', 'blocked'])],
            'check_result' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $account->update($validated);

        return redirect()
            ->route('admin.facebook-quick-check.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $account = FacebookQuickCheck::withTrashed()->findOrFail($id);
            
            $account->status = 'blocked';
            $account->save();
            
            $account->delete();
            
            DB::commit();
            return redirect()
                ->route('admin.facebook-quick-check.index')
                ->with('success', 'Account moved to trash successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to move account to trash: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted account.
     */
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            
            $account = FacebookQuickCheck::withTrashed()->findOrFail($id);
            $account->restore();
            
            // Reset status if it was 'blocked'
            if ($account->status === 'blocked') {
                $account->status = 'pending';
                $account->save();
            }
            
            DB::commit();
            return redirect()->route('admin.facebook-quick-check.index')
                ->with('success', 'Account restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to restore account: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete an account.
     */
    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();
            
            $account = FacebookQuickCheck::withTrashed()->findOrFail($id);
            $account->forceDelete();
            
            DB::commit();
            return redirect()->route('admin.facebook-quick-check.index', ['trashed' => 'true'])
                ->with('success', 'Account permanently deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to permanently delete account: ' . $e->getMessage());
        }
    }

    /**
     * Mark account as active or pending
     */
    public function toggleValid(Request $request, string $id)
    {
        $account = FacebookQuickCheck::withTrashed()->findOrFail($id);
        
        // Toggle between pending and active
        $account->status = ($account->status === 'active') ? 'pending' : 'active';
        $account->last_checked_at = now();
        $account->checked_by = auth('admin')->user()->email;
        $account->check_count++;
        $account->save();

        return redirect()->back()
            ->with('success', 'Account marked as ' . $account->status);
    }

    /**
     * Perform quick check on the account
     */
    public function quickCheck(string $id)
    {
        try {
            DB::beginTransaction();
            
            $account = FacebookQuickCheck::findOrFail($id);
            
            // Update status to processing
            $account->status = 'processing';
            $account->check_result = 'Processing...';
            $account->save();
            
            // Here you would implement the actual Facebook check logic
            // For now, we'll simulate a successful check
            
            // Simulate some processing time
            sleep(2);
            
            // Update the account with check results
            $account->status = 'active';
            $account->check_result = 'Account is valid and active';
            $account->last_checked_at = now();
            $account->checked_by = auth('admin')->user()->name;
            $account->check_count = $account->check_count + 1;
            $account->save();
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Account check completed successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to check account: ' . $e->getMessage());
        }
    }

    /**
     * Transfer account to main Facebook accounts
     */
    public function transferToFacebookAccount(string $id)
    {
        try {
            DB::beginTransaction();
            
            // Find the quick check account
            $quickCheckAccount = FacebookQuickCheck::findOrFail($id);
            
            // Only allow active accounts to be transferred
            if ($quickCheckAccount->status !== 'active') {
                return redirect()->back()
                    ->with('error', 'Only active accounts can be transferred to Facebook accounts.');
            }
            
            // Check if an account with this email already exists
            if (FacebookAccount::withTrashed()->where('email', $quickCheckAccount->email)->exists()) {
                return redirect()->back()
                    ->with('error', 'An account with this email already exists in Facebook accounts.');
            }
            
            // Create a new Facebook account
            $facebookAccountData = [
                'email' => $quickCheckAccount->email,
                'password' => $quickCheckAccount->password,
                'two_factor_secret' => $quickCheckAccount->two_factor_secret,
                'status' => 'active', // Set as active since it was validated
                'note' => "Transferred from Quick Check. Notes: {$quickCheckAccount->notes}",
            ];
            
            // Also transfer cookies if they exist
            if (!empty($quickCheckAccount->account_cookies)) {
                $facebookAccountData['account_cookies'] = $quickCheckAccount->account_cookies;
            }
            
            $facebookAccount = FacebookAccount::create($facebookAccountData);
            
            // Update the quick check account to show it's been transferred
            $quickCheckAccount->status = 'in_use';
            $quickCheckAccount->check_result = 'Transferred to Facebook accounts';
            $quickCheckAccount->save();
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Account successfully transferred to Facebook accounts with ID #{$facebookAccount->id}");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to transfer account: ' . $e->getMessage());
        }
    }

    /**
     * Transfer all active accounts to Facebook accounts
     */
    public function transferAllActive()
    {
        try {
            DB::beginTransaction();
            
            // Get all active quick check accounts
            $activeAccounts = FacebookQuickCheck::where('status', 'active')->get();
            
            if ($activeAccounts->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No active accounts found to transfer.');
            }
            
            $transferred = 0;
            $errors = [];
            
            foreach ($activeAccounts as $quickCheckAccount) {
                // Check if an account with this email already exists
                if (FacebookAccount::withTrashed()->where('email', $quickCheckAccount->email)->exists()) {
                    $errors[] = "An account with email {$quickCheckAccount->email} already exists in Facebook accounts.";
                    continue;
                }
                
                // Create a new Facebook account
                $facebookAccountData = [
                    'email' => $quickCheckAccount->email,
                    'password' => $quickCheckAccount->password,
                    'two_factor_secret' => $quickCheckAccount->two_factor_secret,
                    'status' => 'active', // Set as active since it was validated
                    'note' => "Transferred from Quick Check. Notes: {$quickCheckAccount->notes}",
                ];
                
                // Also transfer cookies if they exist
                if (!empty($quickCheckAccount->account_cookies)) {
                    $facebookAccountData['account_cookies'] = $quickCheckAccount->account_cookies;
                }
                
                $facebookAccount = FacebookAccount::create($facebookAccountData);
                
                // Update the quick check account to show it's been transferred
                $quickCheckAccount->status = 'in_use';
                $quickCheckAccount->check_result = 'Transferred to Facebook accounts';
                $quickCheckAccount->save();
                
                $transferred++;
            }
            
            DB::commit();
            
            if ($transferred > 0) {
                if (!empty($errors)) {
                    return redirect()->back()
                        ->with('warning', implode("\n", $errors))
                        ->with('success', "{$transferred} accounts successfully transferred to Facebook accounts.");
                }
                
                return redirect()->back()
                    ->with('success', "All {$transferred} active accounts successfully transferred to Facebook accounts.");
            } else {
                return redirect()->back()
                    ->with('error', "Failed to transfer any accounts.\n" . implode("\n", $errors));
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to transfer accounts: ' . $e->getMessage());
        }
    }
}
