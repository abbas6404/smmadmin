<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\GmailAccount; // Use GmailAccount model
use App\Models\PcProfile;
use App\Models\SubmissionBatch;
use App\Models\FacebookAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GmailAccountController extends Controller // Rename class
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get PC Profiles for the filter dropdown
        $pcProfiles = PcProfile::orderBy('pc_name')->get();

        // Start building the query
        $query = GmailAccount::with(['chromeProfile', 'pcProfile', 'submissionBatch']);

        // Handle trashed items
        if ($request->has('trashed') && $request->trashed === 'true') {
            $query->onlyTrashed();
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        if ($request->filled('pc_profile_id')) {
            $query->where('pc_profile_id', $request->pc_profile_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get counts for status cards
        $totalCount = GmailAccount::count();
        $pendingCount = GmailAccount::where('status', 'pending')->count();
        $processingCount = GmailAccount::where('status', 'processing')->count();
        $activeCount = GmailAccount::where('status', 'active')->count();
        $inactiveCount = GmailAccount::where('status', 'inactive')->count();

        // Get filtered accounts with pagination
        $accounts = $query->latest()->paginate(15);

        return view('backend.gmail.index', compact(
            'totalCount',
            'pendingCount',
            'processingCount',
            'activeCount',
            'inactiveCount',
            'accounts',
            'pcProfiles'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pcProfiles = PcProfile::where('status', 'active')->get();
        $submissionBatches = SubmissionBatch::where('submission_type', 'gmail')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the next batch ID for auto-generation
        $nextBatchId = SubmissionBatch::max('id') + 1;
        
        return view('backend.gmail.create', compact('pcProfiles', 'submissionBatches', 'nextBatchId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pc_profile_id' => 'nullable|exists:pc_profiles,id',
            'batch_type' => 'required|in:existing,new',
            'submission_batch_id' => 'required_if:batch_type,existing|nullable|exists:submission_batch,id',
            'new_batch_name' => 'required_if:batch_type,new|nullable|string|max:255',
            'accounts' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Create or get batch
            $batchId = null;
            $batchType = 'gmail'; // Default type
            
            if ($request->batch_type === 'new') {
                $batch = new SubmissionBatch();
                $batch->user_id = auth()->id();
                $batch->name = $request->new_batch_name;
                $batch->submission_type = 'facebook_and_gmail'; // Use combined type
                $batch->save();
                $batchId = $batch->id;
            } else {
                $batchId = $request->submission_batch_id;
            }

            // Process accounts
            $accounts = explode("\n", str_replace("\r", "", $request->accounts));
            $gmailCreatedCount = 0;
            $fbCreatedCount = 0;
            $errorMessages = [];

            foreach ($accounts as $account) {
                $account = trim($account);
                if (empty($account)) continue;

                $parts = explode('|', $account);
                $partCount = count($parts);

                // Validate format
                if ($partCount !== 2 && $partCount !== 4) {
                    $errorMessages[] = "Invalid format for account: {$account}. Expected either email|password or gmail|gmailpass|fbid|fbpass";
                    continue;
                }

                // Process Gmail account
                $gmailEmail = trim($parts[0]);
                $gmailPassword = trim($parts[1]);

                // Validate Gmail email format
                if (!filter_var($gmailEmail, FILTER_VALIDATE_EMAIL)) {
                    $errorMessages[] = "Invalid Gmail email format: {$gmailEmail}";
                    continue;
                }

                // Check for duplicate Gmail email
                if (GmailAccount::withTrashed()->where('email', $gmailEmail)->exists()) {
                    $errorMessages[] = "Gmail email already exists: {$gmailEmail}";
                    continue;
                }

                // Create Gmail account
                $gmail = new GmailAccount();
                $gmail->pc_profile_id = $request->pc_profile_id;
                $gmail->submission_batch_id = $batchId;
                $gmail->email = $gmailEmail;
                $gmail->password = Hash::make($gmailPassword);
                $gmail->status = 'pending';
                $gmail->save();

                $gmailCreatedCount++;

                // If we have Facebook account data (4 parts), create Facebook account
                if ($partCount === 4) {
                    try {
                        $fbIdentifier = trim($parts[2]);
                        $fbPassword = trim($parts[3]);

                        $facebook = new FacebookAccount();
                        $facebook->pc_profile_id = $request->pc_profile_id;
                        $facebook->submission_batch_id = $batchId;
                        $facebook->gmail_account_id = $gmail->id;
                        $facebook->email = $fbIdentifier;
                        $facebook->password = Hash::make($fbPassword);
                        $facebook->status = 'pending';
                        $facebook->save();

                        $fbCreatedCount++;
                    } catch (\Exception $e) {
                        $errorMessages[] = "Failed to create Facebook account for Gmail {$gmailEmail}: " . $e->getMessage();
                    }
                }
            }

            // Update batch statistics
            if ($batchId) {
                $batch = SubmissionBatch::find($batchId);
                $batch->total_submissions = $batch->gmailAccounts()->count() + $batch->facebookAccounts()->count();
                $batch->save();
            }

            DB::commit();

            $message = "{$gmailCreatedCount} Gmail account(s)";
            if ($fbCreatedCount > 0) {
                $message .= " and {$fbCreatedCount} Facebook account(s)";
            }
            $message .= " created successfully.";

            if (!empty($errorMessages)) {
                $message .= " However, there were some issues: " . implode(", ", $errorMessages);
                return redirect()
                    ->route('admin.gmail.index')
                    ->with('warning', $message);
            }

            return redirect()
                ->route('admin.gmail.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create accounts: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gmail = GmailAccount::withTrashed()->with(['pcProfile', 'chromeProfile'])->findOrFail($id); // Use GmailAccount
        return view('backend.gmail.show', compact('gmail')); // Change view path and variable name
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gmail = GmailAccount::withTrashed()->with(['pcProfile', 'chromeProfile'])->findOrFail($id); // Use GmailAccount
        return view('backend.gmail.edit', compact('gmail')); // Change view path and variable name
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gmail = GmailAccount::withTrashed()->findOrFail($id); // Use GmailAccount

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('gmail_accounts')->ignore($gmail->id)], // Check gmail_accounts table
            'password' => 'nullable|string|min:8',
            'status' => ['required', Rule::in(['pending', 'processing', 'active', 'inactive', 'remove'])],
            'have_use' => 'boolean', // Keep these if they apply to Gmail
            // 'have_page' => 'boolean', // Remove if not applicable
            // 'have_post' => 'boolean', // Remove if not applicable
            // 'lang' => 'required|string|max:10', // Remove if not applicable
            'note' => 'nullable|string',
        ]);

        // Hash password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $gmail->update($validated); // Use GmailAccount variable

        return redirect()
            ->route('admin.gmail.index') // Change route name
            ->with('success', 'Gmail account updated successfully.'); // Change message
    }

    /**
     * Remove the specified resource from storage.
     * Sets status to 'remove' and soft deletes.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $gmail = GmailAccount::findOrFail($id);
            
            // Update status and soft delete
            $gmail->status = 'remove';
            $gmail->save();
            $gmail->delete();
            
            DB::commit();
            return redirect()
                ->route('admin.gmail.index')
                ->with('success', 'Gmail account moved to trash successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
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
            
            $gmail = GmailAccount::withTrashed()->findOrFail($id);
            $gmail->restore();
            
            // Reset status if it was 'remove'
            if ($gmail->status === 'remove') {
                $gmail->status = 'inactive';
                $gmail->save();
            }
            
            DB::commit();
            return redirect()
                ->route('admin.gmail.index')
                ->with('success', 'Gmail account restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to restore account: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a Gmail account.
     */
    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();
            
            $gmail = GmailAccount::withTrashed()->findOrFail($id);
            $gmail->forceDelete();
            
            DB::commit();
            return redirect()
                ->route('admin.gmail.index', ['trashed' => 'true'])
                ->with('success', 'Gmail account permanently deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to permanently delete account: ' . $e->getMessage());
        }
    }
} 