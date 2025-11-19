@extends('layouts.client')

@section('title', 'My Subscriptions & Payments')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-credit-card me-2 text-primary"></i>My Subscriptions & Payments
    </h1>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Total Paid</h6>
                <h3 class="mb-0">${{ number_format($totalPaid, 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Active Subscriptions</h6>
                <h3 class="mb-0">{{ $activeSubscriptions }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6 class="card-title text-white-50 mb-2">Total Programs</h6>
                <h3 class="mb-0">{{ $userPrograms->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Subscriptions/Programs -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-box me-2"></i>My Programs & Subscriptions
        </h5>
    </div>
    <div class="card-body">
        @if($userPrograms->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Type</th>
                            <th>Payment Type</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Amount Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userPrograms as $userProgram)
                        <tr>
                            <td>
                                <strong>{{ $userProgram->program->name }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($userProgram->program->description, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $userProgram->program->formatted_subscription_type }}</span>
                            </td>
                            <td>
                                @if($userProgram->payment_type === 'monthly')
                                    <span class="badge bg-warning">Monthly</span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $userProgram->payments_completed }}/{{ $userProgram->total_payments_due }} payments
                                    </small>
                                @else
                                    <span class="badge bg-success">One-Time</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $userProgram->status_badge_color }}">
                                    {{ $userProgram->status_display_text }}
                                </span>
                            </td>
                            <td>
                                @if($userProgram->contract_start_date)
                                    {{ $userProgram->contract_start_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($userProgram->contract_end_date)
                                    {{ $userProgram->contract_end_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <strong>${{ number_format($userProgram->amount_paid ?? 0, 0) }}</strong>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('client.programs.show', $userProgram->program) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($userProgram->canBeCancelled() && $userProgram->status === \App\Models\UserProgram::STATUS_ACTIVE)
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelModal{{ $userProgram->id }}">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">You don't have any programs or subscriptions yet.</p>
                <a href="{{ route('client.programs') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Browse Programs
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Payment History -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-history me-2"></i>Payment History
        </h5>
    </div>
    <div class="card-body">
        @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Program</th>
                            <th>Payment Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>
                                {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}
                                <br>
                                <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <strong>{{ $payment->userProgram->program->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $payment->payment_type_display }}</span>
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
                                        Stripe: {{ substr($payment->stripe_payment_intent_id, -12) }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted">No payment history found.</p>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Subscription Modals -->
@foreach($userPrograms as $userProgram)
@if($userProgram->canBeCancelled() && $userProgram->status === \App\Models\UserProgram::STATUS_ACTIVE)
<div class="modal fade" id="cancelModal{{ $userProgram->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Cancel Subscription
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('client.subscriptions.cancel', $userProgram) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Are you sure you want to cancel this subscription?</strong>
                        <ul class="mb-0 mt-2">
                            <li>Your subscription will be cancelled immediately</li>
                            @if($userProgram->payment_type === 'monthly')
                                <li>Future monthly payments will be stopped</li>
                            @endif
                            <li>You will lose access to program benefits</li>
                            <li>This action cannot be undone</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancellation_reason{{ $userProgram->id }}" class="form-label">
                            Reason for Cancellation <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="cancellation_reason{{ $userProgram->id }}" 
                                  name="cancellation_reason" 
                                  rows="4" 
                                  placeholder="Please tell us why you're cancelling..." 
                                  required></textarea>
                        <small class="text-muted">This helps us improve our services.</small>
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

@endsection

