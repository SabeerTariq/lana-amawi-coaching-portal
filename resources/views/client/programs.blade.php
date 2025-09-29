@extends('layouts.client')

@section('title', 'Programs - Client')

@section('content')
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

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Available Programs</h1>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#clientWorkflowModal">
            <i class="fas fa-question-circle me-1"></i>How It Works
        </button>
    </div>
</div>

<!-- My Program Applications -->
@if($userPrograms->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>My Program Applications
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($userPrograms as $userProgram)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $userProgram->program->name }}</h6>
                                <span class="badge bg-{{ $userProgram->status_badge_color }}">
                                    {{ $userProgram->status_display_text }}
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">{{ Str::limit($userProgram->program->description, 100) }}</p>
                                <p class="mb-2"><strong>Price:</strong> {{ $userProgram->program->formatted_price }}</p>
                                
                                <!-- Progress Indicator -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Progress</small>
                                        <small class="text-muted">
                                            @php
                                                $progressSteps = [
                                                    'pending' => 0,
                                                    'agreement_sent' => 20,
                                                    'agreement_uploaded' => 40,
                                                    'approved' => 60,
                                                    'payment_requested' => 80,
                                                    'payment_completed' => 90,
                                                    'active' => 100
                                                ];
                                                $currentProgress = $progressSteps[$userProgram->status] ?? 0;
                                            @endphp
                                            {{ $currentProgress }}%
                                        </small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $userProgram->status_badge_color }}" 
                                             role="progressbar" 
                                             style="width: {{ $currentProgress }}%"
                                             aria-valuenow="{{ $currentProgress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                
                                @if($userProgram->status === \App\Models\UserProgram::STATUS_AGREEMENT_SENT)
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('client.programs.agreement.download', $userProgram) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-download me-1"></i>Download Agreement
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadAgreementModal{{ $userProgram->id }}">
                                            <i class="fas fa-upload me-1"></i>Upload Signed Agreement
                                        </button>
                                    </div>
                                @elseif($userProgram->status === \App\Models\UserProgram::STATUS_AGREEMENT_UPLOADED)
                                    <div class="alert alert-info small mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Agreement uploaded. Waiting for admin approval.
                                    </div>
                                @elseif($userProgram->status === \App\Models\UserProgram::STATUS_APPROVED)
                                    <div class="alert alert-success small mb-0">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Program approved! Payment will be requested soon.
                                    </div>
                                @elseif($userProgram->status === \App\Models\UserProgram::STATUS_PAYMENT_REQUESTED)
                                    <div class="alert alert-warning small mb-0">
                                        <i class="fas fa-credit-card me-1"></i>
                                        Payment requested. Please complete payment to activate your program.
                                    </div>
                                @elseif($userProgram->status === \App\Models\UserProgram::STATUS_ACTIVE)
                                    <div class="alert alert-success small mb-0">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Program active! You can now book sessions.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Available Programs -->
