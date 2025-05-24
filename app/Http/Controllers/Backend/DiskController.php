<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Disk;
use App\Models\PcProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $disks = Disk::with('pcProfile')
            ->orderBy('free_space', 'asc')
            ->paginate(30);

        $stats = [
            'total' => Disk::count(),
            'healthy' => Disk::where('health_percentage', '>=', 90)->count(),
            'warning' => Disk::whereBetween('health_percentage', [70, 89])->count(),
            'min_free_space' => Disk::min('free_space'),
            'total_free_space' => Disk::sum('free_space'),
        ];

        return view('backend.disks.index', compact('disks', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pcProfiles = PcProfile::where('status', 'active')->get();
        return view('backend.disks.create', compact('pcProfiles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pc_profile_id' => 'required|exists:pc_profiles,id',
            'drive_letter' => 'required|string|size:1|regex:/^[A-Z]$/',
            'file_system' => 'required|string|in:NTFS,FAT32,exFAT',
            'total_size' => 'required|integer|min:1',
            'free_space' => 'required|integer|min:0|lte:total_size',
            'used_space' => 'required|integer|min:0|lte:total_size',
            'health_percentage' => 'required|numeric|min:0|max:100',
            'read_speed' => 'required|integer|min:0',
            'write_speed' => 'required|integer|min:0',
        ]);

        // Check if drive letter is already used for this PC profile
        $existingDisk = Disk::where('pc_profile_id', $request->pc_profile_id)
            ->where('drive_letter', $request->drive_letter)
            ->first();

        if ($existingDisk) {
            return back()->withErrors(['drive_letter' => 'This drive letter is already in use for this PC profile.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $disk = Disk::create($request->all());

            DB::commit();

            return redirect()->route('admin.disks.index')
                ->with('success', 'Disk added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add disk. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Disk $disk)
    {
        $disk->load('pcProfile');
        return view('backend.disks.show', compact('disk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Disk $disk)
    {
        $pcProfiles = PcProfile::where('status', 'active')->get();
        return view('backend.disks.edit', compact('disk', 'pcProfiles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disk $disk)
    {
        $request->validate([
            'pc_profile_id' => 'required|exists:pc_profiles,id',
            'drive_letter' => 'required|string|size:1|regex:/^[A-Z]$/',
            'file_system' => 'required|string|in:NTFS,FAT32,exFAT',
            'total_size' => 'required|integer|min:1',
            'free_space' => 'required|integer|min:0|lte:total_size',
            'used_space' => 'required|integer|min:0|lte:total_size',
            'health_percentage' => 'required|numeric|min:0|max:100',
            'read_speed' => 'required|integer|min:0',
            'write_speed' => 'required|integer|min:0',
        ]);

        // Check if drive letter is already used for this PC profile (excluding current disk)
        $existingDisk = Disk::where('pc_profile_id', $request->pc_profile_id)
            ->where('drive_letter', $request->drive_letter)
            ->where('id', '!=', $disk->id)
            ->first();

        if ($existingDisk) {
            return back()->withErrors(['drive_letter' => 'This drive letter is already in use for this PC profile.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $disk->update($request->all());

            DB::commit();

            return redirect()->route('admin.disks.index')
                ->with('success', 'Disk updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update disk. Please try again.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Disk $disk)
    {
        try {
            DB::beginTransaction();

            $disk->delete();

            DB::commit();

            return redirect()->route('admin.disks.index')
                ->with('success', 'Disk deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete disk. Please try again.']);
        }
    }
} 