@extends('frontend.layouts.master')

@section('title', 'Create Order')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back to services button -->
            <div class="mb-4">
                <a href="{{ route('services') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Back to Services
                </a>
            </div>
            
            @php
                $dailyOrderLimit = auth()->user()->daily_order_limit > 0 ? auth()->user()->daily_order_limit : 100;
                $todayOrderCount = \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
                $remainingOrders = $dailyOrderLimit - $todayOrderCount;
                $usedOrders = $todayOrderCount;
                $orderLimitPercentage = ($dailyOrderLimit > 0) ? ($usedOrders / $dailyOrderLimit) * 100 : 0;
            @endphp
            
            @if($remainingOrders <= 0 && $dailyOrderLimit > 0)
            <div class="alert alert-danger mb-4 shadow-sm border-0 rounded-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading">Daily Order Limit Reached!</h5>
                        <p>You've used all of your {{ $dailyOrderLimit }} orders for today. Your limit will reset tomorrow.</p>
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
                        <p>You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ $dailyOrderLimit }}.</p>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 5px; border-radius: 3px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $orderLimitPercentage }}%"></div>
                </div>
            </div>
            @endif
            
            <!-- Current Balance Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-wallet text-primary fa-2x"></i>
                                </div>
                                <div>
                                    <div class="text-muted">Current Balance</div>
                                    <h3 class="mb-0 fw-bold">${{ number_format(auth()->user()->balance, 2) }}</h3>
                                    <small class="text-muted">
                                        @if($dailyOrderLimit > 0)
                                        <i class="fas fa-calendar-day me-1"></i> {{ $remainingOrders }} of {{ $dailyOrderLimit }} orders remaining today
                                        @else
                                        <i class="fas fa-infinity me-1"></i> No daily order limit applied
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('funds.add') }}" class="btn btn-primary rounded-pill shadow-sm">
                                <i class="fas fa-plus me-2"></i>Add Funds
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header py-3 bg-white border-0">
                    <h5 class="m-0 fw-bold text-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Create New Order
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="service-info mb-4 p-3 bg-light rounded-3">
                        <h5 class="mb-3 fw-bold text-primary">{{ $service->name }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="text-muted me-2">Price per 1000:</div>
                                        @if(auth()->user()->custom_rate)
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                ${{ number_format(auth()->user()->custom_rate, 4) }}
                                                <small>(Custom rate)</small>
                                            </span>
                                        @else
                                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                                ${{ number_format($service->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted me-2">Min Order:</div>
                                    <div class="fw-medium">{{ number_format($service->min_quantity) }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-muted me-2">Max Order:</div>
                                    <div class="fw-medium">{{ number_format($service->max_quantity) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($service->requirements)
                                <div class="mb-2">
                                    <div class="text-muted mb-2">Requirements:</div>
                                    <ul class="list-group list-group-flush bg-transparent">
                                        @foreach(json_decode($service->requirements) as $requirement)
                                        <li class="list-group-item bg-transparent px-0 py-1 border-0">
                                            <i class="fas fa-check-circle text-success me-2"></i>{{ $requirement }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('orders.store', $service) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="link" class="form-label fw-medium">Link <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-link text-primary"></i>
                                </span>
                                <input type="url" class="form-control @error('link') is-invalid @enderror border-start-0 ps-0" 
                                    id="link" name="link" 
                                    placeholder="Enter your target link" 
                                    value="{{ old('link') }}" required>
                                @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i> Enter the full URL including https://
                            </div>
                            <input type="hidden" name="extracted_uid" id="extracted_uid">
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="form-label fw-medium">Quantity <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-hashtag text-primary"></i>
                                </span>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror border-start-0 ps-0" 
                                    id="quantity" name="quantity" 
                                    min="{{ $service->min_quantity }}" 
                                    max="{{ $service->max_quantity }}" 
                                    value="{{ old('quantity', $service->min_quantity) }}" 
                                    required>
                                @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i> Min: {{ number_format($service->min_quantity) }} - Max: {{ number_format($service->max_quantity) }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Order Summary</label>
                            <div class="card border-0 bg-light rounded-3">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                                <div class="text-muted small">Quantity</div>
                                                <div class="fw-bold" id="summaryQuantity">0</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                                <div class="text-muted small">Price per 1000</div>
                                                <div class="fw-bold">
                                                    @if(auth()->user()->custom_rate)
                                                        ${{ number_format(auth()->user()->custom_rate, 4) }}
                                                    @else
                                                        ${{ number_format($service->price, 2) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                                <div class="text-muted small">Total Amount</div>
                                                <div class="fw-bold text-primary">$<span id="totalAmount">0.00</span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted">Current Balance</div>
                                            <div class="fw-medium">${{ number_format(auth()->user()->balance, 2) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-muted">Remaining Balance</div>
                                            <div class="fw-bold text-primary">$<span id="remainingBalance">{{ number_format(auth()->user()->balance, 2) }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium">Additional Notes (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-comment text-primary"></i>
                                </span>
                                <textarea class="form-control border-start-0 ps-0" id="description" name="description" rows="3" 
                                    placeholder="Add any specific instructions or notes here">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitButton">
                                <i class="fas fa-shopping-cart me-2"></i> Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: all 0.2s ease;
}
.card:hover {
    transform: translateY(-2px);
}
.form-control:focus, .input-group-text {
    border-color: #4e73df;
    box-shadow: none;
}
.input-group-text {
    border-right: none;
}
.form-control {
    border-left: none;
}
.badge {
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Order Limit Notification
        @php
            $dailyOrderLimit = auth()->user()->daily_order_limit > 0 ? auth()->user()->daily_order_limit : 100;
            $todayOrderCount = \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
            $remainingOrders = $dailyOrderLimit - $todayOrderCount;
            $usedOrders = $todayOrderCount;
            $orderLimitPercentage = ($dailyOrderLimit > 0) ? ($usedOrders / $dailyOrderLimit) * 100 : 0;
        @endphp
        
        @if($remainingOrders <= 0 && $dailyOrderLimit > 0)
            Swal.fire({
                title: 'Daily Order Limit Reached!',
                html: '<div class="text-center mb-3"><i class="fas fa-exclamation-circle text-danger fa-4x"></i></div>' +
                      '<p>You\'ve used all of your {{ $dailyOrderLimit }} orders for today.</p>' +
                      '<p>Your limit will reset tomorrow.</p>',
                icon: 'error',
                confirmButtonText: 'Got it',
                confirmButtonColor: '#dc3545'
            });
        @elseif($remainingOrders <= 2 && $dailyOrderLimit > 0)
            Swal.fire({
                title: 'Almost at Daily Limit!',
                html: '<div class="text-center mb-3"><i class="fas fa-exclamation-triangle text-warning fa-4x"></i></div>' +
                      '<p>You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ $dailyOrderLimit }}.</p>' +
                      '<div class="progress mt-3" style="height: 10px;">' +
                      '  <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $orderLimitPercentage }}%"></div>' +
                      '</div>',
                icon: 'warning',
                confirmButtonText: 'Understood',
                confirmButtonColor: '#ffc107'
            });
        @endif
        
        const quantityInput = document.getElementById('quantity');
        const totalAmountSpan = document.getElementById('totalAmount');
        const summaryQuantitySpan = document.getElementById('summaryQuantity');
        const remainingBalanceSpan = document.getElementById('remainingBalance');
        const submitButton = document.getElementById('submitButton');
        const currentBalance = {{ auth()->user()->balance }};
        const pricePerThousand = {{ auth()->user()->custom_rate ?? $service->price }};
        const linkInput = document.getElementById('link');
        const extractedUidInput = document.getElementById('extracted_uid');
        const dailyOrderLimit = {{ auth()->user()->daily_order_limit }};
        const usedOrdersToday = {{ \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count() }};
        const remainingOrdersToday = dailyOrderLimit - usedOrdersToday;
        const orderForm = document.querySelector('form');

        function calculateTotal() {
            const quantity = parseInt(quantityInput.value) || 0;
            const total = (quantity * pricePerThousand) / 1000;
            const remaining = currentBalance - total;

            totalAmountSpan.textContent = total.toFixed(2);
            summaryQuantitySpan.textContent = quantity.toLocaleString();
            remainingBalanceSpan.textContent = remaining.toFixed(2);

            // Update remaining balance color based on value
            remainingBalanceSpan.parentElement.className = remaining < 0 ? 'fw-bold text-danger' : 'fw-bold text-primary';

            // Disable submit button if remaining balance is negative
            submitButton.disabled = remaining < 0;
            if (remaining < 0) {
                submitButton.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Insufficient Balance';
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-danger');
            } else {
                submitButton.innerHTML = '<i class="fas fa-shopping-cart me-2"></i> Place Order';
                submitButton.classList.remove('btn-danger');
                submitButton.classList.add('btn-primary');
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
                
                // Show loading indicator
                linkInput.classList.add('loading-input');
                
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
                        linkInput.classList.remove('loading-input');
                        linkInput.classList.add('is-valid');
                        
                        // Show success tooltip
                        const tooltip = document.createElement('div');
                        tooltip.className = 'uid-tooltip';
                        tooltip.innerHTML = `<i class="fas fa-check-circle me-1"></i> UID extracted: ${response.uid}`;
                        linkInput.parentNode.appendChild(tooltip);
                        
                        setTimeout(function() {
                            tooltip.classList.add('show');
                        }, 10);
                        
                        setTimeout(function() {
                            tooltip.classList.remove('show');
                            setTimeout(() => tooltip.remove(), 300);
                            linkInput.classList.remove('is-valid');
                        }, 3000);
                    },
                    error: function(xhr) {
                        // In case of error, just clear the extracted UID
                        extractedUidInput.value = '';
                        linkInput.classList.remove('loading-input');
                    }
                });
            }, 800); // Wait for 800ms after typing stops
        });

        // Form submission validation
        orderForm.addEventListener('submit', function(event) {
            // Check if user has already reached their daily limit
            if (remainingOrdersToday <= 0 && dailyOrderLimit > 0) {
                event.preventDefault(); // Stop form submission
                
                // Show warning popup
                Swal.fire({
                    title: 'Daily Order Limit Reached!',
                    html: `<div class="text-center mb-3"><i class="fas fa-exclamation-circle text-danger fa-4x"></i></div>
                          <p>You've reached your daily limit of <strong>${dailyOrderLimit}</strong> orders.</p>
                          <p>Please try again tomorrow when your limit resets.</p>`,
                    icon: 'error',
                    confirmButtonText: 'I Understand',
                    confirmButtonColor: '#dc3545'
                });
                
                // Return false to prevent form submission
                return false;
            }
            
            // If we got here, form submission can proceed
            return true;
        });
    });
</script>
@endpush 