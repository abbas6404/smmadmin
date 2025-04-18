@extends('backend.layouts.master')

@section('title', 'Service Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Service Details</h1>
        <div>
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Edit Service
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-info-circle me-1"></i>
                    Service Information
                </div>
                <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'danger' }}">
                    {{ ucfirst($service->status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h5 class="text-muted mb-1">Service ID</h5>
                    <p class="mb-3"><code>{{ $service->id }}</code></p>

                    <h5 class="text-muted mb-1">Name</h5>
                    <p class="mb-3">{{ $service->name }}</p>

                    <h5 class="text-muted mb-1">Category</h5>
                    <p class="mb-3">{{ $service->category }}</p>

                    <h5 class="text-muted mb-1">Price</h5>
                    <p class="mb-3">${{ number_format($service->price, 4) }} per item</p>
                </div>

                <div class="col-md-6 mb-3">
                    <h5 class="text-muted mb-1">Minimum Quantity</h5>
                    <p class="mb-3">{{ number_format($service->min_quantity) }}</p>

                    <h5 class="text-muted mb-1">Maximum Quantity</h5>
                    <p class="mb-3">{{ number_format($service->max_quantity) }}</p>

                    <h5 class="text-muted mb-1">Created At</h5>
                    <p class="mb-3">{{ $service->created_at->format('F j, Y H:i:s') }}</p>

                    <h5 class="text-muted mb-1">Last Updated</h5>
                    <p class="mb-3">{{ $service->updated_at->format('F j, Y H:i:s') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <h5 class="text-muted mb-1">Description</h5>
                    <p class="mb-0">{{ $service->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-line me-1"></i>
            Service Statistics
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Total Orders</div>
                                    <div class="fs-4">{{ $service->orders_count ?? 0 }}</div>
                                </div>
                                <i class="fas fa-shopping-cart fa-2x opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Completed Orders</div>
                                    <div class="fs-4">{{ $service->completed_orders_count ?? 0 }}</div>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small">Pending Orders</div>
                                    <div class="fs-4">{{ $service->pending_orders_count ?? 0 }}</div>
                                </div>
                                <i class="fas fa-clock fa-2x opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 