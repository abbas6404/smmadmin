@extends('frontend.layouts.master')

@section('title', 'Orders')

@section('content')
<div class="container-fluid px-4">
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
                            <th>Remains</th>
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
                                <a href="{{ $order->link }}" target="_blank" class="text-primary">
                                    {{ Str::limit($order->link, 30) }}
                                </a>
                            </td>
                            <td>{{ number_format($order->start_count) }}</td>
                            <td>{{ number_format($order->remains) }}</td>
                            <td>
                                @if(in_array($order->status, ['pending', 'processing']))
                                    <select class="form-select form-select-sm status-select" 
                                            data-order-id="{{ $order->id }}"
                                            data-original-status="{{ $order->status }}">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
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
                                @if($order->remains !== null)
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
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
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

    // Handle status changes
    $('.status-select').on('change', function(e) {
        e.preventDefault();
        const select = $(this);
        const orderId = select.data('order-id');
        const newStatus = select.val();
        const originalStatus = select.data('original-status');

        console.log('Status change initiated:', {
            orderId: orderId,
            newStatus: newStatus,
            originalStatus: originalStatus
        });

        // Show confirmation dialog
        Swal.fire({
            title: 'Change Order Status',
            text: `Are you sure you want to change the order status to ${newStatus}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, cancel!'
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
                    url: `/orders/${orderId}/status`,
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Response received:', response);
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: 'Order status has been updated successfully',
                                icon: 'success',
                                timer: 1500
                            }).then(() => {
                                // Reload the page to update stats
                                window.location.reload();
                            });
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

                        // Parse error message
                        let errorMessage = 'Failed to update order status';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.error || errorMessage;
                        } catch (e) {
                            console.error('Failed to parse error response:', e);
                        }

                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
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