@extends('layouts.client')

@section('title', 'Profile - Client')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user-circle me-2 text-primary"></i>My Profile
    </h1>
</div>

<!-- Profile Information -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Personal Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Full Name</label>
                        <p class="form-control-plaintext">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Email Address</label>
                        <p class="form-control-plaintext">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Phone Number</label>
                        <p class="form-control-plaintext">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Member Since</label>
                        <p class="form-control-plaintext">{{ $user->created_at->format('F j, Y') }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Agreement Status</label>
                        <div>
                            @if($user->hasSignedAgreement())
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>{{ $user->agreement_status_text }}
                                </span>
                                @if($user->agreement_uploaded_at)
                                    <small class="text-muted d-block mt-1">
                                        Uploaded on {{ $user->agreement_uploaded_at->format('M j, Y \a\t g:i A') }}
                                    </small>
                                @endif
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $user->agreement_status_text }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">Account Type</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-primary">
                                <i class="fas fa-user me-1"></i>Client
                            </span>
                        </p>
                    </div>
                </div>

                @if($user->hasSignedAgreement() && $user->agreement_url)
                <div class="row">
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted">Signed Agreement</label>
                        <div>
                            <a href="{{ $user->agreement_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Download Agreement
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Profile Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Your Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ $appointments->count() }}</h4>
                            <small class="text-muted">Total Sessions</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success mb-1">{{ $appointments->where('status', 'completed')->count() }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-warning mb-1">{{ $appointments->where('status', 'pending')->count() }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-info mb-1">{{ $appointments->where('status', 'confirmed')->count() }}</h4>
                        <small class="text-muted">Confirmed</small>
                    </div>
                </div>

                <hr>

                <div class="text-center">
                    <h4 class="text-primary mb-1">{{ $appointments->where('status', 'completed')->count() * 1 }}</h4>
                    <small class="text-muted">Hours Coached</small>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('client.appointments') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt me-2"></i>View Appointments
                    </a>
                    <a href="{{ route('client.messages') }}" class="btn btn-outline-success">
                        <i class="fas fa-comments me-2"></i>Send Message
                    </a>
                    <a href="{{ route('booking') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Book New Session
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Recent Appointments
                </h5>
            </div>
            <div class="card-body">
                @if($appointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Program</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments->take(10) as $appointment)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $appointment->appointment_date->format('M j, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $appointment->formatted_time }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $color = $statusColors[$appointment->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $appointment->program ?? 'N/A' }}</td>
                                    <td>
                                        @if($appointment->notes)
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                                  title="{{ $appointment->notes }}">
                                                {{ Str::limit($appointment->notes, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">No notes</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($appointments->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('client.appointments') }}" class="btn btn-outline-primary">
                                View All Appointments
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No appointments yet</h6>
                        <p class="text-muted">Book your first session to get started with your coaching journey.</p>
                        <a href="{{ route('booking') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Book Session
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
