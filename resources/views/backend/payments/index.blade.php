@extends('backend.layouts.master')

@section('title', 'Payments')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Payments</h1>
        <div>
            <a href="{{ route('admin.payments.index') }}" class="btn {{ request()->routeIs('admin.payments.index') ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                <i class="fas fa-list me-1"></i> All Payments
            </a>
            <a href="{{ route('admin.payments.pending') }}" class="btn {{ request()->routeIs('admin.payments.pending') ? 'btn-warning' : 'btn-outline-warning' }}">
                <i class="fas fa-clock me-1"></i> Pending Payments
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-money-bill me-1"></i>
                    Payments List
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Transaction Ref</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td><code>{{ $payment->id }}</code></td>
                                <td>
                                    <a href="{{ route('admin.users.show', $payment->user) }}" class="text-decoration-none">
                                        {{ $payment->user->name }}
                                    </a>
                                </td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_method_label }}</td>
                                <td>
                                    <code>{{ $payment->transaction_reference }}</code>
                                </td>
                                <td>
                                    {!! $payment->status_badge !!}
                                </td>
                                <td>{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.payments.show', $payment) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($payment->status === 'pending')
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} payments
                    </div>
                    <div>
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 