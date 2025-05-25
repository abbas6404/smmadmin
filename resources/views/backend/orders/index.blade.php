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
                            <th>Link</th>
                            <th>UID</th>
                            <th>Quantity</th>
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
                                        <code>{{ $order->link_uid }}</code>
                                        <button class="btn btn-sm btn-link p-0 ms-1" onclick="copyToClipboard('{{ $order->link_uid }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ number_format($order->quantity ?? 0) }}</td>
                                <td>{{ number_format($order->start_count ?? 0) }}</td>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No orders found</td>
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

@section('scripts')
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
});

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
        alert('Copied to clipboard!');
    });
}

function bulkAction(action) {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
    
    if (!selectedOrders.length) {
        alert('Please select at least one order');
        return;
    }

    if (!confirm('Are you sure you want to cancel the selected orders?')) return;

    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/admin/orders/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            order_ids: selectedOrders,
            status: action
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
            window.location.reload();
        } else {
            alert(data.message || 'An error occurred while updating orders');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating orders');
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
}
</script>
@endsection 