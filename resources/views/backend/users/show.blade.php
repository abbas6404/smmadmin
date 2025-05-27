@extends('backend.layouts.master')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Details</h1>
        <div>
            <a href="{{ route('admin.users.add-funds-form', $user) }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Add Funds
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- User Information -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" style="width: 150px; height: 150px;">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Balance</th>
                                <td>${{ number_format($user->balance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($user->status)
                                        @if($user->status === 'active')
                                            <span style="color: #0d6efd !important; font-weight: 600; font-size: 13px;">Active</span>
                                        @else
                                            <span style="color: #dc3545 !important; font-weight: 600; font-size: 13px;">Inactive</span>
                                        @endif
                                    @else
                                        <span style="color: #dc3545 !important; font-weight: 600; font-size: 13px;">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Custom Rate</th>
                                <td>{{ $user->custom_rate ? number_format($user->custom_rate, 4) : 'Default' }}</td>
                            </tr>
                            <tr>
                                <th>Daily Order Limit</th>
                                <td>{{ $user->daily_order_limit }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $user->created_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $user->updated_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="col-xl-8 col-lg-7">
            <div class="row">
                <!-- Total Orders -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->orders->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Orders</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->orders->where('status', 'completed')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Spent</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($user->orders->where('status', '!=', 'cancelled')->sum('total_amount'), 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders->take(5) as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->service->name }}</td>
                                    <td>
                                        @if($order->status === 'completed')
                                            <span style="color: #198754; font-weight: 600; font-size: 13px;">Completed</span>
                                        @elseif($order->status === 'pending')
                                            <span style="color: #ffc107; font-weight: 600; font-size: 13px;">Pending</span>
                                        @elseif($order->status === 'processing')
                                            <span style="color: #0dcaf0; font-weight: 600; font-size: 13px;">Processing</span>
                                        @elseif($order->status === 'cancelled')
                                            <span style="color: #dc3545; font-weight: 600; font-size: 13px;">Cancelled</span>
                                        @else
                                            <span style="color: #6c757d; font-weight: 600; font-size: 13px;">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 4px;
    }
    .badge-primary {
        background-color: #0d6efd;
        color: white;
    }
    .badge-success {
        background-color: #198754;
        color: white;
    }
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #000;
    }
    .badge-info {
        background-color: #0dcaf0;
        color: #000;
    }
</style>
@endpush
@endsection 