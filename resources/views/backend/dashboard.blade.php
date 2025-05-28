@extends('backend.layouts.master')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="d-flex gap-2">
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
            </a>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-sync fa-sm text-white-50"></i> Refresh
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div style="display: none;" class="row">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Completed Orders -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($todayCompletedOrders) }}</div>
                            <div class="text-xs text-muted mt-1">
                                <div>Quantity: {{ number_format($todayCompletedQuantity) }}</div>
                                @if($yesterdayCompletedOrders > 0)
                                    @php
                                        $change = (($todayCompletedOrders - $yesterdayCompletedOrders) / $yesterdayCompletedOrders) * 100;
                                        $quantityChange = (($todayCompletedQuantity - $yesterdayCompletedQuantity) / $yesterdayCompletedQuantity) * 100;
                                    @endphp
                                    <div class="mt-1">
                                        <span class="{{ $change >= 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-{{ $change >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                            {{ abs(round($change, 1)) }}%
                                        </span>
                                        orders vs yesterday
                                    </div>
                                    <div>
                                        <span class="{{ $quantityChange >= 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-{{ $quantityChange >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                            {{ abs(round($quantityChange, 1)) }}%
                                        </span>
                                        quantity vs yesterday
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Completion Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completionRate }}%</div>
                            <div class="text-xs text-muted mt-1">
                                {{ number_format($completedOrders) }} of {{ number_format($totalOrders) }} orders
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending Payments -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pending Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingPayments) }}</div>
                            <div class="text-xs text-muted mt-1">
                                <a href="{{ route('admin.payments.pending') }}" class="text-danger">View Pending Payments</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Accounts Stats -->
    <div class="row">
        <!-- Active Gmail Accounts -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Active Gmail Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeGmailAccounts) }}</div>
                            <div class="text-xs text-muted mt-1">
                                <a href="{{ route('admin.gmail.index') }}" class="text-danger">View All Gmail Accounts</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Facebook Accounts -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Facebook Accounts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeFacebookAccounts) }}</div>
                            <div class="text-xs text-muted mt-1">
                                <a href="{{ route('admin.facebook.index') }}" class="text-primary">View All Facebook Accounts</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-facebook fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Chrome Profiles -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Chrome Profiles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeChromeProfiles) }}</div>
                            <div class="text-xs text-muted mt-1">
                                <a href="{{ route('admin.chrome.index') }}" class="text-info">View All Chrome Profiles</a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-chrome fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Facebook Account Usage Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Facebook Account Usage Status</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="fbStatusDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="fbStatusDropdown">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('admin.facebook.index') }}">View All Accounts</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" id="refreshFbStatus">Refresh Status</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="h5 mb-3">Reset Status</div>
                            <div class="mb-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Last Reset Time</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800" id="lastResetTime">
                                    @php
                                        $lastRun = App\Models\Setting::where('key', 'facebook_reset_last_run')->first();
                                        $lastRunTime = $lastRun ? $lastRun->value : 'Never';
                                        $timeAgo = $lastRunTime !== 'Never' ? \Carbon\Carbon::parse($lastRunTime)->diffForHumans() : '';
                                    @endphp
                                    {{ $lastRunTime }} 
                                    @if($timeAgo)
                                        <span class="text-muted">({{ $timeAgo }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Daily Usage Limit</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        $limit = App\Models\Setting::where('key', 'facebook_account_daily_use_limit')->first();
                                        $dailyLimit = $limit ? $limit->value : 0;
                                    @endphp
                                    {{ $dailyLimit }} uses per account
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="h5 mb-3">Account Stats</div>
                            @php
                                $totalAccounts = App\Models\FacebookAccount::count();
                                $usedAccounts = App\Models\FacebookAccount::where('use_count', '>', 0)->count();
                                $maxedOutAccounts = App\Models\FacebookAccount::where('use_count', '>=', $dailyLimit)->count();
                                $availableAccounts = $totalAccounts - $maxedOutAccounts;
                                
                                // Calculate average use count for accounts with uses
                                $avgUseCount = $usedAccounts > 0 
                                    ? App\Models\FacebookAccount::where('use_count', '>', 0)->avg('use_count') 
                                    : 0;
                            @endphp
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Accounts</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAccounts) }}</div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Have Uses</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($usedAccounts) }}
                                        <small class="text-muted">({{ $totalAccounts > 0 ? round(($usedAccounts / $totalAccounts) * 100) : 0 }}%)</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">At Limit</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($maxedOutAccounts) }}
                                        <small class="text-muted">({{ $totalAccounts > 0 ? round(($maxedOutAccounts / $totalAccounts) * 100) : 0 }}%)</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($availableAccounts) }}
                                        <small class="text-muted">({{ $totalAccounts > 0 ? round(($availableAccounts / $totalAccounts) * 100) : 0 }}%)</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average Uses</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($avgUseCount, 1) }} per account with uses
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h5 mb-3">Usage Distribution</div>
                            <div class="chart-container" style="position: relative; height:150px;">
                                <canvas id="fbUsageChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Completed Orders -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daily Completed Orders</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="dailyOrdersChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Orders</th>
                                    <th class="text-end">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailyCompletedOrders as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</td>
                                    <td class="text-end">{{ number_format($day->count) }}</td>
                                    <td class="text-end">{{ number_format($day->total_quantity) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments Section -->
    @if(count($pendingPaymentsList) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Pending Payments Requiring Attention
                    </h6>
                    <a href="{{ route('admin.payments.pending') }}" class="btn btn-sm btn-warning">
                        View All Pending Payments
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPaymentsList as $payment)
                                <tr>
                                    <td>#{{ $payment->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $payment->user) }}" class="text-decoration-none">
                                            {{ $payment->user->name }}
                                        </a>
                                    </td>
                                    <td>${{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_method_label }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.payments.show', $payment) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success" 
                                                        onclick="return confirm('Are you sure you want to approve this payment?')"
                                                        title="Approve Payment">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to reject this payment?')"
                                                        title="Reject Payment">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
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
    @endif

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-xl-6 col-lg-6">
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
                                    <th>User</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->service->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $order->status === 'completed' ? 'success' : 
                                            ($order->status === 'processing' ? 'info' : 
                                            ($order->status === 'cancelled' ? 'danger' : 'warning')) 
                                        }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent orders</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Payments</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>#{{ $payment->id }}</td>
                                    <td>{{ $payment->user ? $payment->user->name : 'Deleted User' }}</td>
                                    <td>${{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $payment->status === 'approved' ? 'success' : 
                                            ($payment->status === 'rejected' ? 'danger' : 'warning') 
                                        }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No recent payments</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($monthlyRevenue->pluck('revenue')) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                pointRadius: 3,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#4e73df',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: '#4e73df',
                pointHoverBorderColor: '#4e73df',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Daily Orders Chart
    const dailyOrdersCtx = document.getElementById('dailyOrdersChart').getContext('2d');
    new Chart(dailyOrdersCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailyCompletedOrders->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            })) !!},
            datasets: [{
                label: 'Completed Orders',
                data: {!! json_encode($dailyCompletedOrders->pluck('count')) !!},
                backgroundColor: '#1cc88a',
                borderColor: '#17a673',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Completed Quantity',
                data: {!! json_encode($dailyCompletedOrders->pluck('total_quantity')) !!},
                backgroundColor: '#36b9cc',
                borderColor: '#2ab7c9',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Orders'
                    },
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Quantity'
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
    
    // Facebook Account Usage Scripts
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Facebook usage chart
        initFacebookUsageChart();
        
        // Handle refresh button click
        document.getElementById('refreshFbStatus').addEventListener('click', function(e) {
            e.preventDefault();
            refreshFacebookStatus();
        });
    });
    
    function initFacebookUsageChart() {
        const ctx = document.getElementById('fbUsageChart').getContext('2d');
        
        // Get data from PHP
        @php
            $usageData = App\Models\FacebookAccount::selectRaw('use_count, COUNT(*) as count')
                ->groupBy('use_count')
                ->orderBy('use_count')
                ->get();
                
            $labels = [];
            $data = [];
            $backgroundColors = [];
            
            $limit = App\Models\Setting::where('key', 'facebook_account_daily_use_limit')->first();
            $dailyLimit = $limit ? (int)$limit->value : 0;
            
            foreach ($usageData as $item) {
                $labels[] = $item->use_count . ' Uses';
                $data[] = $item->count;
                
                // Set color based on usage vs limit
                if ($item->use_count >= $dailyLimit) {
                    $backgroundColors[] = 'rgba(231, 74, 59, 0.8)'; // Red for at limit
                } elseif ($item->use_count > 0) {
                    $backgroundColors[] = 'rgba(246, 194, 62, 0.8)'; // Yellow for used
                } else {
                    $backgroundColors[] = 'rgba(28, 200, 138, 0.8)'; // Green for unused
                }
            }
        @endphp
        
        window.fbUsageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Facebook Accounts',
                    data: {!! json_encode($data) !!},
                    backgroundColor: {!! json_encode($backgroundColors) !!},
                    borderColor: {!! json_encode($backgroundColors) !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    function refreshFacebookStatus() {
        // Show loading indicator
        const lastResetTimeEl = document.getElementById('lastResetTime');
        const originalText = lastResetTimeEl.innerHTML;
        lastResetTimeEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Refreshing...';
        
        // Make AJAX request to get fresh data
        fetch('/admin/facebook/status', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update the UI with new data
            if (data.success) {
                // Reload the page to refresh all stats
                window.location.reload();
            } else {
                // Show error
                lastResetTimeEl.innerHTML = originalText;
                alert('Error refreshing Facebook status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            lastResetTimeEl.innerHTML = originalText;
            alert('An error occurred while refreshing Facebook status');
        });
    }
</script>
@endpush
@endsection 