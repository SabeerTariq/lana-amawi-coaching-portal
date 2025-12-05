@extends('layouts.admin')

@section('title', 'Subscriptions & Programs')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-users me-2 text-primary"></i>Subscriptions & Programs
    </h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Active Subscriptions</h6>
                <h3 class="mb-0">{{ $activeSubscriptions }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Monthly</h6>
                <h3 class="mb-0">{{ $monthlySubscriptions }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">One-Time</h6>
                <h3 class="mb-0">{{ $oneTimeSubscriptions }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Total</h6>
                <h3 class="mb-0">{{ $subscriptions->total() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Status Breakdown -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Subscriptions by Status</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $subscriptionsByStatus['active'] }}</h4>
                        <small class="text-muted">Active</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-info">{{ $subscriptionsByStatus['approved'] }}</h4>
                        <small class="text-muted">Approved</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-danger">{{ $subscriptionsByStatus['cancelled'] }}</h4>
                        <small class="text-muted">Cancelled</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ $subscriptionsByStatus['rejected'] }}</h4>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subscriptions Table -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>All Subscriptions & Programs
        </h5>
    </div>
    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="d-none d-lg-block">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Program</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                        <th>Contract Period</th>
                        <th>Payments</th>
                        <th>Amount Paid</th>
                        <th>Stripe Subscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td>
                            <strong>{{ $subscription->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $subscription->user->email }}</small>
                        </td>
                        <td>
                            <strong>{{ $subscription->program->name }}</strong>
                            <br>
                            <span class="badge bg-info">{{ $subscription->program->formatted_subscription_type }}</span>
                        </td>
                        <td>
                            @if($subscription->payment_type === 'monthly')
                                <span class="badge bg-warning">Monthly</span>
                            @else
                                <span class="badge bg-success">One-Time</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $subscription->status_badge_color }}">
                                {{ $subscription->status_display_text }}
                            </span>
                        </td>
                        <td>
                            @if($subscription->contract_start_date && $subscription->contract_end_date)
                                <small>
                                    {{ $subscription->contract_start_date->format('M d, Y') }} - 
                                    {{ $subscription->contract_end_date->format('M d, Y') }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($subscription->payment_type === 'monthly')
                                <small>
                                    {{ $subscription->payments_completed }}/{{ $subscription->total_payments_due }}
                                    @if($subscription->next_payment_date)
                                        <br>
                                        <span class="text-muted">Next: {{ $subscription->next_payment_date->format('M d') }}</span>
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>${{ number_format($subscription->amount_paid ?? 0, 0) }}</strong>
                        </td>
                        <td>
                            @if($subscription->stripe_subscription_id)
                                <small class="text-muted">
                                    <i class="fas fa-credit-card me-1"></i>
                                    {{ substr($subscription->stripe_subscription_id, -12) }}
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#subscriptionDetailsModal{{ $subscription->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($subscription->canBeCancelled() && $subscription->status === \App\Models\UserProgram::STATUS_ACTIVE)
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#cancelModal{{ $subscription->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No subscriptions found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tablet View -->
        <div class="d-none d-md-block d-lg-none">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Program</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                        <th>Amount Paid</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td>
                            <strong>{{ $subscription->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $subscription->user->email }}</small>
                        </td>
                        <td>
                            <strong>{{ $subscription->program->name }}</strong>
                            <br>
                            <span class="badge bg-info">{{ $subscription->program->formatted_subscription_type }}</span>
                        </td>
                        <td>
                            @if($subscription->payment_type === 'monthly')
                                <span class="badge bg-warning">Monthly</span>
                                @if($subscription->payments_completed !== null)
                                    <br><small class="text-muted">{{ $subscription->payments_completed }}/{{ $subscription->total_payments_due }}</small>
                                @endif
                            @else
                                <span class="badge bg-success">One-Time</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $subscription->status_badge_color }}">
                                {{ $subscription->status_display_text }}
                            </span>
                        </td>
                        <td>
                            <strong>${{ number_format($subscription->amount_paid ?? 0, 0) }}</strong>
                        </td>
                        <td>
                            <div class="btn-group-vertical" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary mb-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#subscriptionDetailsModal{{ $subscription->id }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                @if($subscription->canBeCancelled() && $subscription->status === \App\Models\UserProgram::STATUS_ACTIVE)
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#cancelModal{{ $subscription->id }}">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No subscriptions found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            @forelse($subscriptions as $subscription)
            <div class="card border-bottom rounded-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $subscription->program->name }}</h6>
                            <p class="text-muted mb-1 small">{{ $subscription->user->name }}</p>
                            <p class="text-muted mb-0 small">{{ $subscription->user->email }}</p>
                        </div>
                        <span class="badge bg-{{ $subscription->status_badge_color }}">
                            {{ $subscription->status_display_text }}
                        </span>
                    </div>
                    
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Program Type</small>
                            <span class="badge bg-info">{{ $subscription->program->formatted_subscription_type }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Payment Type</small>
                            @if($subscription->payment_type === 'monthly')
                                <span class="badge bg-warning">Monthly</span>
                            @else
                                <span class="badge bg-success">One-Time</span>
                            @endif
                        </div>
                        @if($subscription->payment_type === 'monthly' && $subscription->payments_completed !== null)
                        <div class="col-6">
                            <small class="text-muted d-block">Payments</small>
                            <strong>{{ $subscription->payments_completed }}/{{ $subscription->total_payments_due }}</strong>
                        </div>
                        @endif
                        @if($subscription->amount_paid)
                        <div class="col-6">
                            <small class="text-muted d-block">Amount Paid</small>
                            <strong class="text-success">${{ number_format($subscription->amount_paid, 0) }}</strong>
                        </div>
                        @endif
                        @if($subscription->contract_start_date && $subscription->contract_end_date)
                        <div class="col-12">
                            <small class="text-muted d-block">Contract Period</small>
                            <small>{{ $subscription->contract_start_date->format('M d, Y') }} - {{ $subscription->contract_end_date->format('M d, Y') }}</small>
                        </div>
                        @endif
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#subscriptionDetailsModal{{ $subscription->id }}">
                            <i class="fas fa-eye me-1"></i>View Details
                        </button>
                        @if($subscription->canBeCancelled() && $subscription->status === \App\Models\UserProgram::STATUS_ACTIVE)
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#cancelModal{{ $subscription->id }}">
                                <i class="fas fa-times me-1"></i>Cancel Subscription
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No subscriptions found.</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>

<!-- Subscription Details Modals -->
@foreach($subscriptions as $subscription)
<div class="modal fade" id="subscriptionDetailsModal{{ $subscription->id }}" tabindex="-1" aria-labelledby="subscriptionDetailsModalLabel{{ $subscription->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="subscriptionDetailsModalLabel{{ $subscription->id }}">
                    <i class="fas fa-user-tie me-2"></i>Subscription & Program Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Client & Program Information -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-user me-2"></i>Client & Program Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Client Name:</strong><br>
                                <span class="text-muted">{{ $subscription->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Client Email:</strong><br>
                                <span class="text-muted">{{ $subscription->user->email ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Program Name:</strong><br>
                                <span class="badge bg-info fs-6">{{ $subscription->program->name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Program Type:</strong><br>
                                <span class="text-muted">{{ $subscription->program->formatted_subscription_type ?? 'N/A' }}</span>
                            </div>
                            @if($subscription->program)
                            <div class="col-md-6 mb-3">
                                <strong>Monthly Price:</strong><br>
                                <span class="text-success fw-bold">${{ number_format($subscription->program->monthly_price ?? 0, 2) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Monthly Sessions:</strong><br>
                                <span class="text-muted">{{ $subscription->program->monthly_sessions ?? 'N/A' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription Details -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Subscription Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Subscription Status:</strong><br>
                                <span class="badge bg-{{ $subscription->status_badge_color }} fs-6">
                                    {{ $subscription->status_display_text }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Payment Type:</strong><br>
                                @if($subscription->payment_type === 'monthly')
                                    <span class="badge bg-warning">Monthly Payments</span>
                                @elseif($subscription->payment_type === 'one_time')
                                    <span class="badge bg-success">One-Time Payment</span>
                                @else
                                    <span class="text-muted">{{ ucfirst($subscription->payment_type ?? 'N/A') }}</span>
                                @endif
                            </div>
                            @if($subscription->contract_start_date)
                            <div class="col-md-6 mb-3">
                                <strong>Contract Start Date:</strong><br>
                                <span class="text-muted">{{ $subscription->contract_start_date->format('F d, Y') }}</span>
                            </div>
                            @endif
                            @if($subscription->contract_end_date)
                            <div class="col-md-6 mb-3">
                                <strong>Contract End Date:</strong><br>
                                <span class="text-muted">{{ $subscription->contract_end_date->format('F d, Y') }}</span>
                            </div>
                            @endif
                            @if($subscription->contract_duration_months)
                            <div class="col-md-6 mb-3">
                                <strong>Contract Duration:</strong><br>
                                <span class="text-muted">{{ $subscription->contract_duration_months }} months</span>
                            </div>
                            @endif
                            @if($subscription->next_payment_date)
                            <div class="col-md-6 mb-3">
                                <strong>Next Payment Date:</strong><br>
                                <span class="text-warning fw-bold">{{ $subscription->next_payment_date->format('F d, Y') }}</span>
                            </div>
                            @endif
                            @if($subscription->total_payments_due)
                            <div class="col-md-6 mb-3">
                                <strong>Total Payments Due:</strong><br>
                                <span class="text-muted">{{ $subscription->total_payments_due }}</span>
                            </div>
                            @endif
                            @if($subscription->payments_completed !== null)
                            <div class="col-md-6 mb-3">
                                <strong>Payments Completed:</strong><br>
                                <span class="text-success fw-bold">{{ $subscription->payments_completed }} / {{ $subscription->total_payments_due ?? 'N/A' }}</span>
                            </div>
                            @endif
                            @if($subscription->amount_paid)
                            <div class="col-md-6 mb-3">
                                <strong>Total Amount Paid:</strong><br>
                                <span class="text-success fw-bold fs-5">${{ number_format($subscription->amount_paid, 2) }}</span>
                            </div>
                            @endif
                            @if($subscription->one_time_payment_amount)
                            <div class="col-md-6 mb-3">
                                <strong>One-Time Payment Amount:</strong><br>
                                <span class="text-success fw-bold">${{ number_format($subscription->one_time_payment_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($subscription->stripe_subscription_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Subscription ID:</strong><br>
                                <code class="small">{{ $subscription->stripe_subscription_id }}</code>
                            </div>
                            @endif
                            @if($subscription->stripe_customer_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Customer ID:</strong><br>
                                <code class="small">{{ $subscription->stripe_customer_id }}</code>
                            </div>
                            @endif
                            @if($subscription->stripe_price_id)
                            <div class="col-md-6 mb-3">
                                <strong>Stripe Price ID:</strong><br>
                                <code class="small">{{ $subscription->stripe_price_id }}</code>
                            </div>
                            @endif
                            @if($subscription->approved_at)
                            <div class="col-md-6 mb-3">
                                <strong>Approved At:</strong><br>
                                <span class="text-muted">{{ $subscription->approved_at->format('F d, Y h:i A') }}</span>
                            </div>
                            @endif
                            @if($subscription->payment_completed_at)
                            <div class="col-md-6 mb-3">
                                <strong>Payment Completed At:</strong><br>
                                <span class="text-muted">{{ $subscription->payment_completed_at->format('F d, Y h:i A') }}</span>
                            </div>
                            @endif
                            @if($subscription->agreement_sent_at)
                            <div class="col-md-6 mb-3">
                                <strong>Agreement Sent At:</strong><br>
                                <span class="text-muted">{{ $subscription->agreement_sent_at->format('F d, Y h:i A') }}</span>
                            </div>
                            @endif
                            @if($subscription->agreement_uploaded_at)
                            <div class="col-md-6 mb-3">
                                <strong>Agreement Uploaded At:</strong><br>
                                <span class="text-muted">{{ $subscription->agreement_uploaded_at->format('F d, Y h:i A') }}</span>
                            </div>
                            @endif
                            @if($subscription->admin_notes)
                            <div class="col-12 mb-3">
                                <strong>Admin Notes:</strong><br>
                                <div class="alert alert-secondary mb-0" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto;">
                                    {{ $subscription->admin_notes }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                @php
                    $allPayments = $subscription->payments()->orderBy('created_at', 'desc')->get();
                @endphp
                @if($allPayments->count() > 0)
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Payment History ({{ $allPayments->count() }})
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
                                        <th>Stripe ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $payment->payment_type_display }}</span>
                                            @if($payment->month_number)
                                                <small class="text-muted">(Month {{ $payment->month_number }})</small>
                                            @endif
                                        </td>
                                        <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status_badge_color }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td><code class="small">{{ $payment->payment_reference }}</code></td>
                                        <td>
                                            @if($payment->stripe_payment_intent_id)
                                                <small class="text-muted">{{ substr($payment->stripe_payment_intent_id, -12) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($subscription->user)
                <a href="{{ route('admin.clients.profile', $subscription->user->id) }}" class="btn btn-primary">
                    <i class="fas fa-user me-1"></i>View Client Profile
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Cancel Subscription Modals -->
@foreach($subscriptions as $subscription)
@if($subscription->canBeCancelled() && $subscription->status === \App\Models\UserProgram::STATUS_ACTIVE)
<div class="modal fade" id="cancelModal{{ $subscription->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Cancel Subscription
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Cancel subscription for {{ $subscription->user->name }}?</strong>
                        <ul class="mb-0 mt-2">
                            <li>Subscription will be cancelled immediately</li>
                            @if($subscription->payment_type === 'monthly')
                                <li>Stripe subscription will be cancelled</li>
                                <li>Future monthly payments will be stopped</li>
                            @endif
                            <li>Client will lose access to program benefits</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancellation_reason{{ $subscription->id }}" class="form-label">
                            Reason for Cancellation (Optional)
                        </label>
                        <textarea class="form-control" 
                                  id="cancellation_reason{{ $subscription->id }}" 
                                  name="cancellation_reason" 
                                  rows="3" 
                                  placeholder="Enter reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Subscription</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Yes, Cancel Subscription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<style>
/* Responsive adjustments */
@media (max-width: 991px) {
    .table td, .table th {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
    }
}

/* Mobile card view styling */
.d-md-none .card {
    border-left: none;
    border-right: none;
}

.d-md-none .card:first-child {
    border-top: none;
}

.d-md-none .card:last-child {
    border-bottom: none;
}
</style>

@endsection

