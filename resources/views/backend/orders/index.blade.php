@extends('backend.layouts.master')

@section('title', 'Orders')

@section('styles')
<style>
    .pagination {
        margin: 0;
        display: flex;
        padding-left: 0;
        list-style: none;
        gap: 3px;
    }
    .page-link {
        position: relative;
        display: block;
        color: #0d6efd;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        min-width: 38px;
        text-align: center;
    }
    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    .page-item:first-child .page-link {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    .page-item:last-child .page-link {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    .page-link:hover {
        z-index: 2;
        color: #0a58ca;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    .page-link:focus {
        z-index: 3;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Toast Notification */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    .toast {
        min-width: 250px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Orders</h1>
        <div>
            <button class="btn btn-primary" onclick="refreshTable()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Toast Notifications Container -->
    <div class="toast-container"></div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['pending']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Processing</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['processing']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['completed']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-danger h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Cancelled</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['cancelled']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['today']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Orders List
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </button>
                    <button class="btn btn-danger" onclick="bulkAction('cancelled')" id="bulkCancelBtn" disabled>
                        <i class="fas fa-times me-1"></i> Cancel Selected
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <form id="filterForm" action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter" name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="date" class="form-control" id="dateFrom" name="date_from" placeholder="From Date" value="{{ request('date_from') }}">
                            <input type="date" class="form-control" id="dateTo" name="date_to" placeholder="To Date" value="{{ request('date_to') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-filter"></i> Apply
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" name="search" placeholder="Search orders..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table id="ordersTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Link</th>
                            <th>UID</th>
                            <th>Quantity</th>
                            <th>Applied Rate</th>
                            <th>Start Count</th>
                            <th>Remains</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                                </td>
                                <td><code>{{ $order->id }}</code></td>
                                <td>
                                    @if($order->user)
                                        <a href="{{ route('admin.users.show', $order->user->id) }}" class="text-decoration-none">
                                            {{ $order->user->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;">
                                        <a href="{{ $order->link }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                            <code class="small">{{ $order->link }}</code>
                                            <i class="fas fa-external-link-alt small"></i>
                                        </a>
                                        <button class="btn btn-sm btn-link p-0 ms-1" onclick="copyToClipboard('{{ $order->link }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    @if($order->link_uid)
                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                            <form action="{{ route('admin.orders.update-uid', $order) }}" method="POST" class="d-flex w-100">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="link_uid" class="form-control form-control-sm" 
                                                    value="{{ $order->link_uid }}"
                                                    title="Edit UID and click save to update"
                                                    autocomplete="off">
                                                <button type="submit" class="btn btn-outline-primary btn-sm" title="Save UID">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                            <form action="{{ route('admin.orders.update-uid', $order) }}" method="POST" class="d-flex w-100">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="link_uid" class="form-control form-control-sm" 
                                                    placeholder="Enter UID"
                                                    title="Enter UID and click save to update"
                                                    autocomplete="off">
                                                <button type="submit" class="btn btn-outline-primary btn-sm" title="Save UID">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                    @if(strpos($order->link, 'facebook.com') !== false || strpos($order->link, 'fb.com') !== false)
                                        <button class="btn btn-outline-info btn-sm w-100 mt-1" 
                                                onclick="extractFacebookUid('{{ $order->id }}', '{{ $order->link }}')" 
                                                title="Auto-extract Facebook UID">
                                            <i class="fab fa-facebook me-1"></i> Auto-Extract UID
                                        </button>
                                    @endif
                                </td>
                                <td>{{ number_format($order->quantity ?? 0) }}</td>
                                <td>
                                    ${{ number_format($order->price, 4) }}
                                    @if($order->user && $order->user->custom_rate && $order->price == $order->user->custom_rate)
                                        <span class="badge bg-info" title="Custom user rate applied instead of standard service rate">Custom</span>
                                    @else
                                        <span class="badge bg-secondary" title="Standard service rate">Standard</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="input-group input-group-sm" style="max-width: 120px;">
                                        <form action="{{ route('admin.orders.update-start-count', $order) }}" method="POST" class="d-flex w-100">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="start_count" class="form-control form-control-sm"
                                                value="{{ $order->start_count ?? 0 }}"
                                                title="Edit start count and click save to update"
                                                min="0"
                                                autocomplete="off">
                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="Save Start Count">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>{{ number_format($order->remains ?? 0) }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $order->status === 'completed' ? 'success' : 
                                        ($order->status === 'processing' ? 'primary' : 
                                        ($order->status === 'cancelled' ? 'danger' : 'warning'))
                                    }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($order->status === 'pending')
                                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="btn btn-sm btn-success" title="Set to Processing">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} results
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            @if ($orders->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">‹</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">‹</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                @if ($page == $orders->currentPage())
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
                            @if ($orders->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">›</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">›</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All Checkbox Handler
    const selectAll = document.getElementById('selectAll');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const bulkCancelBtn = document.getElementById('bulkCancelBtn');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            orderCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButton();
        });
    }

    orderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButton);
    });
    
    // Date range auto-submit
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    
    if (dateFrom && dateTo) {
        dateFrom.addEventListener('change', function() {
            if (dateTo.value) {
                document.getElementById('filterForm').submit();
            }
        });
        
        dateTo.addEventListener('change', function() {
            if (dateFrom.value) {
                document.getElementById('filterForm').submit();
            }
        });
    }
    
    // Enter key on search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('filterForm').submit();
            }
        });
    }
    
    // Add submit event listeners to all the editable forms
    setupEditableForms();
    
    // Show a success toast if there's a success message
    if (document.querySelector('.alert-success')) {
        showToast(document.querySelector('.alert-success').textContent, 'success');
        // Hide the alert after showing toast
        setTimeout(() => {
            document.querySelector('.alert-success').style.display = 'none';
        }, 500);
    }
});

