@extends('layouts.admin')

@section('title', 'Program Applications - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Program Applications</h1>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#workflowGuideModal">
            <i class="fas fa-question-circle me-1"></i>Workflow Guide
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Program Applications Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>Program Applications Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @php
                        $totalApplications = $applications->flatten()->count();
                        $statusCounts = [];
                        foreach($applications as $status => $apps) {
                            $statusCounts[$status] = count($apps);
                        }
                    @endphp
                    
                    <div class="col-md-2 col-6 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 mb-0 text-primary">{{ $totalApplications }}</div>
                            <small class="text-muted">Total Applications</small>
                        </div>
                    </div>
                    
                    @foreach(['agreement_uploaded', 'approved', 'active', 'rejected'] as $status)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 mb-0 text-{{ $statusConfig[$status]['color'] ?? 'secondary' }}">
                                {{ $statusCounts[$status] ?? 0 }}
                            </div>
                            <small class="text-muted">{{ $statusConfig[$status]['label'] ?? ucfirst($status) }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Tabs -->
<ul class="nav nav-tabs mb-4" id="statusTabs" role="tablist">
    @php
        $statusConfig = [
            'agreement_uploaded' => ['icon' => 'fa-upload', 'color' => 'primary', 'label' => 'Agreement Uploaded'],
            'approved' => ['icon' => 'fa-check', 'color' => 'success', 'label' => 'Approved'],
            'active' => ['icon' => 'fa-play', 'color' => 'success', 'label' => 'Active'],
            'rejected' => ['icon' => 'fa-times', 'color' => 'danger', 'label' => 'Rejected'],
            'cancelled' => ['icon' => 'fa-ban', 'color' => 'secondary', 'label' => 'Cancelled'],
        ];
        
        // Filter out pending and agreement_sent statuses - admin only sees uploaded agreements and beyond
        $availableStatuses = $applications->keys()->toArray();
        $statusesToShow = [];
        
        // Only show statuses from agreement_uploaded onwards
        $visibleStatuses = ['agreement_uploaded', 'approved', 'active', 'rejected', 'cancelled'];
        
        foreach($visibleStatuses as $status) {
            if (in_array($status, $availableStatuses) || isset($statusConfig[$status])) {
                $statusesToShow[] = $status;
            }
        }
        
        // Add any other statuses that exist in data but not in config (for backward compatibility)
        foreach($availableStatuses as $status) {
            if (!in_array($status, $visibleStatuses) && !in_array($status, $statusesToShow)) {
                $statusesToShow[] = $status;
                $statusConfig[$status] = [
                    'icon' => 'fa-circle',
                    'color' => 'secondary',
                    'label' => ucfirst(str_replace('_', ' ', $status))
                ];
            }
        }
    @endphp
    
    @php
        // Make agreement_uploaded the default active tab
        $firstStatus = in_array('agreement_uploaded', $statusesToShow) ? 'agreement_uploaded' : ($statusesToShow[0] ?? null);
    @endphp
    @foreach($statusesToShow as $index => $status)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $status === $firstStatus ? 'active' : '' }}" 
                id="{{ $status }}-tab" 
                data-bs-toggle="tab" 
                data-bs-target="#{{ $status }}" 
                type="button" 
                role="tab">
            <i class="fas {{ $statusConfig[$status]['icon'] }} me-2"></i>{{ $statusConfig[$status]['label'] }}
            @if(isset($applications[$status]))
                <span class="badge bg-{{ $statusConfig[$status]['color'] }} ms-1">{{ count($applications[$status]) }}</span>
            @endif
        </button>
    </li>
    @endforeach
</ul>

