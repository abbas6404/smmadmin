<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PcProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

        $profiles = $query->orderBy('created_at', 'desc')->paginate(15);

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
            'hardware_id' => 'required|string|max:255|unique:pc_profiles,hardware_id',
            'max_profile_limit' => 'required|integer|min:1',
            'max_link_limit' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['active', 'inactive', 'blocked'])]
        ]);

        PcProfile::create($validated);

        return redirect()->route('admin.pc-profiles.index')->with('success', 'PC Profile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) // Use string ID
    {
        // Find profile including soft-deleted ones
        $pcProfile = PcProfile::withTrashed()->findOrFail($id); 
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
    public function update(Request $request, string $id) // Use string ID
    {
        // Find profile including soft-deleted ones
        $pcProfile = PcProfile::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'pc_name' => 'required|string|max:255',
            'hardware_id' => ['required', 'string', 'max:255', Rule::unique('pc_profiles')->ignore($pcProfile->id)],
            'max_profile_limit' => 'required|integer|min:1',
            'max_link_limit' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['active', 'inactive', 'blocked', 'deleted'])] // Include deleted status
        ]);

        // If status is changed to 'deleted', perform soft delete
        if ($validated['status'] === 'deleted' && !$pcProfile->trashed()) {
            $pcProfile->status = 'deleted';
            $pcProfile->save();
            $pcProfile->delete(); // Soft delete
            return redirect()
                ->route('admin.pc-profiles.index')
                ->with('success', 'PC Profile marked as deleted and soft-deleted.');
        } 
        // If status is changed FROM 'deleted' to something else, restore the profile
        elseif ($validated['status'] !== 'deleted' && $pcProfile->trashed()) {
             $pcProfile->restore(); // Restore from soft delete
             $pcProfile->update($validated); // Update other fields including new status
             return redirect()
                ->route('admin.pc-profiles.index')
                ->with('success', 'PC Profile restored and updated successfully.');
        }
        // Otherwise, just update normally
        else {
            $pcProfile->update($validated);
             return redirect()
                ->route('admin.pc-profiles.index')
                ->with('success', 'PC Profile updated successfully.');
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