function setupEditableForms() {
    // Get all UID and Start Count forms
    const editForms = document.querySelectorAll('form[action*="update-uid"], form[action*="update-start-count"]');
    
    editForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.action;
            const submitBtn = this.querySelector('button[type="submit"]');
            const inputField = this.querySelector('input');
            const originalValue = inputField.value;
            
            // Disable button and show loading indicator
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i>';
                
                if (data.success) {
                    // Flash green background briefly
                    inputField.style.backgroundColor = '#d4edda';
                    setTimeout(() => {
                        inputField.style.backgroundColor = '';
                    }, 1000);
                    
                    // Show success toast
                    showToast(data.message || 'Updated successfully', 'success');
                } else {
                    // Reset to original value on error
                    inputField.value = originalValue;
                    
                    // Flash red background briefly
                    inputField.style.backgroundColor = '#f8d7da';
                    setTimeout(() => {
                        inputField.style.backgroundColor = '';
                    }, 1000);
                    
                    // Show error toast
                    showToast(data.message || 'Update failed', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i>';
                
                // Reset to original value on error
                inputField.value = originalValue;
                
                // Flash red background briefly
                inputField.style.backgroundColor = '#f8d7da';
                setTimeout(() => {
                    inputField.style.backgroundColor = '';
                }, 1000);
                
                // Show error toast
                showToast('An error occurred while updating', 'danger');
            });
        });
    });
}

