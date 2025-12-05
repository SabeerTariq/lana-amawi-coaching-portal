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
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#paymentDetailsModal{{ $payment->id }}">
                                <i class="fas fa-eye me-1"></i>View Details
                            </button>
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

<!-- Payment Details Modals -->
@foreach($payments as $payment)
<div class="modal fade" id="paymentDetailsModal{{ $payment->id }}" tabindex="-1" aria-labelledby="paymentDetailsModalLabel{{ $payment->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentDetailsModalLabel{{ $payment->id }}">
                    <i class="fas fa-money-bill-wave me-2"></i>Payment & Subscription Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Payment Details Section -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Payment ID:</strong><br>
                                <span class="text-muted">#{{ $payment->id }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Amount:</strong><br>
                                <span class="text-success fs-5 fw-bold">${{ number_format($payment->amount, 2) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Payment Type:</strong><br>
                                <span class="badge bg-secondary">{{ $payment->payment_type_display }}</span>
                                @if($payment->month_number)
                                    <span class="badge bg-info ms-1">Month {{ $payment->month_number }}</span>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $payment->status_badge_color }} fs-6">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Payment Date:</strong><br>
                                <span class="text-muted">
                                    {{ $payment->paid_at ? $payment->paid_at->format('F d, Y h:i A') : $payment->created_at->format('F d, Y h:i A') }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Created At:</strong><br>
                                <span class="text-muted">{{ $payment->created_at->format('F d, Y h:i A') }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Payment Reference:</strong><br>
                                <code class="small">{{ $payment->payment_reference }}</code>
                            </div>
                            @if($payment->stripe_payment_intent_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Payment Intent:</strong><br>
                                <code class="small">{{ $payment->stripe_payment_intent_id }}</code>
                            </div>
                            @endif
                            @if($payment->stripe_charge_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Charge ID:</strong><br>
                                <code class="small">{{ $payment->stripe_charge_id }}</code>
                            </div>
                            @endif
                            @if($payment->stripe_customer_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Customer ID:</strong><br>
                                <code class="small">{{ $payment->stripe_customer_id }}</code>
                            </div>
                            @endif
                            @if($payment->notes)
                            <div class="col-12 mb-3">
                                <strong>Notes:</strong><br>
                                <div class="alert alert-info mb-0">
                                    {{ $payment->notes }}
                                </div>
                            </div>
                            @endif
                            @if($payment->appointment)
                            <div class="col-12 mb-3">
                                <strong>Related Appointment:</strong><br>
                                <span class="text-muted">
                                    {{ $payment->appointment->appointment_date->format('F d, Y') }} at {{ $payment->appointment->appointment_time }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription/Program Details Section -->
                @php
                    $userProgram = $payment->userProgram;
                    $program = $userProgram->program ?? null;
                    $user = $userProgram->user ?? null;
                @endphp
                @if($userProgram)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>Client & Program Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Client Name:</strong><br>
                                <span class="text-muted">{{ $user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Client Email:</strong><br>
                                <span class="text-muted">{{ $user->email ?? 'N/A' }}</span>
                            </div>
                            @if($program)
                            <div class="col-md-6 mb-3">
                                <strong>Program Name:</strong><br>
                                <span class="badge bg-info">{{ $program->name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Program Type:</strong><br>
                                <span class="text-muted">{{ $program->formatted_subscription_type ?? 'N/A' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription Details Section -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Subscription Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Subscription Status:</strong><br>
                                <span class="badge bg-{{ $userProgram->status_badge_color ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $userProgram->status ?? 'N/A')) }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Payment Type:</strong><br>
                                <span class="text-muted">
                                    @if($userProgram->payment_type === 'monthly')
                                        Monthly Payments
                                    @elseif($userProgram->payment_type === 'one_time')
                                        One-Time Payment
                                    @else
                                        {{ ucfirst($userProgram->payment_type ?? 'N/A') }}
                                    @endif
                                </span>
                            </div>
                            @if($userProgram->contract_start_date)
                            <div class="col-md-6 mb-3">
                                <strong>Contract Start Date:</strong><br>
                                <span class="text-muted">{{ $userProgram->contract_start_date->format('F d, Y') }}</span>
                            </div>
                            @endif
                            @if($userProgram->contract_end_date)
                            <div class="col-md-6 mb-3">
                                <strong>Contract End Date:</strong><br>
                                <span class="text-muted">{{ $userProgram->contract_end_date->format('F d, Y') }}</span>
                            </div>
                            @endif
                            @if($userProgram->next_payment_date)
                            <div class="col-md-6 mb-3">
                                <strong>Next Payment Date:</strong><br>
                                <span class="text-warning fw-bold">{{ $userProgram->next_payment_date->format('F d, Y') }}</span>
                            </div>
                            @endif
                            @if($userProgram->contract_duration_months)
                            <div class="col-md-6 mb-3">
                                <strong>Contract Duration:</strong><br>
                                <span class="text-muted">{{ $userProgram->contract_duration_months }} months</span>
                            </div>
                            @endif
                            @if($userProgram->total_payments_due)
                            <div class="col-md-6 mb-3">
                                <strong>Total Payments Due:</strong><br>
                                <span class="text-muted">{{ $userProgram->total_payments_due }}</span>
                            </div>
                            @endif
                            @if($userProgram->payments_completed !== null)
                            <div class="col-md-6 mb-3">
                                <strong>Payments Completed:</strong><br>
                                <span class="text-success fw-bold">{{ $userProgram->payments_completed }} / {{ $userProgram->total_payments_due ?? 'N/A' }}</span>
                            </div>
                            @endif
                            @if($userProgram->amount_paid)
                            <div class="col-md-6 mb-3">
                                <strong>Total Amount Paid:</strong><br>
                                <span class="text-success fw-bold">${{ number_format($userProgram->amount_paid, 2) }}</span>
                            </div>
                            @endif
                            @if($userProgram->stripe_subscription_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Subscription ID:</strong><br>
                                <code class="small">{{ $userProgram->stripe_subscription_id }}</code>
                            </div>
                            @endif
                            @if($userProgram->stripe_customer_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Customer ID:</strong><br>
                                <code class="small">{{ $userProgram->stripe_customer_id }}</code>
                            </div>
                            @endif
                            @if($userProgram->approved_at)
                            <div class="col-md-6 mb-3">
                                <strong>Approved At:</strong><br>
                                <span class="text-muted">{{ $userProgram->approved_at->format('F d, Y h:i A') }}</span>
                            </div>
                            @endif
                            @if($userProgram->payment_completed_at)
                            <div class="col-md-6 mb-3">
                                <strong>Payment Completed At:</strong><br>
                                <span class="text-muted">{{ $userProgram->payment_completed_at->format('F d, Y h:i A') }}</span>
                            </div>
                            @endif
                            @if($userProgram->admin_notes)
                            <div class="col-12 mb-3">
                                <strong>Admin Notes:</strong><br>
                                <div class="alert alert-secondary mb-0" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto;">
                                    {{ $userProgram->admin_notes }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @endif
                <!-- All Payments for this Subscription -->
                @if($userProgram)
                @php
                    $allPayments = $userProgram->payments()->orderBy('created_at', 'desc')->get();
                @endphp
                @if($allPayments->count() > 1)
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Payments for This Subscription
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPayments as $subPayment)
                                    <tr class="{{ $subPayment->id === $payment->id ? 'table-primary' : '' }}">
                                        <td>{{ $subPayment->paid_at ? $subPayment->paid_at->format('M d, Y') : $subPayment->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $subPayment->payment_type_display }}</span>
                                            @if($subPayment->month_number)
                                                <small class="text-muted">(Month {{ $subPayment->month_number }})</small>
                                            @endif
                                        </td>
                                        <td><strong>${{ number_format($subPayment->amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $subPayment->status_badge_color }}">
                                                {{ ucfirst($subPayment->status) }}
                                            </span>
                                        </td>
                                        <td><code class="small">{{ $subPayment->payment_reference }}</code></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($userProgram && $user)
                <a href="{{ route('admin.clients.profile', $user->id) }}" class="btn btn-primary">
                    <i class="fas fa-user me-1"></i>View Client Profile
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

