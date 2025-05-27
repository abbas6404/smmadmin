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
                            <th>Auto Shutdown</th>
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
                                <form action="{{ route('admin.pc-profiles.update', $profile->id) }}" method="POST" class="auto-shutdown-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="auto_shutdown" value="{{ $profile->auto_shutdown ? '0' : '1' }}">
                                    <button type="submit" class="btn btn-sm {{ $profile->auto_shutdown ? 'btn-success' : 'btn-secondary' }}" title="{{ $profile->auto_shutdown ? 'Auto shutdown enabled' : 'Auto shutdown disabled' }}">
                                        <i class="fas fa-power-off"></i>
                                        {{ $profile->auto_shutdown ? 'On' : 'Off' }}
                                    </button>
                                </form>
                            </td>
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
                            <td colspan="11" class="text-center">No PC profiles found.</td>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Handle auto shutdown toggle forms with AJAX
    $('.auto-shutdown-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const button = form.find('button');
        const isCurrentlyEnabled = button.hasClass('btn-success');
        
        // Show loading state
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Toggle button appearance
                    if (isCurrentlyEnabled) {
                        button.removeClass('btn-success').addClass('btn-secondary');
                        button.html('<i class="fas fa-power-off"></i> Off');
                        button.attr('title', 'Auto shutdown disabled');
                    } else {
                        button.removeClass('btn-secondary').addClass('btn-success');
                        button.html('<i class="fas fa-power-off"></i> On');
                        button.attr('title', 'Auto shutdown enabled');
                    }
                    
                    // Toggle the hidden input value for next submission
                    const currentValue = form.find('input[name="auto_shutdown"]').val();
                    form.find('input[name="auto_shutdown"]').val(currentValue === '1' ? '0' : '1');
                    
                    // Show success message
                    toastr.success('Auto shutdown setting updated successfully');
                } else {
                    toastr.error('Failed to update auto shutdown setting');
                    
                    // Reset button to original state
                    button.html('<i class="fas fa-power-off"></i> ' + (isCurrentlyEnabled ? 'On' : 'Off'));
                }
            },
            error: function() {
                toastr.error('An error occurred while updating auto shutdown setting');
                
                // Reset button to original state
                button.html('<i class="fas fa-power-off"></i> ' + (isCurrentlyEnabled ? 'On' : 'Off'));
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush 