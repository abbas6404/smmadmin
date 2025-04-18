<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ChromeProfile;
use App\Models\PcProfile; // Import PcProfile
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChromeProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pcProfiles = PcProfile::orderBy('pc_name')->get();

        $query = ChromeProfile::with('pcProfile');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('profile_directory', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }

        if ($request->filled('pc_profile_id')) {
            $query->where('pc_profile_id', $request->pc_profile_id);
        }

        if ($request->filled('status')) {
            // Handle 'remove' status filter - show ONLY soft-deleted items
            if ($request->status === 'remove') {
                // Remove any default non-trashed scope and apply onlyTrashed
                $query->onlyTrashed(); 
            } else {
                // For other statuses, query normally (non-trashed items)
                $query->where('status', $request->status);
            }
        } 
        // By default (no status filter), the query fetches non-trashed items due to SoftDeletes trait.
        
        // Get counts for status cards (counts are based on non-trashed except for removedCount)
        $pendingCount = ChromeProfile::where('status', 'pending')->count();
        $activeCount = ChromeProfile::where('status', 'active')->count();
        $inactiveCount = ChromeProfile::where('status', 'inactive')->count();
        // removedCount specifically counts soft-deleted items
        $removedCount = ChromeProfile::onlyTrashed()->count(); 

        $profiles = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.chrome.index', compact(
            'profiles', 
            'pcProfiles',
            'pendingCount',
            'activeCount',
            'inactiveCount',
            'removedCount' // Pass removedCount
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $chrome = ChromeProfile::withTrashed()->with('pcProfile')->findOrFail($id);
        return view('backend.chrome.show', compact('chrome'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChromeProfile $chrome)
    {
        $chrome->load('pcProfile');
        return view('backend.chrome.edit', compact('chrome'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChromeProfile $chrome)
    {
        $validated = $request->validate([
            'profile_directory' => 'required|string|max:255',
            'user_agent' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'active', 'inactive', 'remove'])] 
        ]);

        // Update status normally. Soft delete is NOT triggered here anymore.
        $chrome->update($validated);
        
        return redirect()
            ->route('admin.chrome.index')
            ->with('success', 'Chrome profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * This will now set status to 'remove' AND soft delete.
     */
    public function destroy(ChromeProfile $chrome)
    {
        // Update status to remove before soft deleting
        $chrome->status = 'remove';
        $chrome->save();
        $chrome->delete(); // Soft delete
        
        return redirect()
            ->route('admin.chrome.index')
            ->with('success', 'Chrome profile removed successfully.');
    }

    /**
     * Get Chrome profiles for a specific PC Profile.
     */
    public function getByPcProfile($pcProfileId)
    {
        $profiles = ChromeProfile::where('pc_profile_id', $pcProfileId)
            ->where('status', 'active')
            ->select('id', 'profile_directory')
            ->get();
            
        return response()->json($profiles);
    }

    // Add other methods (create, store, show, edit, update, destroy) later if needed
}
