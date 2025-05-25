@extends('frontend.layouts.master')

@section('title', 'Services')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Our Services</h1>
        <div class="d-flex">
            <div class="input-group me-3">
                <input type="text" class="form-control" placeholder="Search services..." id="searchInput">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" data-category="all">All Categories</a></li>
                    <li><a class="dropdown-item" href="#" data-category="facebook">Facebook</a></li>
                    <li><a class="dropdown-item" href="#" data-category="instagram">Instagram</a></li>
                    <li><a class="dropdown-item" href="#" data-category="twitter">Twitter</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- System Notification Alert -->
    @if($systemNotificationActive)
    <div class="alert alert-warning mb-4" role="alert">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-exclamation-triangle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading">Ordering Temporarily Unavailable</h4>
                <p class="mb-0">{{ $systemNotificationMessage }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Services Grid -->
    <div class="row" id="servicesGrid">
        @forelse($services as $service)
        <div class="col-xl-4 col-lg-6 mb-4 service-card" data-category="{{ $service->category }}">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $service->name }}</h5>
                    <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($service->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted">{{ $service->description }}</p>
                    <div class="service-details mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Price per 1000:</span>
                            <span class="fw-bold">${{ number_format($service->price, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Min Order:</span>
                            <span class="fw-bold">{{ number_format($service->min_quantity) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Max Order:</span>
                            <span class="fw-bold">{{ number_format($service->max_quantity) }}</span>
                        </div>
                    </div>
                    @if($service->requirements)
                    <div class="requirements mb-3">
                        <h6 class="text-muted mb-2">Requirements:</h6>
                        <ul class="list-unstyled">
                            @foreach(json_decode($service->requirements) as $requirement)
                            <li class="mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                {{ $requirement }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-grid gap-2">
                        @if($systemNotificationActive)
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-ban me-2"></i> Ordering Unavailable
                            </button>
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-ban me-2"></i> Mass Order Unavailable
                            </button>
                        @else
                            <a href="{{ route('orders.create', $service->id) }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i> Order Now
                            </a>
                            <a href="{{ route('orders.mass-create', $service->id) }}" class="btn btn-info">
                                <i class="fas fa-layer-group me-2"></i> Mass Order
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                No services available at the moment.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
    .service-card {
        transition: transform 0.2s;
    }
    .service-card:hover {
        transform: translateY(-5px);
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    .requirements ul li {
        font-size: 0.9rem;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    .btn-info {
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const servicesGrid = document.getElementById('servicesGrid');
        const serviceCards = document.querySelectorAll('.service-card');
        const filterLinks = document.querySelectorAll('.dropdown-item[data-category]');

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            serviceCards.forEach(card => {
                const serviceName = card.querySelector('h5').textContent.toLowerCase();
                const serviceDesc = card.querySelector('.card-text').textContent.toLowerCase();
                const isVisible = serviceName.includes(searchTerm) || serviceDesc.includes(searchTerm);
                card.style.display = isVisible ? 'block' : 'none';
            });
        });

        // Filter functionality
        filterLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const category = this.dataset.category;
                serviceCards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endpush 