<!-- Tab Content -->
<div class="tab-content" id="statusTabsContent">
    @php
        $firstStatus = in_array('agreement_uploaded', $statusesToShow) ? 'agreement_uploaded' : ($statusesToShow[0] ?? null);
    @endphp
    @foreach($statusesToShow as $index => $status)
    <div class="tab-pane fade {{ $status === $firstStatus ? 'show active' : '' }}" id="{{ $status }}" role="tabpanel">
        @if(isset($applications[$status]) && count($applications[$status]) > 0)
            <div class="row">
                @foreach($applications[$status] as $application)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $application->user->name }}</h6>
                            <span class="badge bg-{{ $application->status_badge_color }}">
                                {{ $application->status_display_text }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary">{{ $application->program->name }}</h6>
                                <p class="text-muted small mb-1">{{ $application->user->email }}</p>
                                <p class="text-muted small mb-1">{{ $application->user->institution_hospital }}</p>
                                <p class="text-muted small mb-0">{{ $application->user->position }}</p>
                            </div>
                            
                            <!-- Program Pricing -->
                            <div class="mb-3">
                                <strong class="d-block mb-2">
                                    <i class="fas fa-dollar-sign text-success me-1"></i>Pricing
                                </strong>
                                <div class="row g-2 text-center">
                                    <div class="{{ $application->program->one_time_payment_amount ? 'col-4' : 'col-6' }}">
                                        <div class="border rounded p-2 bg-light">
                                            <div class="h6 mb-0 text-primary fw-bold">${{ number_format($application->program->monthly_price ?? 0, 0) }}</div>
                                            <small class="text-muted d-block">Monthly</small>
                                        </div>
                                    </div>
                                    @if($application->program->one_time_payment_amount)
                                    <div class="col-4">
                                        <div class="border rounded p-2 border-success bg-light">
                                            <div class="h6 mb-0 text-success fw-bold">${{ number_format($application->program->one_time_payment_amount, 0) }}</div>
                                            <small class="text-muted d-block">One-Time</small>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="{{ $application->program->one_time_payment_amount ? 'col-4' : 'col-6' }}">
                                        <div class="border rounded p-2 bg-light">
                                            <div class="h6 mb-0 text-info fw-bold">{{ $application->program->monthly_sessions ?? 0 }}</div>
                                            <small class="text-muted d-block">Sessions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Indicator -->
                            <div class="mb-3">
                                <strong>Application Progress:</strong>
                                <div class="progress mt-2" style="height: 8px;">
                                    @php
                                        $progressSteps = [
                                            'pending' => 0,
                                            'agreement_sent' => 25,
                                            'agreement_uploaded' => 50,
                                            'approved' => 75,
                                            'active' => 100
                                        ];
                                        $currentProgress = $progressSteps[$application->status] ?? 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $application->status_badge_color }}" 
                                         role="progressbar" 
                                         style="width: {{ $currentProgress }}%"
                                         aria-valuenow="{{ $currentProgress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $currentProgress }}% Complete</small>
                            </div>
                            
                            @if($application->admin_notes)
                                <div class="mb-3">
                                    <strong>Admin Notes:</strong>
                                    <p class="small text-muted">{{ $application->admin_notes }}</p>
                                </div>
                            @endif
                            
                            <div class="d-flex flex-wrap gap-1">
                                @if($status === 'agreement_uploaded')
                                    <form action="{{ route('admin.programs.approve', $application) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal{{ $application->id }}">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                    <a href="{{ route('admin.programs.view-agreement', $application) }}" 
                                       class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="fas fa-eye me-1"></i>View Agreement
                                    </a>
                                @elseif($status === 'approved')
                                    <span class="text-muted small">Client will proceed to payment selection and checkout. Program will activate automatically after payment.</span>
                                @elseif($status === 'payment_completed')
                                    <form action="{{ route('admin.programs.activate', $application) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-play me-1"></i>Activate Program
                                        </button>
                                    </form>
                                @elseif($status === 'active')
                                    <span class="text-success small">
                                        <i class="fas fa-check-circle me-1"></i>Program Active
                                    </span>
                                @elseif($status === 'rejected')
                                    <span class="text-danger small">
                                        <i class="fas fa-times-circle me-1"></i>Application Rejected
                                    </span>
                                @elseif($status === 'cancelled')
                                    <span class="text-secondary small">
                                        <i class="fas fa-ban me-1"></i>Application Cancelled
                                    </span>
                                @endif
                                
                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#notesModal{{ $application->id }}">
                                    <i class="fas fa-sticky-note me-1"></i>Notes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">No {{ str_replace('_', ' ', $status) }} applications</h5>
            </div>
        @endif
    </div>
    @endforeach
</div>

<!-- Reject Modal -->
@foreach($applications->flatten() as $application)
@if(in_array($application->status, ['pending', 'agreement_uploaded']))
<div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.programs.reject', $application) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will reject the program application. The client will be notified and the status will be changed to "Rejected".
                    </div>
                    <div class="mb-3">
                        <label for="admin_notes{{ $application->id }}" class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="admin_notes{{ $application->id }}" name="admin_notes" rows="4" required placeholder="Please provide a reason for rejecting this application..."></textarea>
                        <small class="form-text text-muted">This note will be visible to the admin and may be shared with the client.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Request Payment Modal -->
