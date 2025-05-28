@extends('frontend.layouts.master')

@section('title', 'Create Mass Order')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Back button -->
            <div class="mb-4">
                <a href="{{ route('services') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Back to Services
                </a>
            </div>
            
            @php
                $dailyOrderLimit = auth()->user()->daily_order_limit > 0 ? auth()->user()->daily_order_limit : 100;
                $todayOrderCount = \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count();
                $remainingOrders = $dailyOrderLimit - $todayOrderCount;
                $orderLimitPercentage = ($dailyOrderLimit > 0) ? ($todayOrderCount / $dailyOrderLimit) * 100 : 0;
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
                </div>
            </div>
            @elseif($remainingOrders <= 5 && $dailyOrderLimit > 0)
            <div class="alert alert-warning mb-4 shadow-sm border-0 rounded-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading">Almost at Daily Limit!</h5>
                        <p class="mb-0">You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ $dailyOrderLimit }}. Mass ordering may exceed your limit.</p>
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

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header py-3 bg-white border-0">
                    <h5 class="m-0 fw-bold text-primary">
                        <i class="fas fa-layer-group me-2"></i>Create Multiple Orders
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info rounded-3 mb-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">How to Place Mass Orders</h5>
                                <p>Enter one link per line. The same quantity will be applied to all links.</p>
                                <p class="mb-0">Example: <code>https://example.com/post1</code><br><code>https://example.com/post2</code></p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('orders.mass-store', $service) }}" method="POST" id="massOrderForm">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label for="links" class="form-label fw-medium">Links <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="fas fa-link text-primary"></i>
                                        </span>
                                        <textarea class="form-control @error('links') is-invalid @enderror border-start-0 ps-0" 
                                            id="links" name="links" rows="10" 
                                            placeholder="https://example.com/post1&#10;https://example.com/post2">{{ old('links') }}</textarea>
                                        @error('links')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i> Enter one link per line
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="quantity" class="form-label fw-medium">Quantity <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0">
                                                <i class="fas fa-sort-numeric-up text-primary"></i>
                                            </span>
                                            <input type="number" class="form-control @error('quantity') is-invalid @enderror border-start-0 ps-0" 
                                                id="quantity" name="quantity" value="{{ old('quantity', $service->min_quantity) }}" 
                                                min="{{ $service->min_quantity }}" max="{{ $service->max_quantity }}">
                                            @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i> Min: {{ $service->min_quantity }}, Max: {{ $service->max_quantity }}
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
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card bg-light border-0 rounded-3 h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Available Services</h6>
                                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Service</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($services as $service)
                                                    <tr class="service-row" data-id="{{ $service->id }}" data-min="{{ $service->min_quantity }}" data-max="{{ $service->max_quantity }}">
                                                        <td>{{ $service->id }}</td>
                                                        <td>
                                                            <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $service->name }}">
                                                                {{ $service->name }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if(auth()->user()->custom_rate)
                                                                ${{ number_format(auth()->user()->custom_rate, 4) }}
                                                            @else
                                                                ${{ number_format($service->price, 2) }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light rounded-3 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Order Summary</h6>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                            <div class="text-muted small">Total Orders</div>
                                            <div class="fw-bold" id="totalOrders">0</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                            <div class="text-muted small">Total Quantity</div>
                                            <div class="fw-bold" id="totalQuantity">0</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 rounded-3 bg-white shadow-sm">
                                            <div class="text-muted small">Total Amount</div>
                                            <div class="fw-bold text-primary" id="totalAmount">$0.00</div>
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
                                        <div class="fw-bold text-primary" id="remainingBalance">${{ number_format(auth()->user()->balance, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitButton">
                                <i class="fas fa-paper-plane me-2"></i> Place Mass Order
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
.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 3px;
}
.table-responsive::-webkit-scrollbar-track {
    background-color: rgba(0,0,0,0.05);
}
.service-row {
    cursor: pointer;
}
.service-row:hover {
    background-color: rgba(78, 115, 223, 0.05);
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
        @elseif($remainingOrders <= 5 && $dailyOrderLimit > 0)
            Swal.fire({
                title: 'Almost at Daily Limit!',
                html: '<div class="text-center mb-3"><i class="fas fa-exclamation-triangle text-warning fa-4x"></i></div>' +
                      '<p>You have only <strong>{{ $remainingOrders }}</strong> orders remaining out of your daily limit of {{ $dailyOrderLimit }}.</p>' +
                      '<p>Mass ordering may exceed your limit.</p>' +
                      '<div class="progress mt-3" style="height: 10px;">' +
                      '  <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $orderLimitPercentage }}%"></div>' +
                      '</div>',
                icon: 'warning',
                confirmButtonText: 'Understood',
                confirmButtonColor: '#ffc107'
            });
        @endif

        const linksTextarea = document.getElementById('links');
        const totalOrdersElement = document.getElementById('totalOrders');
        const totalQuantityElement = document.getElementById('totalQuantity');
        const totalAmountElement = document.getElementById('totalAmount');
        const remainingBalanceElement = document.getElementById('remainingBalance');
        const submitButton = document.getElementById('submitButton');
        const currentBalance = {{ auth()->user()->balance }};
        const serviceRows = document.querySelectorAll('.service-row');
        const massOrderForm = document.getElementById('massOrderForm');
        const dailyOrderLimit = {{ auth()->user()->daily_order_limit }};
        const usedOrdersToday = {{ \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count() }};
        const remainingOrdersToday = dailyOrderLimit - usedOrdersToday;
        
        // Make service rows clickable to add to textarea
        serviceRows.forEach(row => {
            row.addEventListener('click', function() {
                // Add a new line with the link template
                const currentText = linksTextarea.value;
                const newLine = currentText ? '\n' : '';
                linksTextarea.value += `${newLine}https://`;
                
                // Focus and move cursor to end
                linksTextarea.focus();
                linksTextarea.setSelectionRange(linksTextarea.value.length, linksTextarea.value.length);
                
                // Trigger calculation
                calculateTotals();
            });
        });
        
        // Calculate totals when links textarea or quantity changes
        linksTextarea.addEventListener('input', calculateTotals);
        document.getElementById('quantity').addEventListener('input', calculateTotals);
        
        // Initial calculation
        calculateTotals();
        
        function calculateTotals() {
            const lines = linksTextarea.value.trim().split('\n').filter(line => line.trim() !== '');
            const quantity = parseInt(document.getElementById('quantity').value) || 0;
            
            let totalOrders = lines.length;
            let totalQuantity = totalOrders * quantity;
            let totalAmount = 0;
            
            // Get the current service price
            const servicePrice = {{ $service->price }};
            const customRate = {{ auth()->user()->custom_rate ?? 'null' }};
            const price = customRate !== null ? customRate : servicePrice;
                    
            // Calculate total cost
            totalAmount = (totalQuantity * price) / 1000;
            
            // Update display
            totalOrdersElement.textContent = totalOrders.toLocaleString();
            totalQuantityElement.textContent = totalQuantity.toLocaleString();
            totalAmountElement.textContent = '$' + totalAmount.toFixed(2);
            
            // Calculate remaining balance
            const remainingBalance = currentBalance - totalAmount;
            remainingBalanceElement.textContent = '$' + remainingBalance.toFixed(2);
            
            // Update remaining balance color based on value
            remainingBalanceElement.className = remainingBalance < 0 ? 'fw-bold text-danger' : 'fw-bold text-primary';
            
            // Disable submit button if remaining balance is negative or no orders
            submitButton.disabled = remainingBalance < 0 || totalOrders === 0 || quantity < {{ $service->min_quantity }} || quantity > {{ $service->max_quantity }};
            
            if (remainingBalance < 0) {
                submitButton.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Insufficient Balance';
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-danger');
            } else if (totalOrders === 0) {
                submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Place Mass Order';
                submitButton.classList.remove('btn-danger');
                submitButton.classList.add('btn-primary');
            } else if (quantity < {{ $service->min_quantity }} || quantity > {{ $service->max_quantity }}) {
                submitButton.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Invalid Quantity';
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-danger');
            } else {
                submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Place Mass Order';
                submitButton.classList.remove('btn-danger');
                submitButton.classList.add('btn-primary');
            }
            
            // Check if orders would exceed daily limit
            if (dailyOrderLimit > 0 && totalOrders + usedOrdersToday > dailyOrderLimit) {
                const exceedBy = totalOrders + usedOrdersToday - dailyOrderLimit;
                const warningElement = document.getElementById('dailyLimitWarning');
                
                if (!warningElement) {
                    const warning = document.createElement('div');
                    warning.id = 'dailyLimitWarning';
                    warning.className = 'alert alert-warning mt-3';
                    warning.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Warning: These orders will exceed your daily limit by <strong>${exceedBy}</strong> orders.`;
                    submitButton.parentNode.appendChild(warning);
                } else {
                    warningElement.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i> Warning: These orders will exceed your daily limit by <strong>${exceedBy}</strong> orders.`;
                }
            } else {
                const warningElement = document.getElementById('dailyLimitWarning');
                if (warningElement) {
                    warningElement.remove();
                }
            }
        }
        
        // Form submission validation
        massOrderForm.addEventListener('submit', function(event) {
            const lines = linksTextarea.value.trim().split('\n').filter(line => line.trim() !== '');
            
            if (lines.length === 0) {
                event.preventDefault();
                Swal.fire({
                    title: 'No Orders',
                    text: 'Please enter at least one order.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }
            
            // Check if any line has invalid format
            let hasInvalidFormat = false;
            let invalidLines = [];
            
            lines.forEach((line, index) => {
                if (!line.trim() || !line.includes('http')) {
                    hasInvalidFormat = true;
                    invalidLines.push(`Line ${index + 1}: ${line}`);
                }
            });
            
            if (hasInvalidFormat) {
                event.preventDefault();
                Swal.fire({
                    title: 'Invalid Format',
                    html: 'The following lines have invalid format:<br><br>' +
                          '<code>' + invalidLines.join('<br>') + '</code><br><br>' +
                          'Please enter valid URLs.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }
            
            // Calculate total amount
            const totalAmountText = totalAmountElement.textContent.replace('$', '');
            const totalAmount = parseFloat(totalAmountText);
            
            // Check if user has sufficient balance
            if (totalAmount > currentBalance) {
                event.preventDefault();
                Swal.fire({
                    title: 'Insufficient Balance',
                    html: `You need <strong>$${totalAmount.toFixed(2)}</strong> but your current balance is <strong>$${currentBalance.toFixed(2)}</strong>.<br><br>Please add funds to your account.`,
                    icon: 'error',
                    confirmButtonText: 'Add Funds',
                    confirmButtonColor: '#4e73df',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("funds.add") }}';
                    }
                });
                return false;
            }
            
            // Check if total orders would exceed daily limit
            if (dailyOrderLimit > 0 && lines.length + usedOrdersToday > dailyOrderLimit) {
                event.preventDefault();
                const exceedBy = lines.length + usedOrdersToday - dailyOrderLimit;
                
                Swal.fire({
                    title: 'Daily Order Limit Warning',
                    html: `These orders will exceed your daily limit by <strong>${exceedBy}</strong> orders.<br><br>Do you want to proceed anyway?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Proceed',
                    confirmButtonColor: '#ffc107',
                    cancelButtonText: 'Cancel',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form
                        massOrderForm.submit();
                    }
                });
                return false;
            }
            
            // If we got here, form submission can proceed
            return true;
        });
    });
</script>
@endpush 