<div class="row">
    @foreach($programs as $program)
    @php
        $userProgram = $userPrograms->where('program_id', $program->id)->first();
        $isSelected = $userProgram !== null && $userProgram->status !== \App\Models\UserProgram::STATUS_CANCELLED;
        $cardClass = $isSelected ? 'border-success' : '';
        $cardHeaderClass = $isSelected ? 'bg-success text-white' : '';
    @endphp
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 {{ $cardClass }}">
            <div class="card-header {{ $cardHeaderClass }}">
                <h5 class="card-title mb-0">
                    {{ $program->name }}
                    @if($isSelected)
                        <span class="badge bg-light text-success ms-2">
                            <i class="fas fa-check-circle me-1"></i>Selected
                        </span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $program->description }}</p>
                
                @if($isSelected)
                <div class="alert alert-info mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div class="flex-grow-1">
                            <strong>Status:</strong> {{ $userProgram->status_display_text }}
                            @if($userProgram->status === \App\Models\UserProgram::STATUS_ACTIVE)
                                <br><small class="text-success">You can now book sessions!</small>
                            @elseif($userProgram->status === \App\Models\UserProgram::STATUS_PENDING)
                                <br><small class="text-warning">Your application is under review.</small>
                            @elseif($userProgram->status === \App\Models\UserProgram::STATUS_AGREEMENT_SENT)
                                <br><small class="text-info">Please check your email for the agreement.</small>
                            @elseif($userProgram->status === \App\Models\UserProgram::STATUS_AGREEMENT_UPLOADED)
                                <br><small class="text-primary">Waiting for approval.</small>
                            @elseif($userProgram->status === \App\Models\UserProgram::STATUS_PAYMENT_REQUESTED)
                                <br><small class="text-warning">Payment is required to activate your program.</small>
                            @endif
                        </div>
                        @if($userProgram->status === \App\Models\UserProgram::STATUS_ACTIVE)
                            <a href="{{ route('client.appointments') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-calendar-plus me-1"></i>Book Session
                            </a>
                        @endif
                        @if($userProgram->canBeCancelled())
                            <button type="button" class="btn btn-outline-danger btn-sm ms-2" 
                                    data-bs-toggle="modal" data-bs-target="#cancelProgramModal{{ $userProgram->id }}">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="mb-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0 text-primary">{{ $program->formatted_price }}</div>
                                <small class="text-muted">Total Price</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0 text-success">{{ $program->duration_weeks }}</div>
                                <small class="text-muted">Weeks</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="h5 mb-0 text-info">{{ $program->sessions_included }}</div>
                                <small class="text-muted">Sessions</small>
                            </div>
                        </div>
                    </div>
                </div>

                <h6>Program Features:</h6>
                <ul class="list-unstyled small">
                    @foreach(array_slice($program->features, 0, 4) as $feature)
                    <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                    @endforeach
                    @if(count($program->features) > 4)
                    <li class="text-muted">+ {{ count($program->features) - 4 }} more features</li>
                    @endif
                </ul>

                <div class="d-grid gap-2">
                    <a href="{{ route('client.programs.show', $program) }}" class="btn btn-outline-primary">
                        <i class="fas fa-info-circle me-1"></i>View Details
                    </a>
                    
                    @php
                        $userProgram = $userPrograms->where('program_id', $program->id)->first();
                    @endphp
                    
                    @if($userProgram)
                        @switch($userProgram->status)
                            @case(\App\Models\UserProgram::STATUS_PENDING)
                                <button class="btn btn-warning w-100" disabled>
                                    <i class="fas fa-clock me-1"></i>Pending Review
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_AGREEMENT_SENT)
                                <button class="btn btn-info w-100" disabled>
                                    <i class="fas fa-file-contract me-1"></i>Agreement Sent
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_AGREEMENT_UPLOADED)
                                <button class="btn btn-primary w-100" disabled>
                                    <i class="fas fa-upload me-1"></i>Agreement Uploaded
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_APPROVED)
                                <button class="btn btn-success w-100" disabled>
                                    <i class="fas fa-check me-1"></i>Approved
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_PAYMENT_REQUESTED)
                                <button class="btn btn-warning w-100" disabled>
                                    <i class="fas fa-credit-card me-1"></i>Payment Requested
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_PAYMENT_COMPLETED)
                                <button class="btn btn-success w-100" disabled>
                                    <i class="fas fa-check-circle me-1"></i>Payment Completed
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_ACTIVE)
                                <button class="btn btn-success w-100" disabled>
                                    <i class="fas fa-star me-1"></i>Active Program
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_REJECTED)
                                <button class="btn btn-danger w-100" disabled>
                                    <i class="fas fa-times me-1"></i>Rejected
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_CANCELLED)
                                <form action="{{ route('client.programs.select') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="program_id" value="{{ $program->id }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i>Select Program
                                    </button>
                                </form>
                                @break
                            @default
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-question me-1"></i>{{ ucfirst($userProgram->status) }}
                                </button>
                        @endswitch
                    @else
                        <form action="{{ route('client.programs.select') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="program_id" value="{{ $program->id }}">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-1"></i>Select Program
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Upload Agreement Modals -->
@foreach($userPrograms as $userProgram)
@if($userProgram->status === \App\Models\UserProgram::STATUS_AGREEMENT_SENT)
<div class="modal fade" id="uploadAgreementModal{{ $userProgram->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Signed Agreement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('client.programs.agreement.upload', $userProgram) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="signed_agreement" class="form-label">Signed Agreement (PDF only)</label>
                        <input type="file" class="form-control" id="signed_agreement" name="signed_agreement" 
                               accept=".pdf" required>
                        <div class="form-text">Please upload the signed agreement in PDF format (max 10MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Upload Agreement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- Client Workflow Guide Modal -->
<div class="modal fade" id="clientWorkflowModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-route me-2 text-primary"></i>How Program Applications Work
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3">Your Application Journey:</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6>1. Select Program</h6>
                                    <p class="small text-muted">Choose your desired program → Application submitted for review</p>
                                    <span class="badge bg-warning">Status: Pending Review</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6>2. Receive Agreement</h6>
                                    <p class="small text-muted">Admin sends program agreement via email → Download and sign</p>
                                    <span class="badge bg-info">Action: Download & Sign Agreement</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>3. Upload Signed Agreement</h6>
                                    <p class="small text-muted">Upload your signed agreement → Wait for approval</p>
                                    <span class="badge bg-primary">Action: Upload Signed Agreement</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>4. Application Approved</h6>
                                    <p class="small text-muted">Admin approves your application → Payment will be requested</p>
                                    <span class="badge bg-success">Status: Approved</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6>5. Complete Payment</h6>
                                    <p class="small text-muted">Receive payment request → Complete payment as instructed</p>
                                    <span class="badge bg-warning">Action: Complete Payment</span>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>6. Program Activated</h6>
                                    <p class="small text-muted">Payment confirmed → Program activated → Start booking sessions</p>
                                    <span class="badge bg-success">Status: Active - Book Sessions!</span>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="text-primary mb-3">What You Need to Do:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled small">
                                    <li><i class="fas fa-check text-success me-2"></i>Select your desired program</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Download agreement when received</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Sign and upload agreement</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Complete payment when requested</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled small">
                                    <li><i class="fas fa-clock text-info me-2"></i>Wait for admin review</li>
                                    <li><i class="fas fa-clock text-info me-2"></i>Wait for approval</li>
                                    <li><i class="fas fa-clock text-info me-2"></i>Wait for payment confirmation</li>
                                    <li><i class="fas fa-play text-success me-2"></i>Start booking sessions!</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Need Help?</strong> Check your application status regularly and contact us if you have any questions about the process.
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

<!-- Cancel Program Modals -->
@foreach($userPrograms as $userProgram)
@if($userProgram->canBeCancelled())
<div class="modal fade" id="cancelProgramModal{{ $userProgram->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('client.programs.cancel', $userProgram) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Cancelling this program will stop all future sessions and you may lose access to program benefits.
                    </div>
                    
                    <p>You are about to cancel: <strong>{{ $userProgram->program->name }}</strong></p>
                    
                    <div class="mb-3">
                        <label for="cancellation_reason{{ $userProgram->id }}" class="form-label">Reason for Cancellation *</label>
                        <textarea class="form-control" id="cancellation_reason{{ $userProgram->id }}" 
                                  name="cancellation_reason" rows="4" required
                                  placeholder="Please tell us why you're cancelling this program..."></textarea>
                        <div class="form-text">This information helps us improve our services.</div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmCancellation{{ $userProgram->id }}" required>
                        <label class="form-check-label" for="confirmCancellation{{ $userProgram->id }}">
                            I understand that cancelling this program will stop all future sessions and I may lose access to program benefits.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Program</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Cancel Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection
