@extends('layouts.client')

@section('title', $program->name . ' - Program Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $program->name }}</h1>
    <a href="{{ route('client.programs') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Programs
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Program Overview</h5>
            </div>
            <div class="card-body">
                <p class="lead">{{ $program->description }}</p>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <div class="h3 text-primary mb-1">{{ $program->formatted_price }}</div>
                            <small class="text-muted">Total Investment</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <div class="h3 text-success mb-1">{{ $program->duration_weeks }}</div>
                            <small class="text-muted">Weeks Duration</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <div class="h3 text-info mb-1">{{ $program->sessions_included }}</div>
                            <small class="text-muted">Sessions Included</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="border rounded p-3">
                            <div class="h3 text-warning mb-1">{{ round($program->price / $program->sessions_included, 2) }}</div>
                            <small class="text-muted">Per Session</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">What's Included</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($program->features as $feature)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                            <span>{{ $feature }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Program Structure</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6>Initial Assessment & Goal Setting</h6>
                            <p class="text-muted">We'll start with a comprehensive assessment to understand your current situation and define clear, achievable goals.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6>Regular Coaching Sessions</h6>
                            <p class="text-muted">{{ $program->sessions_included }} one-on-one sessions over {{ $program->duration_weeks }} weeks, tailored to your specific needs and goals.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6>Ongoing Support & Accountability</h6>
                            <p class="text-muted">Email support between sessions, progress tracking, and continuous guidance to ensure you stay on track.</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6>Final Review & Next Steps</h6>
                            <p class="text-muted">Comprehensive review of your progress and development of a plan for continued growth beyond the program.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @php
            $userProgram = Auth::user()->userPrograms()->where('program_id', $program->id)->first();
            $isSelected = $userProgram !== null && $userProgram->status !== \App\Models\UserProgram::STATUS_CANCELLED;
        @endphp
        <div class="card {{ $isSelected ? 'border-success' : '' }}">
            <div class="card-header {{ $isSelected ? 'bg-success text-white' : '' }}">
                <h5 class="card-title mb-0">
                    @if($isSelected)
                        <i class="fas fa-check-circle me-2"></i>Program Status
                    @else
                        Ready to Get Started?
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($isSelected)
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Current Status:</strong> {{ $userProgram->status_display_text }}
                            @if($userProgram->status === \App\Models\UserProgram::STATUS_ACTIVE)
                                <br><small class="text-success">You can now book sessions!</small>
                            @elseif($userProgram->status === \App\Models\UserProgram::STATUS_PENDING)
                                <br><small class="text-warning">Your application is under review.</small>
                            @elseif($userProgram->status === \App\Models\UserProgram::STATUS_AGREEMENT_SENT)
                                <br><small class="text-info">Please check your email for the agreement.</small>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center mb-4">
                    <div class="h2 text-primary mb-2">{{ $program->formatted_price }}</div>
                    <p class="text-muted">One-time payment for complete program</p>
                </div>

                <div class="mb-4">
                    <h6>What happens next?</h6>
                    <ol class="small">
                        <li>Submit your program application</li>
                        <li>Receive and review the program agreement</li>
                        <li>Sign and upload the agreement</li>
                        <li>Complete payment upon approval</li>
                        <li>Begin your coaching journey!</li>
                    </ol>
                </div>
                @endif

                <form action="{{ route('client.programs.select') }}" method="POST">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $program->id }}">
                    
                    @php
                        $userProgram = Auth::user()->userPrograms()->where('program_id', $program->id)->first();
                    @endphp
                    
                    @if($userProgram)
                        @switch($userProgram->status)
                            @case(\App\Models\UserProgram::STATUS_PENDING)
                                <button type="button" class="btn btn-warning w-100 btn-lg" disabled>
                                    <i class="fas fa-clock me-2"></i>Pending Review
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_AGREEMENT_SENT)
                                <button type="button" class="btn btn-info w-100 btn-lg" disabled>
                                    <i class="fas fa-file-contract me-2"></i>Agreement Sent
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_AGREEMENT_UPLOADED)
                                <button type="button" class="btn btn-primary w-100 btn-lg" disabled>
                                    <i class="fas fa-upload me-2"></i>Agreement Uploaded
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_APPROVED)
                                <button type="button" class="btn btn-success w-100 btn-lg" disabled>
                                    <i class="fas fa-check me-2"></i>Approved
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_PAYMENT_REQUESTED)
                                <button type="button" class="btn btn-warning w-100 btn-lg" disabled>
                                    <i class="fas fa-credit-card me-2"></i>Payment Requested
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_PAYMENT_COMPLETED)
                                <button type="button" class="btn btn-success w-100 btn-lg" disabled>
                                    <i class="fas fa-check-circle me-2"></i>Payment Completed
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_ACTIVE)
                                <button type="button" class="btn btn-success w-100 btn-lg" disabled>
                                    <i class="fas fa-star me-2"></i>Active Program
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_REJECTED)
                                <button type="button" class="btn btn-danger w-100 btn-lg" disabled>
                                    <i class="fas fa-times me-2"></i>Rejected
                                </button>
                                @break
                            @case(\App\Models\UserProgram::STATUS_CANCELLED)
                                <form action="{{ route('client.programs.select') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="program_id" value="{{ $program->id }}">
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="fas fa-rocket me-2"></i>Select This Program
                                    </button>
                                </form>
                                @break
                            @default
                                <button type="button" class="btn btn-secondary w-100 btn-lg" disabled>
                                    <i class="fas fa-question me-2"></i>{{ ucfirst($userProgram->status) }}
                                </button>
                        @endswitch
                    @else
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-rocket me-2"></i>Select This Program
                        </button>
                    @endif
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Secure payment processing
                    </small>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Questions?</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted">Have questions about this program or need more information?</p>
                <a href="{{ route('client.messages') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-envelope me-1"></i>Contact Lana
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #dee2e6;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: #333;
}

.timeline-content p {
    margin-bottom: 0;
    font-size: 0.9rem;
}
</style>
@endsection
