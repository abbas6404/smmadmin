@extends('backend.layouts.master')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Payment Details</h1>
        <div>
            @if($payment->status === 'pending')
                <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="btn btn-success me-2" 
                            onclick="return confirm('Are you sure you want to approve this payment?')">
                        <i class="fas fa-check me-1"></i> Approve Payment
                    </button>
                </form>
                <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" 
                            class="btn btn-danger me-2" 
                            onclick="return confirm('Are you sure you want to reject this payment?')">
                        <i class="fas fa-times me-1"></i> Reject Payment
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle me-1"></i>
                            Payment Information
                        </div>
                        <div>
                            {!! $payment->status_badge !!}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="text-muted mb-1">Payment ID</h5>
                            <p class="mb-3"><code>{{ $payment->id }}</code></p>

                            <h5 class="text-muted mb-1">Amount</h5>
                            <p class="mb-3">${{ number_format($payment->amount, 2) }}</p>

                            <h5 class="text-muted mb-1">Payment Method</h5>
                            <p class="mb-3">{{ $payment->payment_method_label }}</p>

                            <h5 class="text-muted mb-1">Transaction Reference</h5>
                            <p class="mb-3"><code>{{ $payment->transaction_reference }}</code></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted mb-1">Sender Number</h5>
                            <p class="mb-3">{{ $payment->sender_number }}</p>

                            <h5 class="text-muted mb-1">Created At</h5>
                            <p class="mb-3">{{ $payment->created_at->format('F j, Y H:i:s') }}</p>

                            <h5 class="text-muted mb-1">Updated At</h5>
                            <p class="mb-3">{{ $payment->updated_at->format('F j, Y H:i:s') }}</p>
                        </div>
                    </div>

                    @if($payment->admin_note)
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-muted mb-1">Admin Note</h5>
                                <p class="mb-0">{{ $payment->admin_note }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($payment->payment_proof)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-image me-1"></i>
                        Payment Proof
                    </div>
                    <div class="card-body">
                        <img src="{{ asset('storage/' . $payment->payment_proof) }}" 
                             alt="Payment Proof" 
                             class="img-fluid">
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    User Information
                </div>
                <div class="card-body">
                    <h5 class="text-muted mb-1">Name</h5>
                    <p class="mb-3">
                        <a href="{{ route('admin.users.show', $payment->user) }}" class="text-decoration-none">
                            {{ $payment->user->name }}
                        </a>
                    </p>

                    <h5 class="text-muted mb-1">Email</h5>
                    <p class="mb-3">{{ $payment->user->email }}</p>

                    <h5 class="text-muted mb-1">Current Balance</h5>
                    <p class="mb-3">${{ number_format($payment->user->balance, 2) }}</p>

                    <h5 class="text-muted mb-1">Member Since</h5>
                    <p class="mb-0">{{ $payment->user->created_at->format('F j, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 