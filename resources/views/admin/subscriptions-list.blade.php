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
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Program</th>
                        <th>Package Type</th>
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
                            <span class="badge bg-secondary">{{ $subscription->program->formatted_subscription_type }}</span>
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
                                <a href="{{ route('admin.programs.applications') }}?status={{ $subscription->status }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
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
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No subscriptions found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>

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

@endsection

