@extends('layouts.admin')

@section('title', 'Payments Management')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-money-bill-wave me-2 text-primary"></i>Payments Management
    </h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Total Revenue</h6>
                <h3 class="mb-0">${{ number_format($totalRevenue, 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Completed</h6>
                <h3 class="mb-0">{{ $completedPayments }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Pending</h6>
                <h3 class="mb-0">{{ $pendingPayments }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Failed</h6>
                <h3 class="mb-0">{{ $failedPayments }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Payment Breakdown -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Monthly Subscriptions</h6>
            </div>
            <div class="card-body">
                <h4>${{ number_format($paymentsByType['contract_monthly'], 0) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">One-Time Payments</h6>
            </div>
            <div class="card-body">
                <h4>${{ number_format($paymentsByType['contract_one_time'], 0) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0">Additional Sessions</h6>
            </div>
            <div class="card-body">
                <h4>${{ number_format($paymentsByType['additional_session'], 0) }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>All Payments
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Program</th>
                        <th>Payment Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Stripe ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}
                            <br>
                            <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <strong>{{ $payment->userProgram->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $payment->userProgram->user->email }}</small>
                        </td>
                        <td>
                            <strong>{{ $payment->userProgram->program->name }}</strong>
                            <br>
                            <span class="badge bg-info">{{ $payment->userProgram->program->formatted_subscription_type }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $payment->payment_type_display }}</span>
                            @if($payment->month_number)
                                <br>
                                <small class="text-muted">Month {{ $payment->month_number }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>${{ number_format($payment->amount, 0) }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $payment->status_badge_color }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>
                            <code class="small">{{ $payment->payment_reference }}</code>
                        </td>
                        <td>
                            @if($payment->stripe_payment_intent_id)
                                <small class="text-muted">
                                    <i class="fas fa-credit-card me-1"></i>
                                    {{ substr($payment->stripe_payment_intent_id, -12) }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->notes)
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="tooltip" 
                                        title="{{ $payment->notes }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No payments found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>

@endsection

