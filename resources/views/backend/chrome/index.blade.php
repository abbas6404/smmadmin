@extends('backend.layouts.master')

@section('title', 'Chrome Profiles')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Chrome Profiles Management</h1>

    <!-- Status Counts Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Profiles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Profiles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Inactive Profiles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactiveCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Removed Profiles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $removedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profiles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Chrome Profile List</h6>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body border-bottom">
                <form action="{{ route('admin.chrome.index') }}" method="GET" class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                               placeholder="Search by directory or user agent...">
                    </div>

                    <!-- PC Profile Filter -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">PC Profile</label>
                        <select class="form-control" name="pc_profile_id">
                            <option value="">All PC Profiles</option>
                            @foreach($pcProfiles as $pcProfile)
                                <option value="{{ $pcProfile->id }}" {{ request('pc_profile_id') == $pcProfile->id ? 'selected' : '' }}>
                                    {{ $pcProfile->pc_name }}
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
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="remove" {{ request('status') === 'remove' ? 'selected' : '' }}>Remove</option>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-md-12 mb-3 d-flex justify-content-end">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.chrome.index') }}" class="btn btn-secondary">
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
                            <th>Profile Directory</th>
                            <th>User Agent</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($profiles as $profile)
                        <tr>
                            <td>{{ $profile->id }}</td>
                            <td>{{ $profile->pcProfile->pc_name ?? 'N/A' }}</td>
                            <td>{{ $profile->profile_directory }}</td>
                            <td><span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $profile->user_agent }}">{{ $profile->user_agent }}</span></td>
                            <td>
                                <span class="badge bg-{{
                                    $profile->status == 'active' ? 'success' :
                                    ($profile->status == 'pending' ? 'warning' :
                                    ($profile->status == 'inactive' ? 'secondary' :
                                    ($profile->status == 'remove' ? 'danger' : 'dark')))
                                }}">
                                    {{ ucfirst($profile->status) }}
                                </span>
                            </td>
                            <td>{{ $profile->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.chrome.show', $profile->id) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.chrome.edit', $profile->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.chrome.destroy', $profile->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" 
                                                onclick="return confirm('Are you sure you want to remove this profile? Note: This is a soft delete.');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No Chrome profiles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    Showing {{ $profiles->firstItem() ?? 0 }} to {{ $profiles->lastItem() ?? 0 }} of {{ $profiles->total() }} results
                </div>
                <div>
                    @if ($profiles->hasPages())
                        <nav>
                            <ul class="pagination mb-0">
                                {{-- Previous Page Link --}}
                                @if ($profiles->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-hidden="true">&laquo; Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $profiles->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($profiles->getUrlRange(1, $profiles->lastPage()) as $page => $url)
                                    @if ($page == $profiles->currentPage())
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
                                @if ($profiles->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $profiles->nextPageUrl() }}" rel="next">Next &raquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-hidden="true">Next &raquo;</span>
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

<style>
.pagination {
    margin: 0;
    display: flex;
    padding-left: 0;
    list-style: none;
    border-radius: 0.35rem;
}

.page-link {
    position: relative;
    display: block;
    padding: 0.5rem 0.75rem;
    margin-left: -1px;
    line-height: 1.25;
    color: #4e73df;
    background-color: #fff;
    border: 1px solid #dddfeb;
}

.page-item:first-child .page-link {
    margin-left: 0;
    border-top-left-radius: 0.35rem;
    border-bottom-left-radius: 0.35rem;
}

.page-item:last-child .page-link {
    border-top-right-radius: 0.35rem;
    border-bottom-right-radius: 0.35rem;
}

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #4e73df;
    border-color: #4e73df;
}

.page-item.disabled .page-link {
    color: #858796;
    pointer-events: none;
    cursor: auto;
    background-color: #fff;
    border-color: #dddfeb;
}

.page-link:hover {
    z-index: 2;
    color: #224abe;
    text-decoration: none;
    background-color: #eaecf4;
    border-color: #dddfeb;
}

.page-link:focus {
    z-index: 3;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
</style>
@endsection 