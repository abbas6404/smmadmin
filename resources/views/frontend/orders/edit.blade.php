@extends('frontend.layouts.master')

@section('title', 'Edit Order')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back to order details button -->
            <div class="mb-4">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Back to Order Details
                </a>
            </div>
            
            <!-- Order Status Alert -->
            <div class="alert alert-warning mb-4 shadow-sm border-0 rounded-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading">Editing Order #{{ $order->id }}</h5>
                        <p class="mb-0">You can only edit orders with "Pending" status. Once the order is processed, it cannot be modified.</p>
                    </div>
                </div>
            </div>
            
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
                        <i class="fas fa-edit me-2"></i>Edit Order
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

                    <form action="{{ route('orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label for="link" class="form-label fw-medium">Link <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-link text-primary"></i>
                                </span>
                                <input type="url" class="form-control @error('link') is-invalid @enderror border-start-0 ps-0" 
                                    id="link" name="link" 
                                    placeholder="Enter your target link" 
                                    value="{{ old('link', $order->link) }}" required>
                                @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i> Enter the full URL including https://
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="form-label fw-medium">Quantity</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-hashtag text-primary"></i>
                                </span>
                                <input type="text" class="form-control bg-light border-start-0 ps-0" 
                                    id="quantity" value="{{ number_format($order->quantity) }}" readonly>
                                <input type="hidden" name="quantity" value="{{ $order->quantity }}">
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i> Quantity cannot be modified after order is placed
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="link_uid" class="form-label fw-medium">Link UID (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fas fa-fingerprint text-primary"></i>
                                </span>
                                <input type="text" class="form-control @error('link_uid') is-invalid @enderror border-start-0 ps-0" 
                                    id="link_uid" name="link_uid" 
                                    placeholder="Enter UID if available" 
                                    value="{{ old('link_uid', $order->link_uid) }}">
                                @error('link_uid')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i> For Facebook links, this is the profile/page ID
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
                                                <div class="fw-bold" id="summaryQuantity">{{ number_format($order->quantity) }}</div>
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
                                                <div class="fw-bold text-primary">$<span id="totalAmount">{{ number_format($order->total_amount, 2) }}</span></div>
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
                                            <div class="fw-bold text-primary">$<span id="remainingBalance">{{ number_format(auth()->user()->balance - ($order->quantity != old('quantity', $order->quantity) ? (old('quantity', $order->quantity) * ($service->price / 1000)) : 0), 2) }}</span></div>
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
                                    placeholder="Add any specific instructions or notes here">{{ old('description', $order->description) }}</textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitButton">
                                <i class="fas fa-save me-2"></i> Update Order
                            </button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
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
        // Quantity is now read-only, no need for calculation
        const totalAmountSpan = document.getElementById('totalAmount');
        const remainingBalanceSpan = document.getElementById('remainingBalance');
        
        // Display fixed values since quantity can't be changed
        totalAmountSpan.textContent = '{{ number_format($order->total_amount, 2) }}';
        remainingBalanceSpan.textContent = '{{ number_format(auth()->user()->balance, 2) }}';
    });
</script>
@endpush 