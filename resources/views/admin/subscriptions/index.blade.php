@extends('layouts.admin')

@section('title', 'Subscriptions Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Subscriptions Management</h1>
                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create New Subscription
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Subscriptions</h6>
                </div>
                <div class="card-body">
                    @if($subscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Program</th>
                                        <th>Type</th>
                                        <th>Monthly Price</th>
                                        <th>Sessions</th>
                                        <th>Bookings Used</th>
                                        <th>Status</th>
                                        <th>Next Billing</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscriptions as $subscription)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $subscription->user->name }}</strong>
                                                    <br><small class="text-muted">{{ $subscription->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $subscription->program->name }}</strong>
                                                    @if($subscription->program->is_subscription_based)
                                                        <span class="badge bg-info ms-1">Subscription</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $subscription->formatted_subscription_type }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">{{ $subscription->formatted_monthly_price }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge bg-light text-dark">{{ $subscription->monthly_sessions }} sessions/month</span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $used = $subscription->currentMonthBookings()->count();
                                                    $limit = $subscription->monthly_sessions;
                                                    $percentage = $limit > 0 ? ($used / $limit) * 100 : 0;
                                                @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : ($percentage >= 80 ? 'bg-warning' : 'bg-success') }}" 
                                                             role="progressbar" 
                                                             style="width: {{ min($percentage, 100) }}%">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">{{ $used }}/{{ $limit }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($subscription->isActive())
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($subscription->status === 'expired')
                                                    <span class="badge bg-danger">Expired</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subscription->next_billing_date)
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($subscription->next_billing_date)->format('M j, Y') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       data-bs-toggle="tooltip" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       data-bs-toggle="tooltip" 
                                                       title="Edit Subscription">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('admin.subscriptions.toggle-status', $subscription) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm {{ $subscription->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                                data-bs-toggle="tooltip" 
                                                                title="{{ $subscription->is_active ? 'Deactivate' : 'Activate' }} Subscription"
                                                                onclick="return confirm('Are you sure you want to {{ $subscription->is_active ? 'deactivate' : 'activate' }} this subscription?')">
                                                            <i class="fas {{ $subscription->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.subscriptions.reset-monthly', $subscription) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-secondary"
                                                                data-bs-toggle="tooltip" 
                                                                title="Reset Monthly Count"
                                                                onclick="return confirm('Are you sure you want to reset the monthly booking count?')">
                                                            <i class="fas fa-refresh"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this subscription? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="tooltip" 
                                                                title="Delete Subscription">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $subscriptions->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Subscriptions Found</h5>
                            <p class="text-muted">Get started by creating your first subscription.</p>
                            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Subscription
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
