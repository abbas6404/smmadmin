@extends('backend.layouts.master')

@section('title', 'PC Profiles')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">PC Profiles Management</h1>

    <!-- Status Counts Cards -->
    <div class="row">
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Blocked Profiles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $blockedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Deleted Profiles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $deletedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trash fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">PC Profile List</h6>
            <div>
                <a href="{{ route('admin.pc-profiles.create') }}" class="btn btn-success btn-sm me-1"><i class="fas fa-plus"></i> Add New</a>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body border-bottom">
                <form action="{{ route('admin.pc-profiles.index') }}" method="GET" class="row g-3">
                    <!-- Search -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                               placeholder="Search by PC name...">
                    </div>
                    <!-- Status Filter -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All (Non-Deleted)</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                            <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    <!-- Filter Actions -->
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.pc-profiles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Profiles Table -->
        <div class="card-body">
             @if($errors->has('delete'))
                <div class="alert alert-danger">{{ $errors->first('delete') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>PC Name</th>
                            <th>Hostname</th>
                            <th>OS Version</th>
                            <th>Max Profile Limit</th>
                            <th>Max Order Limit</th>
                            <th>Min Order Limit</th>
                            <th>Status</th>
                            <th>Last Verified At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($profiles as $profile)
                        <tr class="{{ $profile->trashed() ? 'table-danger' : '' }}">
                            <td>{{ $profile->id }}</td>
                            <td>{{ $profile->pc_name }}</td>
                            <td>{{ $profile->hostname }}</td>
                            <td>{{ $profile->os_version }}</td>
                            <td>{{ $profile->max_profile_limit }}</td>
                            <td>{{ $profile->max_order_limit }}</td>
                            <td>{{ $profile->min_order_limit }}</td>
                            <td>
                                <span class="badge bg-{{
                                    $profile->status == 'active' ? 'success' :
                                    ($profile->status == 'inactive' ? 'secondary' :
                                    ($profile->status == 'blocked' ? 'warning' : 
                                    ($profile->status == 'deleted' ? 'danger' : 'dark')))
                                }}">
                                    {{ ucfirst($profile->status) }}
                                </span>
                            </td>
                            <td>{{ $profile->last_verified_at ? $profile->last_verified_at->format('Y-m-d H:i') : 'Never' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.pc-profiles.show', $profile->id) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.pc-profiles.edit', $profile->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @if(!$profile->trashed())
                                    <form action="{{ route('admin.pc-profiles.destroy', $profile->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Soft Delete" 
                                                onclick="return confirm('Are you sure you want to soft-delete this PC Profile? Associated accounts will remain but may become inaccessible.');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.pc-profiles.update', $profile->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" class="btn btn-sm btn-success" title="Restore Profile">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No PC profiles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $profiles->appends(request()->query())->links() }} 
            </div>
        </div>
    </div>

</div>
@endsection 