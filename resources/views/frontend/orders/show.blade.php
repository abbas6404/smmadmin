@extends('frontend.layouts.master')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <!-- Toast Notifications Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999" id="toastContainer"></div>
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Back button -->
            <div class="mb-4">
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>
            
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-0">
                    <div>
                        <h5 class="m-0 fw-bold text-primary">Order #{{ $order->id }}</h5>
                        <p class="text-muted mb-0 small">Created {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        @if($order->status === 'pending')
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog me-1"></i> Update Status
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statusDropdown">
                                <li>
                                    <form action="{{ route('orders.update-status', $order) }}" method="POST" class="status-update-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-times-circle me-2"></i> Cancel Order
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-edit me-1"></i> Edit Order
                        </a>
                        @endif
                        <span class="badge rounded-pill px-3 py-2 bg-{{ 
                            $order->status === 'completed' ? 'success' : 
                            ($order->status === 'processing' ? 'info' : 
                            ($order->status === 'cancelled' ? 'danger' : 'warning')) 
                        }}">
                            <i class="fas fa-{{ 
                                $order->status === 'completed' ? 'check-circle' : 
                                ($order->status === 'processing' ? 'spinner fa-spin' : 
                                ($order->status === 'cancelled' ? 'times-circle' : 'clock')) 
                            }} me-2"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light rounded-3 h-100">
                                <div class="card-body">
                                    <h5 class="mb-3 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Order Information</h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted fw-medium ps-0" style="width: 40%">Service:</td>
                                                    <td class="ps-0">{{ $order->service->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted fw-medium ps-0">Quantity:</td>
                                                    <td class="ps-0">{{ number_format($order->quantity) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted fw-medium ps-0">Price per 1000:</td>
                                                    <td class="ps-0">${{ number_format($order->price, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted fw-medium ps-0">Total Amount:</td>
                                                    <td class="ps-0"><span class="fw-bold text-primary">${{ number_format($order->total_amount, 2) }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light rounded-3 h-100">
                                <div class="card-body">
                                    <h5 class="mb-3 fw-bold"><i class="fas fa-link me-2 text-primary"></i>Target Information</h5>
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-medium mb-1">Link:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" value="{{ $order->link }}" readonly>
                                            <a href="{{ $order->link }}" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $order->link }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-medium mb-1">Link UID:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" value="{{ $order->link_uid ?? 'Not available' }}" readonly>
                                            @if($order->link_uid)
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $order->link_uid }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Section -->
                    <div class="card border-0 bg-light rounded-3 mt-4">
                        <div class="card-body">
                            <h5 class="mb-3 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Order Progress</h5>
                            @php
                                $startCount = $order->start_count ?? 0;
                                $remains = $order->remains ?? 0;
                                $completed = max(0, $startCount > 0 ? $startCount - $remains : $order->quantity - $remains);
                                $percentage = 0;
                                
                                if ($order->quantity > 0) {
                                    $percentage = min(100, max(0, ($completed / $order->quantity) * 100));
                                }
                            @endphp
                            
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <div class="progress-circle" data-percentage="{{ round($percentage) }}">
                                        <div class="progress-circle-inner">
                                            <div class="progress-circle-value">{{ round($percentage) }}%</div>
                                            <div class="progress-circle-label small text-muted">Completed</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                                <div class="text-muted small">Start Count</div>
                                                <div class="fw-bold">{{ number_format($startCount) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                                <div class="text-muted small">Completed</div>
                                                <div class="fw-bold">{{ number_format($completed) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                                <div class="text-muted small">Remaining</div>
                                                <div class="fw-bold">{{ number_format($remains) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 10px; border-radius: 5px;">
                                        <div class="progress-bar bg-{{ 
                                            $order->status === 'completed' ? 'success' : 
                                            ($order->status === 'processing' ? 'info' : 
                                            ($order->status === 'cancelled' ? 'danger' : 'warning')) 
                                        }}" 
                                        role="progressbar" 
                                        style="width: {{ $percentage }}%" 
                                        aria-valuenow="{{ $percentage }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">0</small>
                                        <small class="text-muted">{{ number_format($order->quantity) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->description)
                    <div class="card border-0 bg-light rounded-3 mt-4">
                        <div class="card-body">
                            <h5 class="mb-3 fw-bold"><i class="fas fa-comment-alt me-2 text-primary"></i>Description</h5>
                            <p class="mb-0">{{ $order->description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($order->error_message)
                    <div class="alert alert-danger rounded-3 mt-4">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Error Message</h5>
                        <p class="mb-0">{{ $order->error_message }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f8f9fa;
    margin: 0 auto;
}

.progress-circle-inner {
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;
    border-radius: 50%;
    background: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.progress-circle-value {
    font-size: 24px;
    font-weight: bold;
    color: #4e73df;
}

.progress-circle::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(
        var(--progress-color, #4e73df) var(--progress-percentage), 
        #e9ecef var(--progress-percentage)
    );
}

.table td, .table th {
    padding: 0.75rem 0;
}

.card {
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up progress circles
    const progressCircles = document.querySelectorAll('.progress-circle');
    progressCircles.forEach(circle => {
        const percentage = circle.getAttribute('data-percentage');
        circle.style.setProperty('--progress-percentage', percentage + '%');
        
        // Set color based on percentage
        let color = '#ffc107'; // warning (default)
        if (percentage >= 100) {
            color = '#28a745'; // success
        } else if (percentage > 50) {
            color = '#17a2b8'; // info
        }
        circle.style.setProperty('--progress-color', color);
    });

    // Handle status update form submission
    const statusUpdateForms = document.querySelectorAll('.status-update-form');
    statusUpdateForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const statusInput = form.querySelector('input[name="status"]');
            const status = statusInput.value;
            let confirmTitle, confirmText, confirmButtonText, confirmButtonColor;
            
            if (status === 'cancelled') {
                confirmTitle = 'Cancel Order?';
                confirmText = 'Are you sure you want to cancel this order? This action cannot be undone.';
                confirmButtonText = 'Yes, Cancel Order';
                confirmButtonColor = '#dc3545';
            } else {
                confirmTitle = 'Update Status?';
                confirmText = `Are you sure you want to change the order status to ${status}?`;
                confirmButtonText = 'Yes, Update';
                confirmButtonColor = '#4e73df';
            }
            
            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
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
                    
                    // Submit the form via AJAX
                    const formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message || 'Order status updated successfully',
                                icon: 'success',
                                confirmButtonColor: '#4e73df'
                            }).then(() => {
                                // Reload the page
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.error || 'Failed to update status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: error.message || 'Something went wrong',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        });
    });
});

function copyToClipboard(text) {
    // Fallback for older browsers
    if (!navigator.clipboard) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";  // Avoid scrolling to bottom
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'Copied to clipboard!' : 'Failed to copy text';
            showToast(msg, successful ? 'success' : 'danger');
        } catch (err) {
            showToast('Failed to copy text', 'danger');
        }

        document.body.removeChild(textArea);
        return;
    }

    // Modern browsers
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy text: ', err);
        showToast('Failed to copy text', 'danger');
    });
}

// UID update functionality removed

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
</script>
@endpush 