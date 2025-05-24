<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PcProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PcProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query non-deleted profiles by default, unless filtering for 'deleted' status
        $query = PcProfile::query(); 

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pc_name', 'like', "%{$search}%")
                  ->orWhere('hardware_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'deleted') {
                $query->onlyTrashed(); // Show only soft-deleted
            } else {
                $query->where('status', $request->status);
                // Ensure non-deleted are shown when filtering by active/inactive/blocked
                 $query->whereNull('deleted_at'); 
            }
        }
        // Default query (no status filter) will implicitly exclude trashed by SoftDeletes trait

        // Get counts for status cards
        $activeCount = PcProfile::where('status', 'active')->count();
        $inactiveCount = PcProfile::where('status', 'inactive')->count();
        $blockedCount = PcProfile::where('status', 'blocked')->count();
        $deletedCount = PcProfile::onlyTrashed()->count(); // Count soft-deleted

        $profiles = $query->orderBy('created_at', 'desc')->paginate(100);

        return view('backend.pc_profile.index', compact(
            'profiles',
            'activeCount',
            'inactiveCount',
            'blockedCount',
            'deletedCount' // Pass deleted count
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pc_profile.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pc_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'hardware_id' => 'nullable|string|max:255',
            'hostname' => 'nullable|string|max:255',
            'os_version' => 'nullable|string|max:255',
            'user_agent' => ['nullable', 'string', 'regex:/^\d+-\d+$/', function ($attribute, $value, $fail) {
                $numbers = explode('-', $value);
                if (count($numbers) !== 2) {
                    $fail('The user agent must contain exactly two numbers separated by a hyphen.');
                    return;
                }
                if (!is_numeric($numbers[0]) || !is_numeric($numbers[1])) {
                    $fail('Both values must be numbers.');
                    return;
                }
                if ($numbers[0] >= $numbers[1]) {
                    $fail('The first number must be less than the second number.');
                }
            }],
            'drive' => 'required|string|in:C,D,E,F',
            'profile_root_directory' => 'required|string|max:255',
            'max_profile_limit' => 'required|integer|min:1',
            'max_order_limit' => 'required|integer|min:1',
            'min_order_limit' => 'required|integer|min:1',
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            DB::beginTransaction();

            // Hash the password
            $validated['password'] = Hash::make($validated['password']);
            
            // Always set status as inactive for new profiles
            $validated['status'] = 'inactive';

            // Combine drive and folder name, ensuring no extra backslashes
            $folderName = trim($validated['profile_root_directory'], '\\/');
            $validated['profile_root_directory'] = $validated['drive'] . ':\\' . $folderName;
            unset($validated['drive']);

            // Create the profile first to get the ID
            $pcProfile = PcProfile::create($validated);

            // Update the pc_name with the ID prefix
            $pcProfile->update([
                'pc_name' => "#{$pcProfile->id}_{$validated['pc_name']}"
            ]);

            DB::commit();

            return redirect()
                ->route('admin.pc-profiles.index')
                ->with('success', 'PC Profile created successfully with inactive status.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'create' => 'An error occurred while creating the PC Profile. Please try again.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) // Use string ID
    {
        // Find profile including soft-deleted ones and load disks
        $pcProfile = PcProfile::withTrashed()->with('disks')->findOrFail($id); 
        return view('backend.pc_profile.show', compact('pcProfile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) // Use string ID
    {
        // Find profile including soft-deleted ones
        $pcProfile = PcProfile::withTrashed()->findOrFail($id);
        return view('backend.pc_profile.edit', compact('pcProfile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find profile including soft-deleted ones
        $pcProfile = PcProfile::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'pc_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'hardware_id' => ['nullable', 'string', 'max:255', Rule::unique('pc_profiles')->ignore($pcProfile->id)],
            'hostname' => ['nullable', 'string', 'max:255', Rule::unique('pc_profiles')->ignore($pcProfile->id)],
            'os_version' => ['nullable', Rule::in(['Windows 10 Home', 'Windows 10 Pro', 'Windows 11 Home', 'Windows 11 Pro'])],
            'user_agent' => ['nullable', 'string', 'regex:/^\d+-\d+$/', function ($attribute, $value, $fail) {
                $numbers = explode('-', $value);
                if (count($numbers) !== 2) {
                    $fail('The user agent must contain exactly two numbers separated by a hyphen.');
                    return;
                }
                if (!is_numeric($numbers[0]) || !is_numeric($numbers[1])) {
                    $fail('Both values must be numbers.');
                    return;
                }
                if ($numbers[0] >= $numbers[1]) {
                    $fail('The first number must be less than the second number.');
                }
            }],
            'drive' => 'required|string|in:C,D,E,F',
            'profile_root_directory' => 'required|string|max:255',
            'max_profile_limit' => 'required|integer|min:1',
            'max_order_limit' => 'required|integer|min:1',
            'min_order_limit' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['active', 'inactive', 'blocked'])],
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        try {
            DB::beginTransaction();

            // If password is being changed, set status to inactive
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
                $validated['status'] = 'inactive';
            } else {
                unset($validated['password']);
            }

            // Combine drive and folder name, ensuring no extra backslashes
            $folderName = trim($validated['profile_root_directory'], '\\/');
            $validated['profile_root_directory'] = $validated['drive'] . ':\\' . $folderName;
            unset($validated['drive']);

            // Update the profile
            $pcProfile->update($validated);

            DB::commit();

            $message = !empty($request->password) 
                ? 'PC Profile updated successfully. Status set to inactive due to password change.'
                : 'PC Profile updated successfully.';

            return redirect()
                ->route('admin.pc-profiles.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'update' => 'An error occurred while updating the PC Profile. Please try again.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find profile including soft-deleted ones to prevent errors if clicked twice
        $pcProfile = PcProfile::withTrashed()->findOrFail($id);

        // If already soft deleted, return early with success message
        if ($pcProfile->trashed()) {
            return redirect()->route('admin.pc-profiles.index')
                ->with('success', 'PC Profile is already deleted.');
        }
        
        try {
            DB::beginTransaction();
            
            // Set status to deleted before soft deleting
            $pcProfile->status = 'deleted';
            $pcProfile->save();
            
            // Perform soft delete
            $pcProfile->delete();
            
            DB::commit();

            // Check if there were associated accounts and modify the success message
            $hasAssociatedAccounts = $pcProfile->chromeProfiles()->exists() || 
                                   $pcProfile->facebookAccounts()->exists() || 
                                   $pcProfile->gmailAccounts()->exists();

            $message = $hasAssociatedAccounts 
                ? 'PC Profile has been marked as deleted. Associated accounts are preserved but inactive.'
                : 'PC Profile has been deleted successfully.';
            
            return redirect()->route('admin.pc-profiles.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'delete' => 'An error occurred while deleting the PC Profile. Please try again.'
            ]);
        }
    }
} 