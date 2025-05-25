@extends('frontend.layouts.master')

@section('title', 'Manage Funds')

@section('content')
<!-- Page Header -->
<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Manage Funds</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('funds.add') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Add Funds
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Balance Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Current Balance</div>
                </div>
                <div class="h1 mb-0">${{ number_format(auth()->user()->balance, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Spent</div>
                </div>
                <div class="h1 mb-0">${{ number_format($totalSpent, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Manual Payments -->
@if(isset($pendingPayments) && count($pendingPayments) > 0)
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-clock text-warning me-2"></i>
            Pending Manual Payments
        </h3>
        <div class="card-actions">
            <a href="https://wa.me/+8801533651935?text=Hello,%20I%20want%20to%20check%20my%20pending%20payment%20status.%20Please%20assist%20me." target="_blank" class="btn btn-sm btn-success">
                <i class="fab fa-whatsapp me-1"></i>
                Contact Admin
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPayments as $payment)
                    <tr>
                        <td>{{ $payment->created_at ? date('M d, Y H:i', strtotime($payment->created_at)) : 'N/A' }}</td>
                        <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                        <td>
                            <span class="badge bg-blue">{{ ucfirst($payment->payment_method) }}</span>
                        </td>
                        <td>{{ $payment->transaction_reference }}</td>
                        <td>
                            <span class="badge bg-warning">Pending</span>
                        </td>
                        <td>
                            <a href="https://wa.me/+8801533651935?text=Hello,%20I%20want%20to%20check%20my%20pending%20payment%20(ID:%20{{ $payment->id }},%20Amount:%20${{ number_format($payment->amount, 2) }},%20Transaction:%20{{ $payment->transaction_reference }}).%20Please%20assist%20me." target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="fab fa-whatsapp me-1"></i>
                                Ask Status
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Balance History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Balance History</h3>
        <div class="card-actions">
            <form action="{{ route('funds.index') }}" method="GET" class="d-flex">
                <select name="type" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                    <option value="">All Transactions</option>
                    <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credits Only</option>
                    <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debits Only</option>
                </select>
                <a href="{{ route('funds.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-redo"></i>
                </a>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Previous Balance</th>
                        <th>New Balance</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($balanceHistory as $history)
                    <tr>
                        <td class="text-muted">#{{ $history->id }}</td>
                        <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $history->type === 'credit' ? 'success' : 'danger' }}">
                                {{ ucfirst($history->type) }}
                            </span>
                        </td>
                        <td class="font-weight-bold">
                            <span class="{{ $history->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                ${{ number_format($history->amount, 2) }}
                            </span>
                        </td>
                        <td>${{ number_format($history->previous_balance, 2) }}</td>
                        <td>${{ number_format($history->new_balance, 2) }}</td>
                        <td>
                            @if($history->reference_id)
                                <span class="text-muted">{{ $history->reference_id }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $history->description }}</td>
                        <td>
                            @if(isset($history->status))
                                @if($history->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($history->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($history->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($history->status) }}</span>
                                @endif
                            @else
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="fas fa-history fa-3x text-muted"></i>
                                </div>
                                <p class="empty-title">No transactions found</p>
                                <p class="empty-subtitle text-muted">
                                    You haven't made any transactions yet.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('funds.add') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add Funds
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($balanceHistory->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted">Showing <span>{{ $balanceHistory->firstItem() }}</span> to <span>{{ $balanceHistory->lastItem() }}</span> of <span>{{ $balanceHistory->total() }}</span> entries</p>
        <ul class="pagination m-0 ms-auto">
            <!-- Previous Page Link -->
            @if($balanceHistory->onFirstPage())
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                        <i class="fas fa-angle-left"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $balanceHistory->previousPageUrl() }}" tabindex="-1">
                        <i class="fas fa-angle-left"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            @endif

            <!-- Pagination Elements -->
            @foreach($balanceHistory->getUrlRange(max($balanceHistory->currentPage() - 2, 1), 
                                                min($balanceHistory->currentPage() + 2, $balanceHistory->lastPage())) as $page => $url)
                @if($page == $balanceHistory->currentPage())
                    <li class="page-item active">
                        <a class="page-link" href="#">{{ $page }}</a>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            <!-- Next Page Link -->
            @if($balanceHistory->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $balanceHistory->nextPageUrl() }}">
                        <i class="fas fa-angle-right"></i>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                        <i class="fas fa-angle-right"></i>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection 