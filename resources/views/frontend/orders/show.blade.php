@extends('frontend.layouts.master')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Order Details</h6>
                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : 'warning') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Order Information</h5>
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Service:</strong> {{ $order->service->name }}</p>
                            <p><strong>Quantity:</strong> {{ number_format($order->quantity) }}</p>
                            <p><strong>Price per 1000:</strong> ${{ number_format($order->price, 2) }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Target Information</h5>
                            <p>
                                <strong>Link:</strong> 
                                <div class="d-flex align-items-center">
                                    <a href="{{ $order->link }}" target="_blank" rel="noopener noreferrer" class="text-primary">
                                        {{ $order->link }}
                                        <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $order->link }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </p>
                            <p>
                                <strong>Link UID:</strong> 
                                <div class="d-flex align-items-center">
                                    <code>{{ $order->link_uid ?? 'Not available' }}</code>
                                    @if($order->link_uid)
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $order->link_uid }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    @endif
                                </div>
                            </p>
                            <p><strong>Start Count:</strong> {{ number_format($order->start_count ?? 0) }}</p>
                            <p><strong>Remaining:</strong> {{ number_format($order->remains ?? 0) }}</p>
                            @if($order->start_count && $order->remains)
                                @php
                                    $completed = $order->start_count - $order->remains;
                                    $percentage = ($completed / $order->start_count) * 100;
                                @endphp
                                <div class="mt-3">
                                    <p><strong>Progress:</strong></p>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-{{ 
                                            $order->status === 'completed' ? 'success' : 
                                            ($order->status === 'processing' ? 'info' : 'warning') 
                                        }}" 
                                        role="progressbar" 
                                        style="width: {{ $percentage }}%" 
                                        aria-valuenow="{{ $percentage }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ number_format($completed) }}/{{ number_format($order->start_count) }} completed
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($order->description)
                    <div class="mb-4">
                        <h5 class="mb-3">Description</h5>
                        <p>{{ $order->description }}</p>
                    </div>
                    @endif

                    @if($order->error_message)
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Error Message</h5>
                        <p class="mb-0">{{ $order->error_message }}</p>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('services') }}" class="btn btn-primary">Back to Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show a temporary tooltip or alert
        alert('Copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}
</script>
@endpush 