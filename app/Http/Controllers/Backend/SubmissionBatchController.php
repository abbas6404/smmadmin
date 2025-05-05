<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubmissionBatch;
use Illuminate\Support\Facades\DB;
use App\Models\FacebookAccount;
use App\Models\GmailAccount;
use Dompdf\Dompdf;
use Dompdf\Options;

class SubmissionBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SubmissionBatch::query();

        // Handle trashed items
        if ($request->has('trashed') && $request->trashed === 'true') {
            $query->onlyTrashed();
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('submission_type', $request->type);
        }

        // Apply approved filter
        if ($request->filled('approved')) {
            $query->where('approved', $request->approved);
        }

        // Get counts for status cards
        $totalCount = SubmissionBatch::withTrashed()->count();
        $activeCount = SubmissionBatch::count();
        $trashedCount = SubmissionBatch::onlyTrashed()->count();

        // Get paginated results
        $batches = $query->latest()->paginate(10);

        return view('backend.submission-batch.index', compact(
            'batches',
            'totalCount',
            'activeCount',
            'trashedCount'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(SubmissionBatch $submissionBatch)
    {
        // Load relationships based on submission type
        if ($submissionBatch->submission_type === 'facebook') {
            $submissionBatch->load('facebookAccounts');
        } elseif ($submissionBatch->submission_type === 'gmail') {
            $submissionBatch->load('gmailAccounts');
        }

        return view('backend.submission-batch.show', compact('submissionBatch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubmissionBatch $submissionBatch)
    {
        return view('backend.submission-batch.edit', compact('submissionBatch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubmissionBatch $submissionBatch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'approved' => 'boolean',
            'accurate_submissions' => 'required|integer|min:0',
            'incorrect_submissions' => 'required|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        // Convert approved checkbox value
        $validated['approved'] = isset($validated['approved']);

        $submissionBatch->update($validated);

        return redirect()
            ->route('admin.submission-batch.show', $submissionBatch)
            ->with('success', 'Submission batch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubmissionBatch $submissionBatch)
    {
        try {
            DB::beginTransaction();
            
            // Soft delete the batch
            $submissionBatch->delete();
            
            DB::commit();
            return redirect()
                ->route('admin.submission-batch.index')
                ->with('success', 'Submission batch moved to trash successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to move batch to trash: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted batch.
     */
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            
            $batch = SubmissionBatch::withTrashed()->findOrFail($id);
            $batch->restore();
            
            DB::commit();
            return redirect()
                ->route('admin.submission-batch.index')
                ->with('success', 'Submission batch restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to restore batch: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a batch.
     */
    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();
            
            $batch = SubmissionBatch::withTrashed()->findOrFail($id);
            $batch->forceDelete();
            
            DB::commit();
            return redirect()
                ->route('admin.submission-batch.index', ['trashed' => 'true'])
                ->with('success', 'Submission batch permanently deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to permanently delete batch: ' . $e->getMessage());
        }
    }

    /**
     * Generate a report/invoice for the batch
     */
    public function generateReport(SubmissionBatch $submissionBatch)
    {
        try {
            DB::beginTransaction();

            // Load relationships based on submission type
            if ($submissionBatch->submission_type === 'facebook') {
                $submissionBatch->load('facebookAccounts', 'user');
                // Calculate accurate submissions (active accounts)
                $accurateSubmissions = $submissionBatch->facebookAccounts->where('status', 'active')->count();
                // Total submissions is all accounts
                $totalSubmissions = $submissionBatch->facebookAccounts->count();
            } elseif ($submissionBatch->submission_type === 'gmail') {
                $submissionBatch->load('gmailAccounts', 'user');
                // Calculate accurate submissions (active accounts)
                $accurateSubmissions = $submissionBatch->gmailAccounts->where('status', 'active')->count();
                // Total submissions is all accounts
                $totalSubmissions = $submissionBatch->gmailAccounts->count();
            } elseif ($submissionBatch->submission_type === 'facebook_and_gmail') {
                // Load both account types
                $submissionBatch->load('facebookAccounts', 'gmailAccounts', 'user');
                
                // Calculate Facebook stats
                $facebookAccurateSubmissions = $submissionBatch->facebookAccounts->where('status', 'active')->count();
                $facebookTotalSubmissions = $submissionBatch->facebookAccounts->count();
                
                // Calculate Gmail stats
                $gmailAccurateSubmissions = $submissionBatch->gmailAccounts->where('status', 'active')->count();
                $gmailTotalSubmissions = $submissionBatch->gmailAccounts->count();
                
                // Combined stats
                $accurateSubmissions = $facebookAccurateSubmissions + $gmailAccurateSubmissions;
                $totalSubmissions = $facebookTotalSubmissions + $gmailTotalSubmissions;
            }

            // Update the batch with new calculations
            $submissionBatch->update([
                'total_submissions' => $totalSubmissions,
                'accurate_submissions' => $accurateSubmissions,
                'incorrect_submissions' => $totalSubmissions - $accurateSubmissions
            ]);

            DB::commit();

            // Initialize Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            
            // Load the view content
            $html = view('backend.submission-batch.report', compact('submissionBatch'))->render();
            
            // Load HTML to Dompdf
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            
            // Render the PDF
            $dompdf->render();
            
            // Generate filename
            $filename = 'batch_' . $submissionBatch->id . '_report_' . date('Y_m_d') . '.pdf';
            
            // Download the PDF
            return $dompdf->stream($filename);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Update the counts for the batch
     */
    public function updateCount(SubmissionBatch $submissionBatch)
    {
        try {
            DB::beginTransaction();

            // Calculate counts based on submission type
            if ($submissionBatch->submission_type === 'facebook') {
                $totalSubmissions = $submissionBatch->facebookAccounts->count();
                $accurateSubmissions = $submissionBatch->facebookAccounts->where('status', 'active')->count();
                $incorrectSubmissions = $submissionBatch->facebookAccounts->whereIn('status', ['inactive', 'blocked', 'logout', 'remove'])->count();
            } elseif ($submissionBatch->submission_type === 'gmail') {
                $totalSubmissions = $submissionBatch->gmailAccounts->count();
                $accurateSubmissions = $submissionBatch->gmailAccounts->where('status', 'active')->count();
                $incorrectSubmissions = $submissionBatch->gmailAccounts->whereIn('status', ['inactive', 'blocked', 'logout', 'remove'])->count();
            } elseif ($submissionBatch->submission_type === 'facebook_and_gmail') {
                // Calculate Facebook stats
                $facebookTotal = $submissionBatch->facebookAccounts->count();
                $facebookAccurate = $submissionBatch->facebookAccounts->where('status', 'active')->count();
                $facebookIncorrect = $submissionBatch->facebookAccounts->whereIn('status', ['inactive', 'blocked', 'logout', 'remove'])->count();
                
                // Calculate Gmail stats
                $gmailTotal = $submissionBatch->gmailAccounts->count();
                $gmailAccurate = $submissionBatch->gmailAccounts->where('status', 'active')->count();
                $gmailIncorrect = $submissionBatch->gmailAccounts->whereIn('status', ['inactive', 'blocked', 'logout', 'remove'])->count();
                
                // Combined stats
                $totalSubmissions = $facebookTotal + $gmailTotal;
                $accurateSubmissions = $facebookAccurate + $gmailAccurate;
                $incorrectSubmissions = $facebookIncorrect + $gmailIncorrect;
            }

            // Update the batch with new calculations
            $submissionBatch->update([
                'total_submissions' => $totalSubmissions,
                'accurate_submissions' => $accurateSubmissions,
                'incorrect_submissions' => $incorrectSubmissions
            ]);

            DB::commit();

            return redirect()
                ->route('admin.submission-batch.show', $submissionBatch)
                ->with('success', 'Submission batch counts updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to update counts: ' . $e->getMessage());
        }
    }
} 