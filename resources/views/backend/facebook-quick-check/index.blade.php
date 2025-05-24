@extends('backend.layouts.master')

@section('title', 'Facebook Quick ID Check')

@push('styles')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
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
    
    .bulk-actions-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 999;
    }
    
    .bulk-actions-fab .btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bulk-actions-fab .btn i {
        font-size: 24px;
    }
    
    .bulk-actions-counter {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #e74a3b;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
    
    .bulk-actions-panel {
        display: none;
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 300px;
        z-index: 998;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        padding: 15px;
    }
    
    .highlight-row {
        background-color: rgba(78, 115, 223, 0.1) !important;
    }
</style>
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
            <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#transferAllModal">
                <i class="fas fa-exchange-alt fa-sm text-white-50"></i> Transfer All Active ({{ $activeCount }})
            </button>
            @endif
            <a href="{{ route('admin.facebook-quick-check.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add New Account
            </a>
        </div>
    </div>

    <!-- Transfer All Modal -->
    <div class="modal fade" id="transferAllModal" tabindex="-1" aria-labelledby="transferAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferAllModalLabel">Transfer Active Accounts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.facebook-quick-check.transfer-all-active') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="pc_profile_id" class="form-label required">Select PC</label>
                            <select class="form-select" id="pc_profile_id" name="pc_profile_id" required>
                                <option value="">-- Select PC --</option>
                                @foreach($pcProfiles ?? [] as $pc)
                                <option value="{{ $pc->id }}">{{ $pc->pc_name }} ({{ $pc->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="account_count" class="form-label required">Number of Accounts to Transfer</label>
                            <input type="number" class="form-control" id="account_count" name="account_count" 
                                   min="1" max="{{ $activeCount }}" value="{{ min(10, $activeCount) }}" required>
                            <div class="form-text">Maximum available: {{ $activeCount }} accounts</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Selected accounts will be transferred to the chosen PC and marked as "in_use". New accounts will be created with "pending" status in the Facebook accounts system.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-exchange-alt me-1"></i> Transfer Accounts
                        </button>
                    </div>
                </form>
            </div>
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
            <!-- Bulk Actions -->
            <form id="bulk-action-form" action="{{ route('admin.facebook-quick-check-bulk-update') }}" method="POST" class="mb-4">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">Bulk Status Update</label>
                        <select class="form-control" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="active">Active</option>
                            <option value="in_use">In Use</option>
                            <option value="blocked">Blocked</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary" id="bulk-update-btn" disabled>
                            <i class="fas fa-save"></i> Update Selected Accounts
                        </button>
                    </div>
                    <div class="col-md-4 mb-3 text-end">
                        <div class="form-check d-inline-block">
                            <input class="form-check-input" type="checkbox" id="select-all">
                            <label class="form-check-label" for="select-all">
                                Select All
                            </label>
                        </div>
                        <span class="ms-2" id="selected-count">(0 selected)</span>
                    </div>
                </div>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="40px">
                                <div class="text-center">#</div>
                            </th>
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
                        <tr class="{{ $account->trashed() ? 'table-danger' : '' }}">
                            <td class="text-center">
                                @if(!$account->trashed())
                                <div class="form-check">
                                    <input class="form-check-input account-checkbox" type="checkbox" name="account_ids[]" value="{{ $account->id }}" id="account-{{ $account->id }}">
                                </div>
                                @endif
                            </td>
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
            </form>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
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
                                        <span class="page-link" aria-hidden="true">&laquo; Previous</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $accounts->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
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
                                        <a class="page-link" href="{{ $accounts->nextPageUrl() }}" rel="next">Next &raquo;</a>
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
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // Bulk selection functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const accountCheckboxes = document.querySelectorAll('.account-checkbox');
    const bulkUpdateBtn = document.getElementById('bulk-update-btn');
    const selectedCountDisplay = document.getElementById('selected-count');
    const bulkActionForm = document.getElementById('bulk-action-form');
    
    // Select all checkbox functionality
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        
        accountCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
            const row = checkbox.closest('tr');
            if (row) {
                if (isChecked) {
                    row.classList.add('highlight-row');
                } else {
                    row.classList.remove('highlight-row');
                }
            }
        });
        
        updateSelectedCount();
    });
    
    // Individual checkbox functionality
    accountCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            
            // Highlight the row when selected
            const row = this.closest('tr');
            if (row) {
                if (this.checked) {
                    row.classList.add('highlight-row');
                } else {
                    row.classList.remove('highlight-row');
                }
            }
            
            // Update select all checkbox state
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                // Check if all checkboxes are checked
                const allChecked = Array.from(accountCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
    
    // Form submission confirmation
    bulkActionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedCount = document.querySelectorAll('.account-checkbox:checked').length;
        const selectedStatus = this.querySelector('select[name="status"]').value;
        
        if (selectedCount === 0) {
            Swal.fire({
                title: 'No Accounts Selected',
                text: 'Please select at least one account to update.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        if (!selectedStatus) {
            Swal.fire({
                title: 'No Status Selected',
                text: 'Please select a status to apply to the selected accounts.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        Swal.fire({
            title: 'Confirm Status Update',
            html: `Are you sure you want to update <strong>${selectedCount}</strong> accounts to <strong>${selectedStatus}</strong> status?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Remove any method override that might have been added
                const methodField = this.querySelector('input[name="_method"]');
                if (methodField) {
                    methodField.remove();
                }
                
                // Force the form to use POST method
                this.setAttribute('method', 'POST');
                
                // Create a new form and submit it manually to bypass any browser extensions
                const newForm = document.createElement('form');
                newForm.method = 'POST';
                newForm.action = this.action;
                newForm.style.display = 'none';
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 this.querySelector('input[name="_token"]')?.value;
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    newForm.appendChild(csrfInput);
                }
                
                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = selectedStatus;
                newForm.appendChild(statusInput);
                
                // Add selected account IDs
                document.querySelectorAll('.account-checkbox:checked').forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'account_ids[]';
                    input.value = checkbox.value;
                    newForm.appendChild(input);
                });
                
                // Append form to body and submit
                document.body.appendChild(newForm);
                newForm.submit();
            }
        });
    });
    
    // Update selected count and button state
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.account-checkbox:checked').length;
        selectedCountDisplay.textContent = `(${selectedCount} selected)`;
        fabCounter.textContent = selectedCount;
        
        // Show/hide the FAB counter
        if (selectedCount > 0) {
            fabCounter.style.display = 'flex';
            bulkActionsFab.classList.add('btn-primary');
            bulkActionsFab.classList.remove('btn-secondary');
        } else {
            fabCounter.style.display = 'none';
            bulkActionsFab.classList.remove('btn-primary');
            bulkActionsFab.classList.add('btn-secondary');
        }
        
        // Enable/disable bulk update button
        bulkUpdateBtn.disabled = selectedCount === 0;
    }
    
    // Floating Action Button for Bulk Actions
    const body = document.querySelector('body');
    
    // Create FAB
    const fab = document.createElement('div');
    fab.className = 'bulk-actions-fab';
    
    const bulkActionsFab = document.createElement('button');
    bulkActionsFab.className = 'btn btn-secondary';
    bulkActionsFab.innerHTML = '<i class="fas fa-tasks"></i>';
    bulkActionsFab.title = 'Bulk Actions';
    
    const fabCounter = document.createElement('div');
    fabCounter.className = 'bulk-actions-counter';
    fabCounter.style.display = 'none';
    fabCounter.textContent = '0';
    
    bulkActionsFab.appendChild(fabCounter);
    fab.appendChild(bulkActionsFab);
    
    // Create Bulk Actions Panel
    const bulkActionsPanel = document.createElement('div');
    bulkActionsPanel.className = 'bulk-actions-panel';
    bulkActionsPanel.innerHTML = `
        <h6 class="font-weight-bold mb-3">Bulk Actions</h6>
        <div class="mb-3">
            <label class="form-label">Change Status</label>
            <select class="form-select form-control mb-2" id="fab-status">
                <option value="">Select Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="active">Active</option>
                <option value="in_use">In Use</option>
                <option value="blocked">Blocked</option>
            </select>
            <button class="btn btn-primary btn-sm w-100" id="fab-update-btn" disabled>
                <i class="fas fa-save me-1"></i> Update Status
            </button>
        </div>
        <div class="mb-3">
            <label class="form-label">Quick Selection</label>
            <div class="d-grid gap-2">
                <button class="btn btn-outline-primary btn-sm" id="select-all-fab">
                    <i class="fas fa-check-square me-1"></i> Select All
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="deselect-all-fab">
                    <i class="fas fa-square me-1"></i> Deselect All
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Selection Info</label>
            <div class="alert alert-info py-2 mb-0">
                <small><span id="fab-selected-count">0</span> accounts selected</small>
            </div>
        </div>
    `;
    
    // Add to DOM
    body.appendChild(fab);
    body.appendChild(bulkActionsPanel);
    
    // Initialize panel elements
    const fabStatusSelect = document.getElementById('fab-status');
    const fabUpdateBtn = document.getElementById('fab-update-btn');
    const fabSelectedCount = document.getElementById('fab-selected-count');
    const selectAllFabBtn = document.getElementById('select-all-fab');
    const deselectAllFabBtn = document.getElementById('deselect-all-fab');
    
    // Toggle panel visibility
    bulkActionsFab.addEventListener('click', function() {
        if (bulkActionsPanel.style.display === 'block') {
            bulkActionsPanel.style.display = 'none';
        } else {
            bulkActionsPanel.style.display = 'block';
            updateFabSelectedCount();
        }
    });
    
    // Update FAB selected count
    function updateFabSelectedCount() {
        const selectedCount = document.querySelectorAll('.account-checkbox:checked').length;
        fabSelectedCount.textContent = selectedCount;
        fabUpdateBtn.disabled = selectedCount === 0 || !fabStatusSelect.value;
    }
    
    // Status select change
    fabStatusSelect.addEventListener('change', function() {
        updateFabSelectedCount();
    });
    
    // Select all from FAB
    selectAllFabBtn.addEventListener('click', function() {
        selectAllCheckbox.checked = true;
        accountCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
            const row = checkbox.closest('tr');
            if (row) row.classList.add('highlight-row');
        });
        updateSelectedCount();
        updateFabSelectedCount();
    });
    
    // Deselect all from FAB
    deselectAllFabBtn.addEventListener('click', function() {
        selectAllCheckbox.checked = false;
        accountCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            const row = checkbox.closest('tr');
            if (row) row.classList.remove('highlight-row');
        });
        updateSelectedCount();
        updateFabSelectedCount();
    });
    
    // FAB update button
    fabUpdateBtn.addEventListener('click', function() {
        const selectedCount = document.querySelectorAll('.account-checkbox:checked').length;
        const selectedStatus = fabStatusSelect.value;
        
        if (selectedCount === 0 || !selectedStatus) return;
        
        Swal.fire({
            title: 'Confirm Status Update',
            html: `Are you sure you want to update <strong>${selectedCount}</strong> accounts to <strong>${selectedStatus}</strong> status?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a new form and submit
                const newForm = document.createElement('form');
                newForm.method = 'POST';
                newForm.action = "{{ route('admin.facebook-quick-check-bulk-update') }}";
                newForm.style.display = 'none';
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                newForm.appendChild(csrfInput);
                
                // Add status
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = selectedStatus;
                newForm.appendChild(statusInput);
                
                // Add selected account IDs
                document.querySelectorAll('.account-checkbox:checked').forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'account_ids[]';
                    input.value = checkbox.value;
                    newForm.appendChild(input);
                });
                
                // Append form to body and submit
                document.body.appendChild(newForm);
                newForm.submit();
            }
        });
    });
    
    // Close panel when clicking outside
    document.addEventListener('click', function(event) {
        if (!fab.contains(event.target) && !bulkActionsPanel.contains(event.target)) {
            bulkActionsPanel.style.display = 'none';
        }
    });
});
</script>
@endpush 