@extends('backend.layouts.master')

@section('title', 'Facebook Quick ID Check')

@push('styles')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Success Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i>
        <strong>Success!</strong> {!! nl2br(e(session('success'))) !!}
        @if(session('created_count'))
        <div class="mt-2">
            <i class="fas fa-info-circle"></i> Created {{ session('created_count') }} new account(s).
        </div>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Warning Messages -->
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-1"></i>
        <strong>Partial Success</strong>
        <div class="mt-2">
            <i class="fas fa-check-circle text-success"></i> Successfully created: {{ session('created_count') }} account(s)
            @if(session('error_count'))
            <br>
            <i class="fas fa-exclamation-circle text-danger"></i> Failed: {{ session('error_count') }} account(s)
            @endif
        </div>
        <div class="mt-2">
            <strong>Details:</strong><br>
            {!! nl2br(e(session('warning'))) !!}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Error Messages -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-1"></i>
        <strong>Error!</strong> {!! nl2br(e(session('error'))) !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Facebook Quick ID Check</h1>
        <div class="d-flex">
            @if($activeCount > 0)
            <form action="{{ route('admin.facebook-quick-check.transfer-all-active') }}" method="POST" class="me-2">
                @csrf
                <button type="submit" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" 
                   onclick="return confirm('Are you sure you want to transfer all {{ $activeCount }} active accounts to Facebook accounts?')">
                    <i class="fas fa-exchange-alt fa-sm text-white-50"></i> Transfer All Active ({{ $activeCount }})
                </button>
            </form>
            @endif
            <a href="{{ route('admin.facebook-quick-check.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Account
            </a>
        </div>
    </div>

    <!-- Status Cards Row -->
    <div class="row mb-4">
        <!-- Total Accounts -->
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $processingCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-spin fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Accounts -->
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

        <!-- In Use Accounts -->
        <div class="col">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">In Use Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inUseCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blocked Accounts -->
        <div class="col">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Blocked Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $blockedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Facebook Quick ID Check</h6>
            <div>
                <div class="btn-group me-2">
                    <a href="{{ route('admin.facebook-quick-check.index') }}" class="btn btn-sm {{ !request('trashed') ? 'btn-primary' : 'btn-outline-primary' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.facebook-quick-check.index', ['trashed' => 'true']) }}" class="btn btn-sm {{ request('trashed') === 'true' ? 'btn-danger' : 'btn-outline-danger' }}">
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
                <form action="{{ route('admin.facebook-quick-check.index') }}" method="GET" class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                               placeholder="Search by email or notes...">
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>In Use</option>
                            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('admin.facebook-quick-check.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Last Checked</th>
                            <th>Check Count</th>
                            <th>Check Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr>
                            <td>{{ $account->id }}</td>
                            <td>{{ $account->email }}</td>
                            <td>
                                @if($account->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($account->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($account->status == 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @elseif($account->status == 'in_use')
                                    <span class="badge bg-secondary">In Use</span>
                                @elseif($account->status == 'blocked')
                                    <span class="badge bg-danger">Blocked</span>
                                @endif
                            </td>
                            <td>
                                {{ $account->last_checked_at ? $account->last_checked_at->diffForHumans() : 'Never' }}
                            </td>
                            <td>{{ $account->check_count }}</td>
                            <td>{{ $account->check_result ?? 'No result' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.facebook-quick-check.show', $account->id) }}">
                                            <i class="fas fa-eye fa-sm fa-fw mr-2 text-gray-400"></i> View
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.facebook-quick-check.edit', $account->id) }}">
                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i> Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('admin.facebook-quick-check.toggle-valid', $account->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-{{ $account->status == 'active' ? 'times' : 'check' }} fa-sm fa-fw mr-2 text-gray-400"></i>
                                                Mark as {{ $account->status == 'active' ? 'Pending' : 'Active' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.facebook-quick-check.quick-check', $account->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sync fa-sm fa-fw mr-2 text-gray-400"></i> Quick Check
                                            </button>
                                        </form>
                                        @if($account->status == 'active')
                                        <form action="{{ route('admin.facebook-quick-check.transfer', $account->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to transfer this account to Facebook accounts?')">
                                                <i class="fas fa-exchange-alt fa-sm fa-fw mr-2 text-gray-400"></i> Transfer to FB Account
                                            </button>
                                        </form>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        @if(!$account->trashed())
                                            <form action="{{ route('admin.facebook-quick-check.destroy', $account->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to move this account to trash?')">
                                                    <i class="fas fa-trash fa-sm fa-fw"></i> Trash
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.facebook-quick-check.restore', $account->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="fas fa-trash-restore fa-sm fa-fw"></i> Restore
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.facebook-quick-check.force-delete', $account->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to permanently delete this account? This action cannot be undone.')">
                                                    <i class="fas fa-trash-alt fa-sm fa-fw"></i> Delete Permanently
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No accounts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $accounts->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
});
</script>
@endpush 