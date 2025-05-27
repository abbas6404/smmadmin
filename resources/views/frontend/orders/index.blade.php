@extends('frontend.layouts.master')

@section('title', 'Orders')

@section('content')
<div class="container-fluid px-4">
    <!-- Toast Notifications Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999" id="toastContainer"></div>
    
    @php
        $remainingOrders = auth()->user()->daily_order_limit - \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
        $orderLimitPercentage = (1 - ($remainingOrders / auth()->user()->daily_order_limit)) * 100;
    @endphp
    
    @if($remainingOrders <= 0)
    <div class="alert alert-danger mb-4">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Daily Order Limit Reached!</h5>
                <p class="mb-0">You've used all of your {{ auth()->user()->daily_order_limit }} orders for today. Your limit will reset tomorrow.</p>
            </div>
            <div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @elseif($remainingOrders <= 2)
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Almost at Daily Limit!</h5>
                <p class="mb-0">You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ auth()->user()->daily_order_limit }}.</p>
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
        <h4 class="mb-0">Orders</h4>
        <div>
            <a href="{{ route('services') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Order
            </a>
        </div>
    </div>

    <!-- Order Stats -->
    <div class="row mb-4">
        <div class="col-md">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Orders</h6>
                    <h3>{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h3>{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Processing</h6>
                    <h3>{{ $stats['processing'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Completed</h6>
                    <h3>{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Cancelled</h6>
                    <h3>{{ $stats['cancelled'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6>Daily Limit</h6>
                    <h3>{{ \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count() }} / {{ auth()->user()->daily_order_limit }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by ID, link or description...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Date</option>
                        <option value="total_amount" {{ request('sort') == 'total_amount' ? 'selected' : '' }}>Amount</option>
                        <option value="quantity" {{ request('sort') == 'quantity' ? 'selected' : '' }}>Quantity</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Link</th>
                            <th>Start Count</th>
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
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->service->name }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ $order->link }}" target="_blank" rel="noopener noreferrer" class="text-primary">
                                        {{ Str::limit($order->link, 30) }}
                                        <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                    <button class="btn btn-sm btn-link p-0 ms-1" onclick="copyToClipboard('{{ $order->link }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td>{{ number_format($order->start_count) }}</td>
                            <td>{{ number_format($order->quantity) }}</td>
                            <td>
                                @if($order->status === 'pending')
                                    <select class="form-select form-select-sm status-select" 
                                            data-order-id="{{ $order->id }}"
                                            data-original-status="{{ $order->status }}">
                                        <option value="pending" selected>Pending</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                @elseif($order->status === 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-{{ 
                                        $order->status === 'completed' ? 'success' : 
                                        ($order->status === 'processing' ? 'info' : 
                                        ($order->status === 'cancelled' ? 'danger' : 'warning')) 
                                    }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($order->quantity !== null)
                                    @php
                                        $completed = $order->quantity - ($order->remains ?? 0);
                                        $percentage = ($completed / $order->quantity) * 100;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 6px;">
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
                                        <span class="ms-2 small">
                                            {{ number_format($completed) }}/{{ number_format($order->quantity) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
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
            </div>

            <div class="d-flex justify-content-end">
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
    border-radius: 3px;
}
.badge {
    padding: 0.5em 0.75em;
}
.status-select {
    min-width: 130px;
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
}
.status-select option {
    padding: 8px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Daily Order Limit Notification
    @php
        $remainingOrders = auth()->user()->daily_order_limit - \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
        $usedOrders = auth()->user()->daily_order_limit - $remainingOrders;
        $orderLimitPercentage = ($usedOrders / auth()->user()->daily_order_limit) * 100;
    @endphp
    
    @if($remainingOrders <= 0)
        Swal.fire({
            title: 'Daily Order Limit Reached!',
            html: '<div class="text-center mb-3"><i class="fas fa-exclamation-circle text-danger fa-4x"></i></div>' +
                  '<p>You\'ve used all of your {{ auth()->user()->daily_order_limit }} orders for today.</p>' +
                  '<p>Your limit will reset tomorrow.</p>',
            icon: 'error',
            confirmButtonText: 'Got it',
            confirmButtonColor: '#dc3545'
        });
    @elseif($remainingOrders <= 2)
        Swal.fire({
            title: 'Almost at Daily Limit!',
            html: '<div class="text-center mb-3"><i class="fas fa-exclamation-triangle text-warning fa-4x"></i></div>' +
                  '<p>You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ auth()->user()->daily_order_limit }}.</p>' +
                  '<div class="progress mt-3" style="height: 10px;">' +
                  '  <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $orderLimitPercentage }}%"></div>' +
                  '</div>',
            icon: 'warning',
            confirmButtonText: 'Understood',
            confirmButtonColor: '#ffc107'
        });
    @endif

    // Function to copy text to clipboard
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show toast notification
            showToast('Copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Failed to copy text: ', err);
            showToast('Failed to copy text', 'danger');
        });
    };
    
    // Toast notification function
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        
        // Create toast element
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

    // Handle status changes
    $('.status-select').on('change', function(e) {
        e.preventDefault();
        const select = $(this);
        const orderId = select.data('order-id');
        const newStatus = select.val();
        const originalStatus = select.data('original-status');

        // Skip if status hasn't changed
        if (newStatus === originalStatus) {
            return;
        }

        console.log('Status change initiated:', {
            orderId: orderId,
            newStatus: newStatus,
            originalStatus: originalStatus
        });

        let confirmMessage = '';
        let confirmButtonColor = '';
        
        if (newStatus === 'cancelled') {
            confirmMessage = 'Are you sure you want to cancel this order? This action cannot be undone.';
            confirmButtonColor = '#dc3545'; // danger red
        } else {
            confirmMessage = `Are you sure you want to change the order status to ${newStatus}?`;
            confirmButtonColor = '#3085d6'; // primary blue
        }

        // Show confirmation dialog
        Swal.fire({
            title: newStatus === 'cancelled' ? 'Cancel Order' : 'Change Order Status',
            text: confirmMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: newStatus === 'cancelled' ? 'Yes, cancel it!' : 'Yes, change it!',
            cancelButtonText: 'No, keep it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                select.prop('disabled', true);
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the order status',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Make the AJAX request
                $.ajax({
                    url: "{{ url('orders') }}/" + orderId + "/status",
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Response received:', response);
                        if (response.success) {
                            // Close SweetAlert
                            Swal.close();
                            
                            // Show success toast
                            let successMessage = newStatus === 'cancelled' 
                                ? 'Order has been cancelled successfully' 
                                : 'Order status has been updated successfully';
                            
                            showToast(successMessage, 'success');
                            
                            // Reload the page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            throw new Error(response.error || 'Failed to update status');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error details:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });

                        // Reset select
                        select.prop('disabled', false);
                        select.val(originalStatus);

                        // Close SweetAlert
                        Swal.close();

                        // Parse error message
                        let errorMessage = 'Failed to update order status';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.error || errorMessage;
                        } catch (e) {
                            console.error('Failed to parse error response:', e);
                        }

                        // Show error toast
                        showToast(errorMessage, 'danger');
                    }
                });
            } else {
                // Reset to original status if user cancels
                select.val(originalStatus);
            }
        });
    });

    // Store original status when page loads
    $('.status-select').each(function() {
        const currentStatus = $(this).val();
        $(this).data('original-status', currentStatus);
        console.log('Stored original status:', {
            orderId: $(this).data('order-id'),
            status: currentStatus
        });
    });
});
</script>
@endpush 