@extends('frontend.layouts.master')

@section('title', 'Order #' . $order->id)

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
                                                <tr>
                                                    <td class="text-muted fw-medium ps-0">Date Created:</td>
                                                    <td class="ps-0">{{ $order->created_at->format('F d, Y h:i A') }}</td>
                                                </tr>
                                                @if($order->updated_at && $order->updated_at->ne($order->created_at))
                                                <tr>
                                                    <td class="text-muted fw-medium ps-0">Last Updated:</td>
                                                    <td class="ps-0">{{ $order->updated_at->format('F d, Y h:i A') }}</td>
                                                </tr>
                                                @endif
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
                                            <a href="{{ $order->link }}" class="btn btn-outline-primary" target="_blank" data-bs-toggle="tooltip" title="Open link in new tab">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $order->link }}')" data-bs-toggle="tooltip" title="Copy link to clipboard">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="mt-2 d-flex align-items-center">
                                            <span class="badge bg-light text-dark me-2">URL Preview:</span>
                                            <a href="{{ $order->link }}" class="text-truncate d-inline-block" style="max-width: 350px;" target="_blank">
                                                {{ $order->link }}
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-medium mb-1">Link UID:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" value="{{ $order->link_uid ?? 'Not available' }}" readonly id="link-uid-input">
                                            @if($order->link_uid)
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $order->link_uid }}')" data-bs-toggle="tooltip" title="Copy UID to clipboard">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            @endif
                                            @if(in_array($order->status, ['pending', 'processing']))
                                            <button class="btn btn-outline-primary" onclick="showUidUpdateModal()" data-bs-toggle="tooltip" title="Edit UID">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @endif
                                        </div>
                                        @if(in_array($order->status, ['pending', 'processing']) && strpos($order->link, 'facebook.com') !== false)
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-info" onclick="extractFacebookUid('{{ $order->link }}')">
                                                <i class="fab fa-facebook me-1"></i> Auto-Extract Facebook UID
                                            </button>
                                        </div>
                                        @endif
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

function showUidUpdateModal() {
    const currentUid = document.getElementById('link-uid-input').value;
    const uidValue = currentUid === 'Not available' ? '' : currentUid;
    
    // Create modal if it doesn't exist
    if (!document.getElementById('uidUpdateModal')) {
        const modalHTML = `
            <div class="modal fade" id="uidUpdateModal" tabindex="-1" aria-labelledby="uidUpdateModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uidUpdateModalLabel">Update Link UID</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new-uid" class="form-label">Link UID</label>
                                <input type="text" class="form-control" id="new-uid" value="${uidValue}">
                                <div class="form-text">Enter the unique identifier for this link.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="updateUid()">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    } else {
        document.getElementById('new-uid').value = uidValue;
    }
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('uidUpdateModal'));
    modal.show();
}

function updateUid() {
    const newUid = document.getElementById('new-uid').value;
    const orderId = {{ $order->id }};
    
    // Show loading state
    const saveButton = document.querySelector('#uidUpdateModal .btn-primary');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
    saveButton.disabled = true;
    
    // Send AJAX request to update UID
    fetch('{{ route('orders.update-uid', $order) }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            link_uid: newUid
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UID input field
            document.getElementById('link-uid-input').value = newUid;
            
            // Close the modal
            bootstrap.Modal.getInstance(document.getElementById('uidUpdateModal')).hide();
            
            // Show success toast
            const toast = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check-circle me-2"></i> ${data.message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            const toastContainer = document.getElementById('toastContainer');
            toastContainer.innerHTML = toast;
            const toastElement = toastContainer.querySelector('.toast');
            const bsToast = new bootstrap.Toast(toastElement, { delay: 3000 });
            bsToast.show();
        } else {
            // Show error toast
            const toast = `
                <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-exclamation-circle me-2"></i> ${data.error}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            const toastContainer = document.getElementById('toastContainer');
            toastContainer.innerHTML = toast;
            const toastElement = toastContainer.querySelector('.toast');
            const bsToast = new bootstrap.Toast(toastElement, { delay: 3000 });
            bsToast.show();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error toast
        const toast = `
            <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> An error occurred. Please try again.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        const toastContainer = document.getElementById('toastContainer');
        toastContainer.innerHTML = toast;
        const toastElement = toastContainer.querySelector('.toast');
        const bsToast = new bootstrap.Toast(toastElement, { delay: 3000 });
        bsToast.show();
    })
    .finally(() => {
        // Reset button state
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
    });
}

function extractFacebookUid(link) {
    // Show loading toast
    const toast = `
        <div class="toast align-items-center text-white bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Extracting Facebook UID...
                </div>
            </div>
        </div>
    `;
    const toastContainer = document.getElementById('toastContainer');
    toastContainer.innerHTML = toast;
    const toastElement = toastContainer.querySelector('.toast');
    const bsToast = new bootstrap.Toast(toastElement, { delay: 10000 });
    bsToast.show();
    
    // Make an AJAX request to extract the UID
    fetch('/api/extract-facebook-uid', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
        
        // Hide the loading toast
        bsToast.hide();
        
        if (!data.success || !data.uid) {
            throw new Error(data.message || 'Failed to extract UID');
        }
        
        // Update the UID input field
        const uid = data.uid;
        document.getElementById('link-uid-input').value = uid;
        console.log('Extracted UID:', uid);
        
        // Show an intermediate toast
        const extractToast = `
            <div class="toast align-items-center text-white bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        UID extracted, updating order...
                    </div>
                </div>
            </div>
        `;
        toastContainer.innerHTML = extractToast;
        const extractToastElement = toastContainer.querySelector('.toast');
        const extractBsToast = new bootstrap.Toast(extractToastElement, { delay: 5000 });
        extractBsToast.show();
        
        // Now update the order with the UID
        const orderId = {{ $order->id }};
        console.log('Updating order #' + orderId + ' with UID: ' + uid);
        
        // Separate function to update the UID
        return updateOrderUid(orderId, uid);
    })
    .then(updateResult => {
        console.log('Update result:', updateResult);
        
        if (!updateResult.success) {
            throw new Error(updateResult.error || 'Failed to update order with UID');
        }
        
        // Show success toast
        const successToast = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i> Facebook UID extracted and saved successfully!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        toastContainer.innerHTML = successToast;
        const successToastElement = toastContainer.querySelector('.toast');
        const successBsToast = new bootstrap.Toast(successToastElement, { delay: 3000 });
        successBsToast.show();
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Show error toast
        const errorToast = `
            <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> ${error.message || 'Failed to extract or update UID. Please try again.'}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        toastContainer.innerHTML = errorToast;
        const errorToastElement = toastContainer.querySelector('.toast');
        const errorBsToast = new bootstrap.Toast(errorToastElement, { delay: 5000 });
        errorBsToast.show();
    });
}