@foreach($applications->flatten() as $application)
@if($application->status === 'approved')
<div class="modal fade" id="requestPaymentModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Payment - {{ $application->program->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.programs.request-payment', $application) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>3-Month Contract</strong><br>
                        Monthly Price: <strong>${{ number_format($application->program->monthly_price ?? 0, 2) }}</strong><br>
                        @if($application->program->one_time_payment_amount)
                            One-Time Amount: <strong>${{ number_format($application->program->one_time_payment_amount, 2) }}</strong>
                        @else
                            One-Time Amount: <strong>Not set</strong> (will use monthly × 3)
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Type *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_type" id="payment_monthly{{ $application->id }}" 
                                   value="monthly" checked onchange="updatePaymentAmount{{ $application->id }}()">
                            <label class="form-check-label" for="payment_monthly{{ $application->id }}">
                                <strong>Monthly Payments</strong> - 3 payments of ${{ number_format($application->program->monthly_price ?? 0, 2) }} each
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="payment_type" id="payment_one_time{{ $application->id }}" 
                                   value="one_time" onchange="updatePaymentAmount{{ $application->id }}()">
                            <label class="form-check-label" for="payment_one_time{{ $application->id }}">
                                <strong>One-Time Payment</strong> - 
                                @if($application->program->one_time_payment_amount)
                                    ${{ number_format($application->program->one_time_payment_amount, 2) }}
                                @else
                                    ${{ number_format(($application->program->monthly_price ?? 0) * 3, 2) }} (monthly × 3)
                                @endif
                                 for all 3 months
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Request Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Payment Modal (kept for backward compatibility if needed) -->
@foreach($applications->flatten() as $application)
@if(false && $application->status === 'payment_requested')
<div class="modal fade" id="paymentModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Payment as Completed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.programs.mark-payment-completed', $application) }}" method="POST">
                @csrf
                <div class="modal-body">
                    @php
                        $paymentType = $application->payment_type ?? 'monthly';
                        $monthlyPrice = $application->program->monthly_price ?? 0;
                        $oneTimeAmount = $application->program->one_time_payment_amount ?? ($monthlyPrice * 3);
                        $paymentsCompleted = $application->payments_completed ?? 0;
                        $nextMonth = $paymentsCompleted + 1;
                    @endphp
                    
                    <div class="alert alert-info mb-3">
                        <strong>Contract Details:</strong><br>
                        Payment Type: <strong>{{ ucfirst($paymentType) }}</strong><br>
                        @if($paymentType === 'monthly')
                            Payments Completed: {{ $paymentsCompleted }}/3<br>
                            @if($nextMonth <= 3)
                                This is payment for <strong>Month {{ $nextMonth }}</strong>
                            @endif
                        @else
                            One-Time Payment: <strong>${{ number_format($oneTimeAmount, 2) }}</strong>
                        @endif
                    </div>
                    
                    <input type="hidden" name="payment_type" value="{{ $paymentType === 'one_time' ? 'contract_one_time' : 'contract_monthly' }}">
                    @if($paymentType === 'monthly')
                        <input type="hidden" name="month_number" value="{{ $nextMonth }}">
                    @endif
                    
                    <div class="mb-3">
                        <label for="amount_paid{{ $application->id }}" class="form-label">Amount Paid *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="amount_paid{{ $application->id }}" name="amount_paid" 
                                   step="0.01" min="0" 
                                   value="{{ $paymentType === 'one_time' ? $oneTimeAmount : $monthlyPrice }}" 
                                   required>
                        </div>
                        <small class="form-text text-muted">
                            @if($paymentType === 'monthly')
                                Expected: ${{ number_format($monthlyPrice, 2) }} (Month {{ $nextMonth }})
                            @else
                                Expected: ${{ number_format($oneTimeAmount, 2) }} (Full 3 months)
                            @endif
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="payment_reference{{ $application->id }}" class="form-label">Payment Reference *</label>
                        <input type="text" class="form-control" id="payment_reference{{ $application->id }}" name="payment_reference" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Notes Modal -->
@foreach($applications->flatten() as $application)
<div class="modal fade" id="notesModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Admin Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.programs.add-notes', $application) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4">{{ $application->admin_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Notes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Workflow Guide Modal -->
<div class="modal fade" id="workflowGuideModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-route me-2 text-primary"></i>Program Application Workflow Guide
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">Application Processing Steps:</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>1. Agreement Uploaded</h6>
                                    <p class="small text-muted">Client selects program → Agreement auto-sent → Client uploads signed agreement</p>
                                    <span class="badge bg-primary">Action: View Agreement & Approve</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>2. Approved</h6>
                                    <p class="small text-muted">Application approved → Client selects payment type (Monthly/One-Time) and completes checkout</p>
                                    <span class="badge bg-success">Monitor: Client will proceed to payment</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>3. Active</h6>
                                    <p class="small text-muted">Client completes payment → Program activates automatically</p>
                                    <span class="badge bg-success">Monitor: Program in progress</span>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="text-primary mb-3">Quick Actions by Status:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled small">
                                    <li><span class="badge bg-primary me-2">Agreement Uploaded</span> View | Approve</li>
                                    <li><span class="badge bg-success me-2">Approved</span> Client will proceed to payment</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled small">
                                    <li><span class="badge bg-success me-2">Active</span> Monitor</li>
                                    <li><span class="badge bg-danger me-2">Rejected</span> View Details</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 17px;
    width: 2px;
    height: calc(100% + 10px);
    background: #dee2e6;
}
</style>

@endsection
