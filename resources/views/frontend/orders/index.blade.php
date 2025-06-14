@extends('frontend.layouts.master')

@section('title', 'Orders')

@section('content')
<div class="container-fluid px-4">
    <!-- Toast Notifications Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999" id="toastContainer"></div>
    
    @php
        $dailyOrderLimit = auth()->user()->daily_order_limit > 0 ? auth()->user()->daily_order_limit : 100;
        $todayOrderCount = \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
        $remainingOrders = $dailyOrderLimit - $todayOrderCount;
        $orderLimitPercentage = ($dailyOrderLimit > 0) ? (1 - ($remainingOrders / $dailyOrderLimit)) * 100 : 0;
    @endphp
    
    @if($remainingOrders <= 0 && $dailyOrderLimit > 0)
    <div class="alert alert-danger mb-4 shadow-sm border-0 rounded-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading">Daily Order Limit Reached!</h5>
                <p class="mb-0">You've used all of your {{ $dailyOrderLimit }} orders for today. Your limit will reset tomorrow.</p>
            </div>
            <div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @elseif($remainingOrders <= 2 && $dailyOrderLimit > 0)
    <div class="alert alert-warning mb-4 shadow-sm border-0 rounded-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading">Almost at Daily Limit!</h5>
                <p class="mb-0">You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ $dailyOrderLimit }}.</p>
            </div>
            <div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <div class="progress mt-2" style="height: 5px;">
            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $orderLimitPercentage }}%"></div>
        </div>
    </div>
    @endif
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-primary">My Orders</h4>
            <p class="text-muted mb-0">Manage and track your orders</p>
        </div>
        <div>
            <a href="{{ route('services') }}" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-plus me-2"></i> New Order
            </a>
        </div>
    </div>

    <!-- Order Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 mb-2">
                        <i class="fas fa-shopping-cart text-primary fa-2x"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Orders</h6>
                    <h3 class="fw-bold">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 mb-2">
                        <i class="fas fa-clock text-warning fa-2x"></i>
                    </div>
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h3 class="fw-bold">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 mb-2">
                        <i class="fas fa-spinner text-info fa-2x"></i>
                    </div>
                    <h6 class="text-muted mb-1">Processing</h6>
                    <h3 class="fw-bold">{{ $stats['processing'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 mb-2">
                        <i class="fas fa-check-circle text-success fa-2x"></i>
                    </div>
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h3 class="fw-bold">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 mb-2">
                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                    </div>
                    <h6 class="text-muted mb-1">Cancelled</h6>
                    <h3 class="fw-bold">{{ $stats['cancelled'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3 mb-2">
                        <i class="fas fa-calendar-day text-secondary fa-2x"></i>
                    </div>
                    <h6 class="text-muted mb-1">Daily Limit</h6>
                    <h3 class="fw-bold">
                        @php
                            $dailyOrderLimit = auth()->user()->daily_order_limit;
                            $todayOrderCount = \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
                        @endphp
                        {{ $todayOrderCount }} @if($dailyOrderLimit > 0) / {{ $dailyOrderLimit }} @else <small>(unlimited)</small> @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <form action="{{ route('orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" value="{{ request('search') }}" placeholder="Search by ID, link or description...">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Sort By</label>
                    <select name="sort" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Date</option>
                        <option value="total_amount" {{ request('sort') == 'total_amount' ? 'selected' : '' }}>Amount</option>
                        <option value="quantity" {{ request('sort') == 'quantity' ? 'selected' : '' }}>Quantity</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Link</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><span class="badge bg-light text-dark">#{{ $order->id }}</span></td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $order->service->name }}">
                                    {{ $order->service->name }}
                                </span>
                            </td>
                            <td>
                                <div class="link-container">
                                    <a href="{{ $order->link }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $order->link }}">
                                            {{ $order->link }}
                                    </a>
                                    <div class="link-actions">
                                        <a href="{{ $order->link }}" target="_blank" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" title="Open link">
                                            <i class="fas fa-external-link-alt"></i>
                                    </a>
                                        <button class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyToClipboard('{{ $order->link }}')" data-bs-toggle="tooltip" title="Copy link">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    </div>
                                </div>
                                @if($order->link_uid)
                                <div class="mt-1">
                                    <small class="text-muted">UID: 
                                        <span class="badge bg-light text-dark">{{ $order->link_uid }}</span>
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td>{{ number_format($order->quantity) }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 bg-{{ 
                                    $order->status === 'completed' ? 'success' : 
                                    ($order->status === 'processing' ? 'info' : 
                                    ($order->status === 'cancelled' ? 'danger' : 'warning')) 
                                }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                @if($order->quantity !== null)
                                    @php
                                        $completed = $order->quantity - ($order->remains ?? 0);
                                        $percentage = ($completed / $order->quantity) * 100;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px;">
                                            <div class="progress-bar bg-{{ 
                                                $order->status === 'completed' ? 'success' : 
                                                ($order->status === 'processing' ? 'info' : 'warning') 
                                            }}" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%" 
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ms-2 small text-muted">
                                            {{ number_format($completed) }}/{{ number_format($order->quantity) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span title="{{ $order->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $order->created_at->diffForHumans() }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                    @if($order->status === 'pending')
                                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5>No orders found</h5>
                                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                                    <a href="{{ route('services') }}" class="btn btn-primary mt-2">Create New Order</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress {
    background-color: rgba(0,0,0,0.05);
    border-radius: 10px;
}
.badge {
    font-weight: 500;
}
.table th {
    font-weight: 600;
    color: #555;
}
.table td {
    vertical-align: middle;
    padding: 0.75rem 1rem;
}
.pagination {
    --bs-pagination-active-bg: #4e73df;
    --bs-pagination-active-border-color: #4e73df;
}
.card {
    transition: all 0.2s ease;
}
.card:hover {
    transform: translateY(-2px);
}
.link-container {
    position: relative;
    max-width: 200px;
}

.link-container:hover .link-actions {
    opacity: 1;
    visibility: visible;
}

.link-actions {
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s;
    background: rgba(255, 255, 255, 0.9);
    padding: 2px;
    border-radius: 4px;
}

.copy-btn {
    padding: 0.1rem 0.3rem;
    font-size: 0.7rem;
}

@media (max-width: 768px) {
    .link-actions {
        opacity: 1;
        visibility: visible;
    }
}

.badge.bg-light {
    font-family: monospace;
}
</style>
@endpush

@push('scripts')
<script>
    // Toast notification container
    if (!document.getElementById('toastContainer')) {
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    function copyToClipboard(text) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
            // Show toast notification
        const toast = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> Copied to clipboard!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        const toastContainer = document.getElementById('toastContainer');
        toastContainer.innerHTML = toast;
        const toastElement = toastContainer.querySelector('.toast');
        const bsToast = new bootstrap.Toast(toastElement, { delay: 2000 });
        bsToast.show();
    }
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
});
</script>
@endpush 