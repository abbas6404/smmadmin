@extends('frontend.layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Balance Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ACCOUNT BALANCE</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format(auth()->user()->balance, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">MY ORDERS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ auth()->user()->orders()->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spent Amount Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">TOTAL SPENT</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format(auth()->user()->orders()->where('status', '!=', 'cancelled')->sum('total_amount'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ACTIVE ORDERS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ auth()->user()->orders()->where('status', 'processing')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(auth()->user()->orders()->latest()->take(5)->get() as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->service->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No orders found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }
    .btn-block {
        display: block;
        width: 100%;
    }
    .chart-area {
        position: relative;
        height: 100%;
        min-height: 300px;
    }
    .generate-report {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .generate-report:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
</style>
@endpush 