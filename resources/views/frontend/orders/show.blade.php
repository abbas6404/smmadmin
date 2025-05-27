@extends('frontend.layouts.master')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <!-- Toast Notifications Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999" id="toastContainer"></div>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Order Details</h6>
                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : 'warning') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Order Information</h5>
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Service:</strong> {{ $order->service->name }}</p>
                            <p><strong>Quantity:</strong> {{ number_format($order->quantity) }}</p>
                            <p><strong>Price per 1000:</strong> ${{ number_format($order->price, 2) }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Target Information</h5>
                            <p>
                                <strong>Link:</strong> 
                                <div class="d-flex align-items-center">
                                    <a href="{{ $order->link }}" target="_blank" rel="noopener noreferrer" class="text-primary">
                                        {{ $order->link }}
                                        <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $order->link }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </p>
                            <p>
                                <strong>Link UID:</strong> 
                                <div class="d-flex align-items-center">
                                    @if($order->status === 'pending')
                                        <form action="{{ route('orders.update-uid', $order) }}" method="POST" class="d-flex align-items-center" id="updateUidForm">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="link_uid" class="form-control form-control-sm me-2" value="{{ $order->link_uid ?? '' }}" placeholder="Enter UID" style="min-width: 200px;">
                                            <button type="submit" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                        </form>
                                    @else
                                    <code>{{ $order->link_uid ?? 'Not available' }}</code>
                                    @if($order->link_uid)
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $order->link_uid }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                        @endif
                                    @endif
                                </div>
                            </p>
                            <p><strong>Start Count:</strong> {{ number_format($order->start_count ?? 0) }}</p>
                            <p><strong>Remaining:</strong> {{ number_format($order->remains ?? 0) }}</p>
                            @if($order->start_count && $order->remains)
                                @php
                                    $completed = $order->start_count - $order->remains;
                                    $percentage = ($completed / $order->start_count) * 100;
                                @endphp
                                <div class="mt-3">
                                    <p><strong>Progress:</strong></p>
                                    <div class="progress" style="height: 6px;">
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
                                    <small class="text-muted">
                                        {{ number_format($completed) }}/{{ number_format($order->start_count) }} completed
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($order->description)
                    <div class="mb-4">
                        <h5 class="mb-3">Description</h5>
                        <p>{{ $order->description }}</p>
                    </div>
                    @endif

                    @if($order->error_message)
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Error Message</h5>
                        <p class="mb-0">{{ $order->error_message }}</p>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('services') }}" class="btn btn-primary">Back to Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    }).catch(err => {
        console.error('Failed to copy text: ', err);
        showToast('Failed to copy text', 'danger');
    });
}

// Handle UID form submission
$(document).ready(function() {
    $('#updateUidForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnHtml = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                showToast('UID updated successfully', 'success');
                submitBtn.html(originalBtnHtml);
                submitBtn.prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error updating UID:', xhr);
                let errorMessage = 'Failed to update UID';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                showToast(errorMessage, 'danger');
                submitBtn.html(originalBtnHtml);
                submitBtn.prop('disabled', false);
            }
        });
    });
});

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