@extends('frontend.layouts.master')

@section('title', 'Mass Order Details')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Mass Order Details</h4>
                    <div>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-sm me-2">All Orders</a>
                        <a href="{{ route('services') }}" class="btn btn-outline-light btn-sm">New Order</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <strong>Total Orders Created:</strong> {{ $orders->count() }}
                        <br>
                        <strong>Total Amount:</strong> ${{ number_format($orders->sum('total_amount'), 2) }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Service</th>
                                    <th>Link</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->service->name }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ $order->link }}" target="_blank" class="text-truncate" style="max-width: 200px;">
                                                    {{ $order->link }}
                                                </a>
                                                <button class="btn btn-sm btn-link p-0 ms-2" onclick="navigator.clipboard.writeText('{{ $order->link }}')" title="Copy link">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ number_format($order->quantity) }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'completed' ? 'success' : 'danger') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                                    <td colspan="4"><strong>${{ number_format($orders->sum('total_amount'), 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show a tooltip when link is copied
    document.querySelectorAll('[onclick*="clipboard"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const link = this.previousElementSibling.textContent.trim();
            navigator.clipboard.writeText(link).then(() => {
                // Visual feedback
                const icon = this.querySelector('i');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                }, 1500);
            });
        });
    });
</script>
@endpush
@endsection 