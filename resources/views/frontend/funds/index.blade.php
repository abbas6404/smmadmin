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

<!-- Balance History -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Balance History</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Previous Balance</th>
                        <th>New Balance</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($balanceHistory as $history)
                    <tr>
                        <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $history->type === 'credit' ? 'success' : 'danger' }}">
                                {{ ucfirst($history->type) }}
                            </span>
                        </td>
                        <td>${{ number_format($history->amount, 2) }}</td>
                        <td>${{ number_format($history->previous_balance, 2) }}</td>
                        <td>${{ number_format($history->new_balance, 2) }}</td>
                        <td>{{ $history->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No transactions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($balanceHistory->hasPages())
    <div class="card-footer pb-0">
        {{ $balanceHistory->links() }}
    </div>
    @endif
</div>
@endsection 