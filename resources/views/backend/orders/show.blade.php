@extends('backend.layouts.master')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Order Details</h1>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Orders
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
                <i class="fas fa-edit me-1"></i> Update Status
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle me-1"></i>
                            Order Information
                        </div>
                        <span class="badge bg-{{ 
                            $order->status === 'completed' ? 'success' : 
                            ($order->status === 'processing' ? 'primary' : 
                            ($order->status === 'cancelled' ? 'danger' : 'warning'))
                        }} fs-6">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Order Details</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="35%">Order ID:</th>
                                    <td><code>{{ $order->id }}</code></td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At:</th>
                                    <td>{{ $order->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @if($order->status === 'completed')
                                <tr>
                                    <th>Completed At:</th>
                                    <td>{{ $order->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Service Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="35%">Service:</th>
                                    <td>{{ $order->service ? $order->service->name : 'Deleted Service' }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $order->service ? $order->service->category : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td>
                                        @if($order->service)
                                            <div class="mb-1">
                                                <strong>Service Standard Rate:</strong> 
                                                ${{ number_format($order->service->price, 4) }} per 1000
                                            </div>
                                        @else
                                            <div class="mb-1">
                                                <strong>Service Standard Rate:</strong> 
                                                <span class="text-muted">Unknown (service deleted)</span>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>Applied Order Rate:</strong> 
                                            ${{ number_format($order->price, 4) }} per 1000
                                            @if($order->user && $order->user->custom_rate && $order->price == $order->user->custom_rate)
                                                <span class="badge bg-info">Custom user rate</span>
                                            @elseif($order->service && $order->price == $order->service->price)
                                                <span class="badge bg-secondary">Standard rate</span>
                                            @endif
                                        </div>
                                        @if($order->user && $order->user->custom_rate && $order->price != $order->user->custom_rate)
                                            <div class="mt-1 text-warning">
                                                <small><i class="fas fa-exclamation-triangle me-1"></i> User has custom rate (${{ number_format($order->user->custom_rate, 4) }}) but it was not applied to this order</small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Min/Max:</th>
                                    <td>
                                        @if($order->service)
                                            {{ number_format($order->service->min_quantity) }} - {{ number_format($order->service->max_quantity) }}
                                        @else
                                            0 - 0
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Order Progress</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="35%">Quantity:</th>
                                    <td>{{ number_format($order->quantity) }}</td>
                                </tr>
                                <tr>
                                    <th>Start Count:</th>
                                    <td>{{ number_format($order->start_count ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <th>Remains:</th>
                                    <td>{{ number_format($order->remains ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <th>Progress:</th>
                                    <td>
                                        @php
                                            $progress = $order->start_count && $order->quantity 
                                                ? (($order->quantity - $order->remains) / $order->quantity) * 100 
                                                : 0;
                                            $completed = $order->quantity - ($order->remains ?? 0);
                                        @endphp
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-{{ 
                                                $order->status === 'completed' ? 'success' : 
                                                ($order->status === 'processing' ? 'primary' : 
                                                ($order->status === 'cancelled' ? 'danger' : 'warning'))
                                            }}" 
                                                role="progressbar" 
                                                style="width: {{ $progress }}%"
                                                aria-valuenow="{{ $progress }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ number_format($progress, 1) }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ number_format($completed) }} / {{ number_format($order->quantity) }} completed
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Payment Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="35%">Amount:</th>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Link:</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ $order->link }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                                <code class="small text-break">{{ $order->link }}</code>
                                                <i class="fas fa-external-link-alt ms-1 small"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $order->link }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning ms-1" data-bs-toggle="modal" data-bs-target="#editLinkModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Link UID:</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <code>{{ $order->link_uid ?? 'Not available' }}</code>
                                            @if($order->link_uid)
                                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $order->link_uid }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-warning ms-1" data-bs-toggle="modal" data-bs-target="#editUidModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>User Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="35%">Name:</th>
                                    <td>
                                        @if($order->user)
                                            <a href="{{ route('admin.users.show', $order->user->id) }}" class="text-decoration-none">
                                                {{ $order->user->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Deleted User</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $order->user ? $order->user->email : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Custom Rate:</th>
                                    <td>
                                        @if($order->user && $order->user->custom_rate)
                                            <span class="text-success">${{ number_format($order->user->custom_rate, 4) }}</span>
                                            @if($order->price == $order->user->custom_rate)
                                                <span class="badge bg-info">Applied to this order</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Daily Order Limit:</th>
                                    <td>
                                        @if($order->user)
                                            {{ $order->user->daily_order_limit }} orders/day
                                            @php
                                                $todayOrderCount = App\Models\Order::where('user_id', $order->user->id)
                                                    ->whereDate('created_at', now()->toDateString())
                                                    ->count();
                                            @endphp
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar {{ $todayOrderCount >= $order->user->daily_order_limit ? 'bg-danger' : 'bg-success' }}" 
                                                    style="width: {{ min(100, ($todayOrderCount / $order->user->daily_order_limit) * 100) }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $todayOrderCount }} used today</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Order Timeline
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Order Created</h6>
                                <p class="timeline-text">{{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        @if($order->status !== 'pending')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Status Updated to {{ ucfirst($order->status) }}</h6>
                                    <p class="timeline-text">{{ $order->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($relatedOrders->count() > 0)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Related Orders
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($relatedOrders as $relatedOrder)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $relatedOrder) }}" class="text-decoration-none">
                                            <code>{{ $relatedOrder->id }}</code>
                                        </a>
                                    </td>
                                    <td>{{ $relatedOrder->service ? $relatedOrder->service->name : 'Deleted Service' }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $relatedOrder->status === 'completed' ? 'success' : 
                                            ($relatedOrder->status === 'processing' ? 'primary' : 
                                            ($relatedOrder->status === 'cancelled' ? 'danger' : 'warning'))
                                        }}">
                                            {{ ucfirst($relatedOrder->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $relatedOrder->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <input type="text" class="form-control" value="{{ ucfirst($order->status) }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ old('status', $order->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-top: 6px;
}

.timeline-content {
    padding-bottom: 20px;
    border-bottom: 1px solid #e9ecef;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 0;
    color: #6c757d;
    font-size: 0.875rem;
}

.text-break {
    word-break: break-all;
}
</style>

<!-- Edit Link Modal -->
<div class="modal fade" id="editLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.update-link', $order) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Link</label>
                        <input type="text" class="form-control" value="{{ $order->link }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Link</label>
                        <textarea name="link" class="form-control" required rows="3">{{ old('link', $order->link) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit UID Modal -->
<div class="modal fade" id="editUidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.update-uid', $order) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order UID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current UID</label>
                        <input type="text" class="form-control" value="{{ $order->link_uid ?? 'Not available' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New UID</label>
                        <input type="text" name="link_uid" class="form-control" value="{{ old('link_uid', $order->link_uid) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update UID</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link copied to clipboard!');
    });
}
</script>
@endsection 