// Helper function to update order UID
function updateOrderUid(orderId, uid) {
    // Ensure UID is a string
    const uidString = String(uid);
    const requestBody = {
        link_uid: uidString
    };
    
    console.log('Sending update request with body:', requestBody);
    
    return fetch('{{ route('orders.update-uid', $order) }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => {
        console.log('Update response status:', response.status);
        if (!response.ok) {
            // Try to get more error details if available
            return response.text().then(text => {
                console.error('Error response body:', text);
                try {
                    const errorData = JSON.parse(text);
                    throw new Error(errorData.error || 'Failed to update UID: HTTP status ' + response.status);
                } catch (e) {
                    throw new Error('Failed to update UID: HTTP status ' + response.status);
                }
            });
        }
        return response.json();
    })
    .catch(error => {
        console.error('Error updating UID:', error);
        return { success: false, error: error.message };
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Initialize progress circle
    const progressCircle = document.querySelector('.progress-circle');
    if (progressCircle) {
        const percentage = progressCircle.getAttribute('data-percentage');
        const radius = 70;
        const circumference = 2 * Math.PI * radius;
        const dashoffset = circumference * (1 - percentage / 100);
        
        const svgHTML = `
            <svg width="160" height="160" viewBox="0 0 160 160">
                <circle cx="80" cy="80" r="${radius}" fill="none" stroke="#e9ecef" stroke-width="12"></circle>
                <circle cx="80" cy="80" r="${radius}" fill="none" stroke="${getProgressColor(percentage)}" stroke-width="12" 
                    stroke-dasharray="${circumference}" stroke-dashoffset="${dashoffset}"
                    transform="rotate(-90 80 80)"></circle>
            </svg>
        `;
        
        progressCircle.insertAdjacentHTML('afterbegin', svgHTML);
    }
});

function getProgressColor(percentage) {
    if (percentage >= 100) return '#28a745'; // Success
    if (percentage >= 50) return '#17a2b8';  // Info
    if (percentage >= 25) return '#ffc107';  // Warning
    return '#dc3545';  // Danger
}
</script>
@endpush 