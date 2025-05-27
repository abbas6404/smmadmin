@extends('frontend.layouts.master')

@section('title', 'Create Mass Order')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @php
                $remainingOrders = auth()->user()->daily_order_limit - \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
                $orderLimitPercentage = (1 - ($remainingOrders / auth()->user()->daily_order_limit)) * 100;
            @endphp
            
            @if($remainingOrders <= 0)
            <div class="alert alert-danger mb-4">
                <h5 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i>Daily Order Limit Reached!</h5>
                <p>You've used all of your {{ auth()->user()->daily_order_limit }} orders for today. Your limit will reset tomorrow.</p>
            </div>
            @elseif($remainingOrders <= 2)
            <div class="alert alert-warning mb-4">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Almost at Daily Limit!</h5>
                <p>You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ auth()->user()->daily_order_limit }}.</p>
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $orderLimitPercentage }}%"></div>
                </div>
            </div>
            @endif
            
            <!-- Current Balance Card -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-muted">Current Balance</div>
                            <h3 class="mb-0">${{ number_format(auth()->user()->balance, 2) }}</h3>
                            <small class="text-muted">
                                Order Limit: {{ auth()->user()->daily_order_limit - \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count() }} of {{ auth()->user()->daily_order_limit }} remaining today
                            </small>
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
                    <h6 class="m-0 font-weight-bold text-primary">Create Mass Order - {{ $service->name }}</h6>
                </div>
                <div class="card-body">
                    <div class="service-info mb-4">
                        <h5 class="mb-3">Service Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <strong>Price per 1000:</strong> 
                                    @if(auth()->user()->custom_rate)
                                        <span class="text-success">${{ number_format(auth()->user()->custom_rate, 4) }}</span>
                                        <small class="text-muted">(Custom rate)</small>
                                    @else
                                        ${{ number_format($service->price, 2) }}
                                    @endif
                                </p>
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
                    
                    <form action="{{ route('orders.mass-store', $service) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="links" class="form-label">Links (One per line)</label>
                            <textarea 
                                name="links" 
                                id="links" 
                                rows="5" 
                                class="form-control @error('links') is-invalid @enderror" 
                                required
                                placeholder="https://example.com/link1&#10;https://example.com/link2&#10;https://example.com/link3"
                            >{{ old('links') }}</textarea>
                            @error('links')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Each line represents a separate order with the same quantity</small>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity per Link</label>
                            <input 
                                type="number" 
                                name="quantity" 
                                id="quantity" 
                                class="form-control @error('quantity') is-invalid @enderror" 
                                value="{{ old('quantity', $service->min_quantity) }}"
                                min="{{ $service->min_quantity }}"
                                max="{{ $service->max_quantity }}"
                                required
                            >
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Min: {{ $service->min_quantity }} - Max: {{ $service->max_quantity }}
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Order Summary</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Number of Links:</span>
                                        <span id="linkCount">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Quantity per Link:</span>
                                        <span id="quantityPerLink">{{ $service->min_quantity }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Price per 1000:</span>
                                        <span>
                                            @if(auth()->user()->custom_rate)
                                                ${{ number_format(auth()->user()->custom_rate, 4) }}
                                            @else
                                                ${{ number_format($service->price, 2) }}
                                            @endif
                                        </span>
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
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea 
                                name="description" 
                                id="description" 
                                rows="2" 
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Any additional notes..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submitButton">
                                <i class="fas fa-shopping-cart me-2"></i> Place Mass Order
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
        
        const linksTextarea = document.getElementById('links');
        const quantityInput = document.getElementById('quantity');
        const linkCountSpan = document.getElementById('linkCount');
        const quantityPerLinkSpan = document.getElementById('quantityPerLink');
        const totalAmountSpan = document.getElementById('totalAmount');
        const remainingBalanceSpan = document.getElementById('remainingBalance');
        const submitButton = document.getElementById('submitButton');
        const currentBalance = {{ auth()->user()->balance }};
        const pricePerThousand = {{ auth()->user()->custom_rate ?? $service->price }};
        const dailyOrderLimit = {{ auth()->user()->daily_order_limit }};
        const usedOrdersToday = {{ \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count() }};
        const remainingOrdersToday = dailyOrderLimit - usedOrdersToday;
        const orderForm = document.querySelector('form');

        function calculateTotal() {
            // Count non-empty lines in the textarea
            const links = linksTextarea.value.split('\n').filter(link => link.trim() !== '');
            const linkCount = links.length;
            const quantity = parseInt(quantityInput.value) || 0;
            
            // Update link count display
            linkCountSpan.textContent = linkCount;
            quantityPerLinkSpan.textContent = quantity.toLocaleString();
            
            // Calculate total cost
            const totalCost = (pricePerThousand * quantity * linkCount) / 1000;
            const remainingBalance = currentBalance - totalCost;
            
            // Update display
            totalAmountSpan.textContent = totalCost.toFixed(2);
            remainingBalanceSpan.textContent = remainingBalance.toFixed(2);
            
            // Update remaining balance color based on value
            remainingBalanceSpan.parentElement.className = remainingBalance < 0 ? 'text-danger' : 'text-primary';
            
            // Disable submit button if remaining balance is negative or no links
            submitButton.disabled = remainingBalance < 0 || linkCount === 0;
            
            if (remainingBalance < 0) {
                submitButton.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Insufficient Balance';
            } else if (linkCount === 0) {
                submitButton.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Add Links First';
            } else {
                submitButton.innerHTML = '<i class="fas fa-shopping-cart me-2"></i> Place Mass Order';
            }
        }

        // Calculate on input change
        linksTextarea.addEventListener('input', calculateTotal);
        quantityInput.addEventListener('input', calculateTotal);
        
        // Initial calculation
        calculateTotal();

        // Form submission validation
        orderForm.addEventListener('submit', function(event) {
            // Count non-empty links
            const links = linksTextarea.value.split('\n').filter(link => link.trim() !== '');
            const linkCount = links.length;
            
            // Check if adding these links would exceed the daily limit
            if (linkCount > remainingOrdersToday) {
                event.preventDefault(); // Stop form submission
                
                // Calculate how many links need to be removed
                const excessLinks = linkCount - remainingOrdersToday;
                
                // Show warning popup
                Swal.fire({
                    title: 'Daily Order Limit Would Be Exceeded!',
                    html: `<div class="text-center mb-3"><i class="fas fa-exclamation-circle text-danger fa-4x"></i></div>
                          <p>You're trying to place <strong>${linkCount}</strong> orders, but you only have <strong>${remainingOrdersToday}</strong> orders remaining today.</p>
                          <p>Please remove at least <strong>${excessLinks}</strong> link${excessLinks > 1 ? 's' : ''} to continue.</p>
                          <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i> Your daily limit is ${dailyOrderLimit} orders per day.
                          </div>`,
                    icon: 'error',
                    confirmButtonText: 'I Understand',
                    confirmButtonColor: '#dc3545',
                    showCancelButton: true,
                    cancelButtonText: 'Edit My Order',
                    cancelButtonColor: '#6c757d',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User clicked "I Understand" - do nothing, let them edit
                    } else {
                        // Focus on the textarea to help them edit
                        linksTextarea.focus();
                    }
                });
                
                // Highlight the textarea to indicate it needs attention
                linksTextarea.classList.add('is-invalid');
                
                // Return false to prevent form submission
                return false;
            }
            
            // If we got here, form submission can proceed
            return true;
        });
    });
</script>
@endpush 