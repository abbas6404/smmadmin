<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FacebookAccount;
use App\Models\PcProfile;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FacebookAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get PC Profiles for the filter dropdown
        $pcProfiles = PcProfile::orderBy('pc_name')->get();
        
        // Get Submission Batches for the filter dropdown
        $submissionBatches = DB::table('submission_batch')
            ->where('submission_type', 'facebook')
            ->orderBy('created_at', 'desc')
            ->get();

        // Start building the query
        $query = FacebookAccount::with(['chromeProfile', 'pcProfile', 'submissionBatch']);

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
        
        if ($request->filled('submission_batch_id')) {
            $query->where('submission_batch_id', $request->submission_batch_id);
        }

        if ($request->filled('have_page')) {
            $query->where('have_page', $request->have_page);
        }

        if ($request->filled('lang')) {
            $query->where('lang', $request->lang);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get counts for status cards
        $pendingCount = FacebookAccount::where('status', 'pending')->count();
        $processingCount = FacebookAccount::where('status', 'processing')->count();
        $activeCount = FacebookAccount::where('status', 'active')->count();
        $inactiveCount = FacebookAccount::where('status', 'inactive')->count();
        $deletedCount = FacebookAccount::onlyTrashed()->count();
        $totalCount = FacebookAccount::count();

        // Get filtered accounts with pagination
        $accounts = $query->latest()->paginate(15);

        return view('backend.facebook.index', compact(
            'pendingCount',
            'processingCount',
            'activeCount',
            'inactiveCount',
            'deletedCount',
            'totalCount',
            'accounts',
            'pcProfiles',
            'submissionBatches'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $facebook = FacebookAccount::withTrashed()->with(['pcProfile', 'chromeProfile', 'submissionBatch'])->findOrFail($id);
        return view('backend.facebook.show', compact('facebook'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $facebook = FacebookAccount::withTrashed()->with(['pcProfile', 'chromeProfile'])->findOrFail($id);
        return view('backend.facebook.edit', compact('facebook'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $facebook = FacebookAccount::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'email' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($facebook) {
                    // Check if it's a numeric UID
                    if (is_numeric($value)) {
                        return;
                    }
                    // If not numeric, it must be a valid email
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('The identifier must be either a numeric UID or a valid email address.');
                    }
                },
                Rule::unique('facebook_accounts')->ignore($facebook->id)
            ],
            'password' => 'nullable|string|min:8',
            'status' => ['required', Rule::in(['pending', 'processing', 'active', 'inactive', 'remove'])],
            'have_use' => 'boolean',
            'have_page' => 'boolean',
            'have_post' => 'boolean',
            'note' => 'nullable|string'
        ]);

        // Hash password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $facebook->update($validated);

        return redirect()
            ->route('admin.facebook.index')
            ->with('success', 'Facebook account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * Sets status to 'remove' and soft deletes.
     * Can be called on any status, including already removed.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            // Find account including already soft-deleted ones
            $facebook = FacebookAccount::withTrashed()->findOrFail($id); 
            
            $facebook->status = 'remove';
            $facebook->save();
            
            // Perform soft delete (will update deleted_at even if already set)
            $facebook->delete(); 
            
            DB::commit();
            return redirect()
                ->route('admin.facebook.index')
                ->with('success', 'Facebook account moved to trash successfully.');
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
            
            $facebook = FacebookAccount::withTrashed()->findOrFail($id);
            $facebook->restore();
            
            // Reset status if it was 'remove'
            if ($facebook->status === 'remove') {
                $facebook->status = 'inactive';
                $facebook->save();
            }
            
            DB::commit();
            return redirect()->route('admin.facebook.index')
                ->with('success', 'Facebook account restored successfully.');
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
            
            $facebook = FacebookAccount::withTrashed()->findOrFail($id);
            $facebook->forceDelete();
            
            DB::commit();
            return redirect()->route('admin.facebook.index', ['trashed' => 'true'])
                ->with('success', 'Facebook account permanently deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to permanently delete account: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pcProfiles = PcProfile::where('status', 'active')->orderBy('pc_name')->get();
        $submissionBatches = DB::table('submission_batch')
            ->where('submission_type', 'facebook')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get the next available batch ID
        $nextBatchId = DB::table('submission_batch')->max('id') + 1;
            
        return view('backend.facebook.create', compact('pcProfiles', 'submissionBatches', 'nextBatchId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pc_profile_id' => 'required|exists:pc_profiles,id',
            'batch_type' => 'required|in:existing,new',
            'submission_batch_id' => 'required_if:batch_type,existing|exists:submission_batch,id',
            'new_batch_name' => 'required_if:batch_type,new|string|max:255',
            'accounts' => 'required|string'
        ]);

        // Handle new batch creation if needed
        $submissionBatchId = null;
        if ($validated['batch_type'] === 'new') {
            $submissionBatch = DB::table('submission_batch')->insertGetId([
                'user_id' => auth()->id(),
                'name' => $validated['new_batch_name'],
                'submission_type' => 'facebook',
                'total_submissions' => 0,
                'accurate_submissions' => 0,
                'incorrect_submissions' => 0,
                'approved' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $submissionBatchId = $submissionBatch;
        } else {
            $submissionBatchId = $validated['submission_batch_id'];
        }

        // Split the accounts text into lines
        $accounts = array_filter(explode("\n", str_replace("\r", "", $validated['accounts'])));
        $created = 0;
        $errors = [];

        foreach ($accounts as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Split the line into email and password
            $parts = explode('|', $line);
            if (count($parts) !== 2) {
                $errors[] = "Invalid format for line: {$line}. Expected format: email|password";
                continue;
            }

            $email = trim($parts[0]);
            $password = trim($parts[1]);

            // Check if identifier already exists
            if (FacebookAccount::withTrashed()->where('email', $email)->exists()) {
                $errors[] = "Identifier already exists: {$email}";
                continue;
            }

            try {
                FacebookAccount::create([
                    'pc_profile_id' => $validated['pc_profile_id'],
                    'submission_batch_id' => $submissionBatchId,
                    'email' => $email,
                    'password' => $password,
                    'total_count' => 0,
                    'have_use' => false,
                    'have_page' => false,
                    'have_post' => false,
                    'status' => 'pending',
                    'order_link_uid' => []
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Error creating account for {$email}: " . $e->getMessage();
            }
        }

        // Update the total_submissions count in the submission_batch table
        if ($created > 0 && $submissionBatchId) {
            DB::table('submission_batch')
                ->where('id', $submissionBatchId)
                ->increment('total_submissions', $created);
        }

        if ($created > 0) {
            $message = "{$created} account(s) created successfully.";
            if (!empty($errors)) {
                $message .= " However, there were some errors: " . implode("; ", $errors);
                return redirect()
                    ->route('admin.facebook.index')
                    ->with('warning', $message);
            }
            return redirect()
                ->route('admin.facebook.index')
                ->with('success', $message);
        }

        return redirect()
            ->route('admin.facebook.create')
            ->withInput()
            ->withErrors(['accounts' => 'No accounts were created. ' . implode("; ", $errors)]);
    }
}
