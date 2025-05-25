@extends('frontend.layouts.master')

@section('title', 'Create Order')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Current Balance Card -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-muted">Current Balance</div>
                            <h3 class="mb-0">${{ number_format(auth()->user()->balance, 2) }}</h3>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('funds.add') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Funds
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create New Order</h6>
                </div>
                <div class="card-body">
                    <div class="service-info mb-4">
                        <h5 class="mb-3">{{ $service->name }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Price per 1000:</strong> ${{ number_format($service->price, 2) }}</p>
                                <p class="mb-1"><strong>Min Order:</strong> {{ number_format($service->min_quantity) }}</p>
                                <p class="mb-1"><strong>Max Order:</strong> {{ number_format($service->max_quantity) }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($service->requirements)
                                <p class="mb-1"><strong>Requirements:</strong></p>
                                <ul class="mb-0">
                                    @foreach(json_decode($service->requirements) as $requirement)
                                    <li>{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('orders.store', $service) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="link" class="form-label">Link</label>
                            <input type="url" class="form-control @error('link') is-invalid @enderror" 
                                id="link" name="link" 
                                placeholder="Enter your target link" 
                                value="{{ old('link') }}" required>
                            @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <input type="hidden" name="extracted_uid" id="extracted_uid">
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" name="quantity" 
                                min="{{ $service->min_quantity }}" 
                                max="{{ $service->max_quantity }}" 
                                value="{{ old('quantity', $service->min_quantity) }}" 
                                required>
                            @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Order Summary</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Quantity:</span>
                                        <span id="summaryQuantity">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Price per 1000:</span>
                                        <span>${{ number_format($service->price, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Amount:</span>
                                        <strong>$<span id="totalAmount">0.00</span></strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Current Balance:</span>
                                        <span>${{ number_format(auth()->user()->balance, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Remaining Balance:</span>
                                        <strong class="text-primary">$<span id="remainingBalance">{{ number_format(auth()->user()->balance, 2) }}</span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submitButton">
                                <i class="fas fa-shopping-cart me-2"></i> Place Order
                            </button>
                            <a href="{{ route('services') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Services
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const totalAmountSpan = document.getElementById('totalAmount');
        const summaryQuantitySpan = document.getElementById('summaryQuantity');
        const remainingBalanceSpan = document.getElementById('remainingBalance');
        const submitButton = document.getElementById('submitButton');
        const currentBalance = {{ auth()->user()->balance }};
        const pricePerThousand = {{ $service->price }};
        const linkInput = document.getElementById('link');
        const extractedUidInput = document.getElementById('extracted_uid');

        function calculateTotal() {
            const quantity = parseInt(quantityInput.value) || 0;
            const total = (quantity * pricePerThousand) / 1000;
            const remaining = currentBalance - total;

            totalAmountSpan.textContent = total.toFixed(2);
            summaryQuantitySpan.textContent = quantity.toLocaleString();
            remainingBalanceSpan.textContent = remaining.toFixed(2);

            // Update remaining balance color based on value
            remainingBalanceSpan.parentElement.className = remaining < 0 ? 'text-danger' : 'text-primary';

            // Disable submit button if remaining balance is negative
            submitButton.disabled = remaining < 0;
            if (remaining < 0) {
                submitButton.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Insufficient Balance';
            } else {
                submitButton.innerHTML = '<i class="fas fa-shopping-cart me-2"></i> Place Order';
            }
        }

        quantityInput.addEventListener('input', calculateTotal);
        calculateTotal(); // Initial calculation
        
        // Auto Facebook UID Finder functionality
        let uidExtractionTimeout;
        
        linkInput.addEventListener('input', function() {
            // Clear any existing timeout
            if (uidExtractionTimeout) {
                clearTimeout(uidExtractionTimeout);
            }
            
            // Set a new timeout to avoid making requests on every keystroke
            uidExtractionTimeout = setTimeout(function() {
                const url = linkInput.value.trim();
                
                // Basic validation
                if (!url) {
                    return;
                }
                
                // Check if it's a Facebook URL
                if (!url.includes('facebook.com') && !url.includes('fb.com')) {
                    return;
                }
                
                // Send AJAX request
                $.ajax({
                    url: '{{ route("uid-finder.extract") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        url: url,
                        platform: 'facebook'
                    },
                    success: function(response) {
                        // Store the extracted UID in the hidden field
                        extractedUidInput.value = response.uid;
                        
                        // Flash the input to show it's been processed
                        linkInput.classList.add('is-valid');
                        setTimeout(function() {
                            linkInput.classList.remove('is-valid');
                        }, 2000);
                    },
                    error: function(xhr) {
                        // In case of error, just clear the extracted UID
                        extractedUidInput.value = '';
                    }
                });
            }, 800); // Wait for 800ms after typing stops
        });
    });
</script>
@endpush 