function extractFacebookUid(orderId, link) {
    // Find the input field for this order
    const row = document.querySelector(`input[type="checkbox"].order-checkbox[value="${orderId}"]`).closest('tr');
    const uidInput = row.querySelector('input[name="link_uid"]');
    const saveBtn = row.querySelector('button[type="submit"]');
    
    // Disable the extract button to prevent multiple clicks
    const extractBtn = row.querySelector('button[onclick*="extractFacebookUid"]');
    extractBtn.disabled = true;
    extractBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Extracting...';
    
    // Show extracting toast
    showToast('Extracting Facebook UID...', 'info');
    
    // Make the API request to extract the UID
    fetch('/api/extract-facebook-uid', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            link: link
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to connect to UID extraction service: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('UID extraction response:', data);
        
        if (!data.success || !data.uid) {
            throw new Error(data.message || 'Failed to extract UID');
        }
        
        // Update the UID input field
        uidInput.value = data.uid;
        
        // Highlight the input field
        uidInput.style.backgroundColor = '#d4edda';
        setTimeout(() => {
            uidInput.style.backgroundColor = '';
        }, 1000);
        
        // Show success toast
        showToast('Facebook UID extracted successfully: ' + data.uid, 'success');
        
        // Trigger a click on the save button
        saveBtn.click();
        
        return data.uid;
    })
    .catch(error => {
        console.error('Error extracting UID:', error);
        
        // Show error toast
        showToast(error.message || 'Failed to extract UID', 'danger');
    })
    .finally(() => {
        // Re-enable the extract button
        extractBtn.disabled = false;
        extractBtn.innerHTML = '<i class="fab fa-facebook me-1"></i> Auto-Extract UID';
    });
}

function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container');
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize the Bootstrap toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    
    bsToast.show();
    
    // Remove the toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function updateBulkActionButton() {
    const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
    const bulkCancelBtn = document.getElementById('bulkCancelBtn');
    if (bulkCancelBtn) {
        bulkCancelBtn.disabled = checkedCount === 0;
    }
}

function refreshTable() {
    window.location.reload();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'info');
    });
}

function bulkAction(action) {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
    
    if (!selectedOrders.length) {
        showToast('Please select at least one order', 'warning');
        return;
    }

    // For cancel action, show refund option
    if (action === 'cancelled') {
        // Create a confirmation dialog with refund option
        const confirmationHtml = `
            <div class="modal fade" id="bulkCancelConfirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Bulk Cancellation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to cancel the selected orders (${selectedOrders.length})?</p>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="bulkRefundCheckbox" checked>
                                <label class="form-check-label" for="bulkRefundCheckbox">
                                    Refund users' balances
                                </label>
                            </div>
                            <div class="alert alert-info">
                                <small><i class="fas fa-info-circle me-1"></i> When checked, the order amounts will be credited back to the users' balances. Only orders with amounts greater than $0 will be refunded.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmBulkCancelBtn">
                                Yes, Cancel Orders
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Append the modal to the body
        document.body.insertAdjacentHTML('beforeend', confirmationHtml);
        
        // Show the modal
        const modalElement = document.getElementById('bulkCancelConfirmModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        // Handle confirmation button click
        document.getElementById('confirmBulkCancelBtn').addEventListener('click', function() {
            const refund = document.getElementById('bulkRefundCheckbox').checked;
            
            // Hide the modal
            modal.hide();
            
            // Process the bulk action with refund option
            processBulkAction(selectedOrders, action, refund);
            
            // Remove the modal from the DOM after hiding
            modalElement.addEventListener('hidden.bs.modal', function() {
                modalElement.remove();
            });
        });
    } else {
        // For other actions, just show a simple confirmation
        if (!confirm(`Are you sure you want to update the selected orders to ${action}?`)) return;
        
        processBulkAction(selectedOrders, action, false);
    }
}

function processBulkAction(orderIds, status, refund) {
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Show processing toast
    showToast(`Processing ${orderIds.length} orders...`, 'info');

    fetch('/admin/orders/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            order_ids: orderIds,
            status: status,
            refund: refund
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Orders updated successfully', 'success');
            // Reload after a short delay to show the toast
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'An error occurred while updating orders', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while updating orders', 'danger');
    });
}

function exportToExcel() {
    // Clone the current filter form
    const form = document.getElementById('filterForm').cloneNode(true);
    
    // Change the action to the export URL
    form.action = "{{ route('admin.orders.export') }}";
    
    // Make it invisible and submit it
    form.style.display = 'none';
    document.body.appendChild(form);
    form.submit();
    
    // Clean up
    setTimeout(() => {
        document.body.removeChild(form);
    }, 1000);
    
    showToast('Exporting orders to Excel...', 'info');
}
</script>
@endpush 