@extends('frontend.layouts.master')

@section('title', 'Create Mass Order')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create Mass Order - {{ $service->name }}</h4>
                </div>
                <div class="card-body">
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

                        <div class="alert alert-info">
                            <strong>Price per 1000:</strong> 
                            @if(auth()->user()->custom_rate)
                                <span class="text-success">${{ number_format(auth()->user()->custom_rate, 4) }}</span>
                                <small class="text-muted">(Custom rate)</small>
                            @else
                                ${{ number_format($service->price, 2) }}
                            @endif
                            <br>
                            <strong>Service Description:</strong> {{ $service->description }}<br>
                            <strong>Daily Order Limit:</strong> {{ auth()->user()->daily_order_limit - \App\Models\Order::where('user_id', auth()->id())->whereDate('created_at', now()->toDateString())->count() }} of {{ auth()->user()->daily_order_limit }} remaining today
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Place Mass Order
                            </button>
                            <a href="{{ route('services') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 