@extends('backend.layouts.master')

@section('title', 'Submission Batch Details')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Submission Batch Details</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Batch #{{ $submissionBatch->id }} - {{ $submissionBatch->name }}</h6>
            <div>
                <a href="{{ route('admin.submission-batch.report', $submissionBatch->id) }}" class="btn btn-info btn-sm me-1" target="_blank">
                    <i class="fas fa-file-pdf"></i> Generate Report
                </a>
                <a href="{{ route('admin.submission-batch.edit', $submissionBatch->id) }}" class="btn btn-warning btn-sm me-1">
                    <i class="fas fa-edit"></i> Edit Batch
                </a>
                <a href="{{ route('admin.submission-batch.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 20%;">ID</th>
                        <td>{{ $submissionBatch->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $submissionBatch->name }}</td>
                    </tr>
                    <tr>
                        <th>Submission Type</th>
                        <td>{{ ucfirst($submissionBatch->submission_type) }}</td>
                    </tr>
                    <tr>
                        <th>Total Submissions</th>
                        <td>{{ number_format($submissionBatch->total_submissions) }}</td>
                    </tr>
                    <tr>
                        <th>Accurate Submissions</th>
                        <td>{{ number_format($submissionBatch->accurate_submissions) }}</td>
                    </tr>
                    <tr>
                        <th>Incorrect Submissions</th>
                        <td>{{ number_format($submissionBatch->incorrect_submissions) }}</td>
                    </tr>
                    <tr>
                        <th>Approved</th>
                        <td>
                            @if($submissionBatch->approved)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $submissionBatch->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $submissionBatch->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>
                            @if($submissionBatch->notes)
                                <pre class="mb-0" style="white-space: pre-wrap;">{{ $submissionBatch->notes }}</pre>
                            @else
                                <span class="text-muted">No notes available</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Associated Accounts -->
            @if($submissionBatch->submission_type === 'facebook')
                <h5 class="mt-4 mb-3">Associated Facebook Accounts</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Have Page</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissionBatch->facebookAccounts as $account)
                                <tr>
                                    <td>{{ $account->id }}</td>
                                    <td>{{ $account->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $account->status == 'active' ? 'success' :
                                            ($account->status == 'pending' ? 'warning' :
                                            ($account->status == 'inactive' ? 'secondary' :
                                            ($account->status == 'processing' ? 'info' : 'dark')))
                                        }}">
                                            {{ ucfirst($account->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($account->have_page)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.facebook.show', $account->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No Facebook accounts in this batch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            @if($submissionBatch->submission_type === 'gmail')
                <h5 class="mt-4 mb-3">Associated Gmail Accounts</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissionBatch->gmailAccounts as $account)
                                <tr>
                                    <td>{{ $account->id }}</td>
                                    <td>{{ $account->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $account->status == 'active' ? 'success' :
                                            ($account->status == 'pending' ? 'warning' :
                                            ($account->status == 'inactive' ? 'secondary' :
                                            ($account->status == 'processing' ? 'info' : 'dark')))
                                        }}">
                                            {{ ucfirst($account->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.gmail.show', $account->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No Gmail accounts in this batch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 