@extends('layouts.client')

@section('title', 'Payment Success')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-check fa-3x text-white"></i>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <h2 class="mb-3 text-success">Payment Successful!</h2>
                    <p class="lead text-muted mb-4">
                        Your payment has been processed successfully and your program has been activated.
                    </p>

                    <!-- Package Details -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-box me-2"></i>Your Program Details
                            </h5>
                        </div>
                        <div class="card-body text-start">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Program Name</h6>
                                    <p class="mb-0"><strong>{{ $userProgram->program->name }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Package Type</h6>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ $userProgram->program->formatted_subscription_type }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            @if($userProgram->program->description)
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Description</h6>
                                <p class="mb-0">{{ $userProgram->program->description }}</p>
                            </div>
                            @endif

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Contract Duration</h6>
                                    <p class="mb-0"><strong>3 months</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Sessions per Month</h6>
                                    <p class="mb-0"><strong>{{ $userProgram->program->monthly_sessions ?? 0 }} sessions</strong></p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Payment Type</h6>
                                    <p class="mb-0">
                                        <strong>
                                            {{ $userProgram->payment_type === 'monthly' ? 'Monthly Payments' : 'One-Time Payment' }}
                                        </strong>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-1">Amount Paid</h6>
                                    <p class="mb-0"><strong>${{ number_format($userProgram->amount_paid ?? 0, 0) }}</strong></p>
                                </div>
                            </div>

                            @if($userProgram->payment_reference)
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Payment Reference</h6>
                                <p class="mb-0"><code>{{ $userProgram->payment_reference }}</code></p>
                            </div>
                            @endif

                            @if($userProgram->program->features && count($userProgram->program->features) > 0)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Package Features</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($userProgram->program->features as $feature)
                                        <li class="mb-1">
                                            <i class="fas fa-check-circle text-success me-2"></i>{{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="alert alert-success mb-4" role="alert">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-check-circle me-2"></i>Program Activated
                        </h5>
                        <p class="mb-0">
                            Your program is now active! You can start booking sessions immediately.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('client.appointments') }}" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-calendar-plus me-2"></i>Book Your First Session
                        </a>
                        <a href="{{ route('client.programs') }}" class="btn btn-outline-secondary btn-lg px-5">
                            <i class="fas fa-list me-2"></i>View My Programs
                        </a>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-4">
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            A confirmation email has been sent to your registered email address.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

