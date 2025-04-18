@extends('backend.layouts.master')

@section('title', 'Facebook Accounts Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Facebook Accounts Management</h1>
        <a href="{{ route('admin.facebook.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Account
        </a>
    </div>

    <!-- Status Cards Row -->
    <div class="row mb-4">
        <!-- Total Facebook Accounts -->
        <div class="col">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-facebook fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Accounts -->
        <div class="col">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Processing Accounts -->
        <div class="col">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Processing Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $processingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Accounts -->
        <div class="col">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Accounts -->
        <div class="col">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Inactive Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactiveCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Facebook Account List</h6>
            <div>
                <div class="btn-group me-2">
                    <a href="{{ route('admin.facebook.index') }}" class="btn btn-sm {{ !request('trashed') ? 'btn-primary' : 'btn-outline-primary' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.facebook.index', ['trashed' => 'true']) }}" class="btn btn-sm {{ request('trashed') === 'true' ? 'btn-danger' : 'btn-outline-danger' }}">
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
                <form action="{{ route('admin.facebook.index') }}" method="GET" class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                               placeholder="Search by email/UID or note...">
                    </div>

                    <!-- PC Profile Filter -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">PC Profile</label>
                        <select class="form-control" name="pc_profile_id">
                            <option value="">All PC Profiles</option>
                            @foreach($pcProfiles as $profile)
                                <option value="{{ $profile->id }}" {{ request('pc_profile_id') == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->pc_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            <a href="{{ route('admin.facebook.index', request('trashed') ? ['trashed' => request('trashed')] : []) }}" class="btn btn-secondary">
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
                            <th>PC Profile</th>
                            <th>Chrome ID</th>
                            <th>Email/UID</th>
                            <th>Batch</th>
                            <th>Have Use</th>
                            <th>Have Page</th>
                            <th>Have Post</th>
                            <th>Total Count</th>
                            <th>Status</th>
                            <th>Note</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $account)
                        <tr class="{{ $account->trashed() ? 'table-danger' : '' }}">
                            <td>{{ $account->id }}</td>
                            <td>{{ $account->pcProfile->pc_name ?? 'N/A' }}</td>
                            <td>{{ $account->chrome_profile_id }}</td>
                            <td>{{ $account->email }}</td>
                            <td>
                                @if($account->submissionBatch)
                                    <a href="{{ route('admin.submission-batch.show', $account->submission_batch_id) }}" title="View Batch">
                                        {{ $account->submissionBatch->name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($account->have_use)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td>
                                @if($account->have_page)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td>
                                @if($account->have_post)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td>{{ number_format($account->total_count) }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $account->status == 'active' ? 'success' :
                                    ($account->status == 'pending' ? 'warning' :
                                    ($account->status == 'inactive' ? 'secondary' :
                                    ($account->status == 'processing' ? 'info' :
                                    ($account->status == 'remove' ? 'danger' : 'dark'))))
                                }}">
                                    {{ ucfirst($account->status) }}
                                </span>
                            </td>
                            <td>
                                @if($account->note)
                                    <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $account->note }}">
                                        {{ $account->note }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($account->trashed())
                                        <form action="{{ route('admin.facebook.restore', $account->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                                <i class="fas fa-trash-restore"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.facebook.force-delete', $account->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Permanently" 
                                                    onclick="return confirm('Are you sure you want to permanently delete this account? This action cannot be undone.');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.facebook.show', $account->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.facebook.edit', $account->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.facebook.destroy', $account->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Move to Trash" 
                                                    onclick="return confirm('Are you sure you want to move this account to trash?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">No Facebook accounts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $accounts->firstItem() ?? 0 }} to {{ $accounts->lastItem() ?? 0 }} of {{ $accounts->total() }} results
                </div>
                <div>
                    @if ($accounts->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Previous Page Link --}}
                                @if ($accounts->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span> Previous
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $accounts->previousPageUrl() }}" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span> Previous
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($accounts->getUrlRange(1, $accounts->lastPage()) as $page => $url)
                                    @if ($page == $accounts->currentPage())
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
                                @if ($accounts->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $accounts->nextPageUrl() }}" aria-label="Next">
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