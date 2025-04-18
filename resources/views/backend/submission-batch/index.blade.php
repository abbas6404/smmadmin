@extends('backend.layouts.master')

@section('title', 'Submission Batches')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Submission Batches</h1>
    </div>

    <!-- Status Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Batches</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalCount) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Batches</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeCount) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Trashed Batches</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($trashedCount) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trash-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Submission Batch List</h6>
            <div>
                <div class="btn-group me-2">
                    <a href="{{ route('admin.submission-batch.index') }}" class="btn btn-sm {{ !request('trashed') ? 'btn-primary' : 'btn-outline-primary' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.submission-batch.index', ['trashed' => 'true']) }}" class="btn btn-sm {{ request('trashed') === 'true' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Trashed
                    </a>
                </div>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body border-bottom">
                <form action="{{ route('admin.submission-batch.index') }}" method="GET" class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                               placeholder="Search by name...">
                    </div>

                    <!-- Type Filter -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-control" name="type">
                            <option value="">All Types</option>
                            <option value="facebook" {{ request('type') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="gmail" {{ request('type') === 'gmail' ? 'selected' : '' }}>Gmail</option>
                        </select>
                    </div>

                    <!-- Approved Filter -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Approved</label>
                        <select class="form-control" name="approved">
                            <option value="">All</option>
                            <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <!-- Keep trashed parameter if it exists -->
                    @if(request('trashed'))
                        <input type="hidden" name="trashed" value="{{ request('trashed') }}">
                    @endif

                    <!-- Filter Actions -->
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.submission-batch.index', request('trashed') ? ['trashed' => request('trashed')] : []) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Accurate</th>
                            <th>Incorrect</th>
                            <th>Approved</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batches as $batch)
                        <tr class="{{ $batch->trashed() ? 'table-danger' : '' }}">
                            <td>{{ $batch->id }}</td>
                            <td>{{ $batch->name }}</td>
                            <td>{{ ucfirst($batch->submission_type) }}</td>
                            <td>{{ number_format($batch->total_submissions) }}</td>
                            <td>{{ number_format($batch->accurate_submissions) }}</td>
                            <td>{{ number_format($batch->incorrect_submissions) }}</td>
                            <td>
                                @if($batch->approved)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td>{{ $batch->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($batch->trashed())
                                        <form action="{{ route('admin.submission-batch.restore', $batch->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                                <i class="fas fa-trash-restore"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.submission-batch.force-delete', $batch->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Permanently" 
                                                    onclick="return confirm('Are you sure you want to permanently delete this batch? This action cannot be undone.');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.submission-batch.show', $batch->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.submission-batch.edit', $batch->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.submission-batch.destroy', $batch->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Move to Trash" 
                                                    onclick="return confirm('Are you sure you want to move this batch to trash?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No submission batches found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $batches->firstItem() ?? 0 }} to {{ $batches->lastItem() ?? 0 }} of {{ $batches->total() }} results
                </div>
                <div>
                    @if ($batches->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Previous Page Link --}}
                                @if ($batches->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span> Previous
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $batches->previousPageUrl() }}" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span> Previous
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($batches->getUrlRange(1, $batches->lastPage()) as $page => $url)
                                    @if ($page == $batches->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($batches->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $batches->nextPageUrl() }}" aria-label="Next">
                                            Next <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Next">
                                            Next <span aria-hidden="true">&raquo;</span>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 