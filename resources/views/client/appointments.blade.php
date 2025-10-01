@extends('layouts.client')

@section('title', 'Appointments - Client')

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
    <h1 class="h2">My Appointments</h1>
    @php
        $activePrograms = Auth::user()->userPrograms()->where('status', \App\Models\UserProgram::STATUS_ACTIVE)->count();
    @endphp
    @if($activePrograms > 0)
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookNewSessionModal">
            <i class="fas fa-plus me-2"></i>Book New Session
        </button>
    @else
        <a href="{{ route('client.programs') }}" class="btn btn-primary">
            <i class="fas fa-graduation-cap me-2"></i>Select Program First
        </a>
    @endif
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="appointmentTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
            <i class="fas fa-clock me-2"></i>Pending
            @if(count($pendingBookings) > 0)
                <span class="badge bg-warning ms-1">{{ count($pendingBookings) }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="suggested-tab" data-bs-toggle="tab" data-bs-target="#suggested" type="button" role="tab">
            <i class="fas fa-lightbulb me-2"></i>Alternative Times
            @if(count($suggestedBookings) > 0)
                <span class="badge bg-info ms-1">{{ count($suggestedBookings) }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
            <i class="fas fa-calendar-check me-2"></i>Upcoming
            @if(count($upcomingAppointments) > 0)
                <span class="badge bg-success ms-1">{{ count($upcomingAppointments) }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
            <i class="fas fa-calendar-times me-2"></i>Past
            @if(count($pastAppointments) > 0)
                <span class="badge bg-secondary ms-1">{{ count($pastAppointments) }}</span>
            @endif
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="appointmentTabsContent">
    <!-- Pending Bookings -->
    <div class="tab-pane fade show active" id="pending" role="tabpanel">
        @if(isset($pendingBookings) && count($pendingBookings) > 0)
            <div class="row">
                @foreach($pendingBookings as $booking)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-warning">
                            <div class="card-header d-flex justify-content-between align-items-center bg-warning text-dark">
                                <h6 class="mb-0">Pending Booking</h6>
                                <span class="badge bg-warning text-dark">Pending Review</span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $booking->preferred_date->format('l, F j, Y') }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $booking->formatted_time }}
                                    </span>
                                </div>
                                @if($booking->program)
                                    <div class="mb-3">
                                        <i class="fas fa-tag text-muted me-2"></i>
                                        <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $booking->program)) }}</span>
                                    </div>
                                @endif
                                @if($booking->message)
                                    <div class="mb-3">
                                        <i class="fas fa-comment text-muted me-2"></i>
                                        <span class="text-muted">{{ Str::limit($booking->message, 100) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-light">
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Your booking is under review. We'll notify you once confirmed.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-check text-muted mb-3" style="font-size: 4rem;"></i>
                <h4 class="text-muted">No pending bookings</h4>
                <p class="text-muted">All your bookings have been processed.</p>
            </div>
        @endif
    </div>

    <!-- Suggested Alternative Times -->
    <div class="tab-pane fade" id="suggested" role="tabpanel">
        @if(isset($suggestedBookings) && count($suggestedBookings) > 0)
            <div class="row">
                @foreach($suggestedBookings as $booking)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-info">
                            <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                                <h6 class="mb-0">Alternative Time Suggested</h6>
                                <span class="badge bg-info text-white">Alternative Suggested</span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $booking->preferred_date->format('l, F j, Y') }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $booking->formatted_time }}
                                    </span>
                                </div>
                                @if($booking->program)
                                    <div class="mb-3">
                                        <i class="fas fa-tag text-muted me-2"></i>
                                        <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $booking->program)) }}</span>
                                    </div>
                                @endif
                                @if($booking->admin_suggestion)
                                    <div class="mb-3">
                                        <i class="fas fa-comment-dots text-info me-2"></i>
                                        <span class="text-info fw-bold">Admin Message:</span>
                                        <br>
                                        <span class="text-muted">{{ $booking->admin_suggestion }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#acceptModal{{ $booking->id }}">
                                        <i class="fas fa-check me-1"></i>Accept Time
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $booking->id }}">
                                        <i class="fas fa-times me-1"></i>Reject Time
                                    </button>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modifyModal{{ $booking->id }}">
                                        <i class="fas fa-edit me-1"></i>Request Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accept Modal -->
                    <div class="modal fade" id="acceptModal{{ $booking->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Accept Suggested Time</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('client.bookings.accept', $booking) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>Are you sure you want to accept this suggested time?</p>
                                        <div class="alert alert-info">
                                            <strong>Date:</strong> {{ $booking->preferred_date->format('M d, Y') }}<br>
                                            <strong>Time:</strong> {{ $booking->preferred_time }}
                                        </div>
                                        <p class="text-muted">Once accepted, we will convert this to a confirmed appointment.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success">Accept Time</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal{{ $booking->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Reject Suggested Time</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('client.bookings.reject', $booking) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>Please let us know why this time doesn't work for you:</p>
                                        <div class="mb-3">
                                            <label for="rejection_reason{{ $booking->id }}" class="form-label">Reason for Rejection *</label>
                                            <textarea class="form-control" id="rejection_reason{{ $booking->id }}" 
                                                      name="rejection_reason" rows="3" required
                                                      placeholder="e.g., I have another commitment, I prefer morning sessions, etc."></textarea>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-info-circle me-2"></i>
                                            We'll use this feedback to suggest a better time for you.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Reject Time</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modify Modal -->
                    <div class="modal fade" id="modifyModal{{ $booking->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Request Changes to Suggested Time</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('client.bookings.modify', $booking) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <p>Please suggest a different time that works better for you:</p>
                                        <div class="mb-3">
                                            <label for="new_date{{ $booking->id }}" class="form-label">Preferred Date *</label>
                                            <input type="date" class="form-control" id="new_date{{ $booking->id }}" 
                                                   name="new_date" required min="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_booking_type{{ $booking->id }}" class="form-label">Session Type *</label>
                                            <select class="form-control" id="new_booking_type{{ $booking->id }}" name="new_booking_type" required>
                                                <option value="">Select session type</option>
                                                <option value="in-office" {{ $booking->booking_type == 'in-office' ? 'selected' : '' }}>In-Office</option>
                                                <option value="virtual" {{ $booking->booking_type == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_time{{ $booking->id }}" class="form-label">Preferred Time *</label>
                                            <select class="form-control" id="new_time{{ $booking->id }}" name="new_time" required disabled>
                                                <option value="">Please select date and session type first</option>
                                            </select>
                                            <div class="form-text" id="modify-time-info{{ $booking->id }}">
                                                Select a date and session type to see available time slots.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="modification_reason{{ $booking->id }}" class="form-label">Reason for Change *</label>
                                            <textarea class="form-control" id="modification_reason{{ $booking->id }}" 
                                                      name="modification_reason" rows="3" required
                                                      placeholder="e.g., I have a conflict, I prefer different hours, etc."></textarea>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            We'll review your request and get back to you.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Request Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-lightbulb text-muted mb-3" style="font-size: 4rem;"></i>
                <h4 class="text-muted">No alternative times suggested</h4>
                <p class="text-muted">All your bookings are proceeding as originally scheduled.</p>
            </div>
        @endif
    </div>

    <!-- Upcoming Appointments -->
    <div class="tab-pane fade" id="upcoming" role="tabpanel">
        @if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
            <div class="row">
                @foreach($upcomingAppointments as $appointment)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Coaching Session</h6>
                                <span class="badge bg-{{ $appointment->status_badge_color }}">{{ ucfirst($appointment->status) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $appointment->appointment_date->format('l, F j, Y') }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $appointment->formatted_time }}
                                    </span>
                                </div>
                                @if($appointment->message)
                                    <div class="mb-3">
                                        <i class="fas fa-comment text-muted me-2"></i>
                                        <span class="text-muted">{{ Str::limit($appointment->message, 100) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $appointment->id }}">
                                        <i class="fas fa-edit me-1"></i>Reschedule
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $appointment->id }}">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 4rem;"></i>
                <h4 class="text-muted">No upcoming appointments</h4>
                <p class="text-muted">You don't have any scheduled sessions at the moment.</p>
                <a href="{{ route('booking') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Book a New Session
                </a>
            </div>
        @endif
    </div>

    <!-- Past Appointments -->
    <div class="tab-pane fade" id="past" role="tabpanel">
        @if(isset($pastAppointments) && count($pastAppointments) > 0)
            <div class="row">
                @foreach($pastAppointments as $appointment)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Coaching Session</h6>
                                <span class="badge bg-{{ $appointment->status_badge_color }}">{{ ucfirst($appointment->status) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $appointment->appointment_date->format('l, F j, Y') }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <span class="text-muted">
                                        {{ $appointment->formatted_time }}
                                    </span>
                                </div>
                                @if($appointment->message)
                                    <div class="mb-3">
                                        <i class="fas fa-comment text-muted me-2"></i>
                                        <span class="text-muted">{{ Str::limit($appointment->message, 100) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-light">
                                <div class="text-center">
                                    <small class="text-muted">
                                        @if($appointment->status === 'completed')
                                            <i class="fas fa-check-circle me-1"></i>
                                            Session completed
                                        @else
                                            <i class="fas fa-calendar-times me-1"></i>
                                            Past session
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 4rem;"></i>
                <h4 class="text-muted">No past appointments</h4>
                <p class="text-muted">You haven't completed any sessions yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Reschedule Modals -->
@if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
    @foreach($upcomingAppointments as $appointment)
        <div class="modal fade" id="rescheduleModal{{ $appointment->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reschedule Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('client.appointments.reschedule', $appointment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="appointment_date{{ $appointment->id }}" class="form-label">New Date</label>
                                <input type="date" class="form-control" id="appointment_date{{ $appointment->id }}" 
                                       name="appointment_date" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label for="appointment_time{{ $appointment->id }}" class="form-label">New Time</label>
                                <select class="form-control" id="appointment_time{{ $appointment->id }}" name="appointment_time" required disabled>
                                    <option value="">Please select date first</option>
                                </select>
                                <div class="form-text" id="reschedule-time-info{{ $appointment->id }}">
                                    Select a date to see available time slots.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Reschedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Cancel Modals -->
@if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
    @foreach($upcomingAppointments as $appointment)
        <div class="modal fade" id="cancelModal{{ $appointment->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to cancel this appointment?</p>
                        <div class="alert alert-warning">
                            <strong>Date:</strong> {{ $appointment->appointment_date->format('M d, Y') }}<br>
                                                                    <strong>Time:</strong> {{ $appointment->formatted_time }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Appointment</button>
                        <form action="{{ route('client.appointments.cancel', $appointment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Book New Session Modal -->
<div class="modal fade" id="bookNewSessionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book a New Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('client.book-session') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Client Information (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                        </div>
                    </div>
                    
                    @if(Auth::user()->phone)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->phone }}" readonly>
                        </div>
                    </div>
                    @endif

                    <!-- Session Details -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="preferred_date" class="form-label">Preferred Date *</label>
                            <input type="date" class="form-control @error('preferred_date') is-invalid @enderror" 
                                   id="preferred_date" name="preferred_date" required min="{{ date('Y-m-d') }}" 
                                   value="{{ old('preferred_date') }}">
                            @error('preferred_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="booking_type" class="form-label">Session Type *</label>
                            <select class="form-control @error('booking_type') is-invalid @enderror" 
                                    id="booking_type" name="booking_type" required>
                                <option value="">Select session type</option>
                                <option value="in-office" {{ old('booking_type') == 'in-office' ? 'selected' : '' }}>In-Office</option>
                                <option value="virtual" {{ old('booking_type') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                            </select>
                            @error('booking_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="preferred_time" class="form-label">Preferred Time *</label>
                        <select class="form-control @error('preferred_time') is-invalid @enderror" 
                                id="preferred_time" name="preferred_time" required disabled>
                            <option value="">Please select date and session type first</option>
                        </select>
                        @error('preferred_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text" id="time-slots-info">
                            Select a date and session type to see available time slots.
                        </div>
                        <div id="slot-status" class="mt-2" style="display: none;">
                            <div class="alert alert-success" id="slot-available" style="display: none;">
                                <i class="fas fa-check-circle me-2"></i>
                                <span id="available-count">0</span> time slots available
                            </div>
                            <div class="alert alert-warning" id="slot-unavailable" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No available slots for this date and session type
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Additional Message (Optional)</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  id="message" name="message" rows="3" 
                                  placeholder="Any specific topics you'd like to discuss or special requirements...">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Your session request will be reviewed and confirmed by our team. You'll receive a notification once confirmed.
                    </div>

                    <!-- Schedule Information -->
                    <div class="alert alert-light border">
                        <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Availability Schedule</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Monday:</strong><br>
                                    Virtual: 10:00 AM - 6:00 PM<br>
                                    In-Office: 6:00 PM - 9:00 PM
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Tuesday:</strong><br>
                                    In-Office: 8:30 AM - 5:00 PM
                                </small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Wednesday:</strong><br>
                                    In-Office: 9:00 AM - 12:00 PM<br>
                                    Virtual: 12:00 PM - 5:00 PM
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Thursday:</strong><br>
                                    In-Office: 9:00 AM - 12:00 PM<br>
                                    Virtual: 12:00 PM - 5:00 PM
                                </small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Friday:</strong><br>
                                    Virtual: 10:00 AM - 4:00 PM
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Weekend:</strong><br>
                                    By Appointments Only
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Book Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const preferredDate = document.getElementById('preferred_date');
    const bookingType = document.getElementById('booking_type');
    const preferredTime = document.getElementById('preferred_time');
    const timeSlotsInfo = document.getElementById('time-slots-info');
    
    
    // Set default date to next available weekday when modal opens
    const bookModal = document.getElementById('bookNewSessionModal');
    
    if (bookModal) {
        bookModal.addEventListener('show.bs.modal', function() {
            // Find next available weekday (Monday-Friday)
            const today = new Date();
            let nextWeekday = new Date(today);
            
            // Skip weekends (Saturday = 6, Sunday = 0)
            do {
                nextWeekday.setDate(nextWeekday.getDate() + 1);
            } while (nextWeekday.getDay() === 0 || nextWeekday.getDay() === 6);
            
            if (preferredDate) {
                preferredDate.value = nextWeekday.toISOString().split('T')[0];
            }
            updateTimeSlots();
        });
    }
    
    // Update time slots when date or booking type changes
    if (preferredDate) {
        preferredDate.addEventListener('change', updateTimeSlots);
    }
    if (bookingType) {
        bookingType.addEventListener('change', updateTimeSlots);
    }
    
    function updateTimeSlots() {
        const date = preferredDate?.value;
        const type = bookingType?.value;
        
        
        if (!date || !type) {
            preferredTime.disabled = true;
            preferredTime.innerHTML = '<option value="">Please select date and session type first</option>';
            timeSlotsInfo.textContent = 'Select a date and session type to see available time slots.';
            document.getElementById('slot-status').style.display = 'none';
            return;
        }
        
        // Check if date is available
        fetch(`{{ url('/api/available-slots') }}?date=${date}&type=${type}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                preferredTime.disabled = false;
                preferredTime.innerHTML = '<option value="">Select time</option>';
                
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;
                        option.textContent = formatTime(slot);
                        preferredTime.appendChild(option);
                    });
                    timeSlotsInfo.textContent = `Available slots: ${data.slots.length} time slots`;
                    
                    // Show success status
                    document.getElementById('slot-status').style.display = 'block';
                    document.getElementById('slot-available').style.display = 'block';
                    document.getElementById('slot-unavailable').style.display = 'none';
                    document.getElementById('available-count').textContent = data.slots.length;
                } else {
                    preferredTime.innerHTML = '<option value="">No slots available for this date/type</option>';
                    timeSlotsInfo.textContent = 'No available slots for this date and session type.';
                    
                    // Show warning status
                    document.getElementById('slot-status').style.display = 'block';
                    document.getElementById('slot-available').style.display = 'none';
                    document.getElementById('slot-unavailable').style.display = 'block';
                    
                    // Show helpful message for weekends
                    const selectedDate = new Date(date);
                    const dayOfWeek = selectedDate.getDay();
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        document.getElementById('slot-unavailable').innerHTML = 
                            '<i class="fas fa-exclamation-triangle me-2"></i>No availability on weekends. Please select a weekday (Monday-Friday).';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching slots:', error);
                preferredTime.disabled = true;
                preferredTime.innerHTML = '<option value="">Error loading slots</option>';
                timeSlotsInfo.textContent = 'Error loading available slots.';
                
                // Show error status
                document.getElementById('slot-status').style.display = 'block';
                document.getElementById('slot-available').style.display = 'none';
                document.getElementById('slot-unavailable').style.display = 'block';
            });
    }
    
    function formatTime(time24) {
        const [hours, minutes] = time24.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }
    
    // Real-time slot availability check before form submission
    const bookingForm = document.querySelector('form[action="{{ route('client.book-session') }}"]');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const date = preferredDate?.value;
            const type = bookingType?.value;
            const time = preferredTime?.value;
            
            if (!date || !type || !time) {
                e.preventDefault();
                alert('Please select a date, session type, and time.');
                return;
            }
            
            // Check slot availability one more time before submission
            fetch(`{{ url('/api/check-slot') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    date: date,
                    time: time,
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    e.preventDefault();
                    alert('Sorry, this time slot is no longer available. Please select another time.');
                    updateTimeSlots(); // Refresh the time slots
                }
                // If available, let the form submit normally
            })
            .catch(error => {
                console.error('Error checking slot availability:', error);
                // Let the form submit anyway - server-side validation will catch it
            });
        });
    }
    
    // Initialize on page load if values are already set
    updateTimeSlots();
    
    // Handle modify modals - add event listeners for date and booking type changes
    document.querySelectorAll('[id^="new_date"]').forEach(function(dateInput) {
        const bookingId = dateInput.id.replace('new_date', '');
        const timeSelect = document.getElementById('new_time' + bookingId);
        const timeInfo = document.getElementById('modify-time-info' + bookingId);
        const bookingTypeSelect = document.getElementById('new_booking_type' + bookingId);
        
        if (dateInput && timeSelect && timeInfo) {
            dateInput.addEventListener('change', function() {
                updateModifyTimeSlots(bookingId, dateInput.value, bookingTypeSelect?.value, timeSelect, timeInfo);
            });
        }
        
        if (bookingTypeSelect && timeSelect && timeInfo) {
            bookingTypeSelect.addEventListener('change', function() {
                updateModifyTimeSlots(bookingId, dateInput?.value, bookingTypeSelect.value, timeSelect, timeInfo);
            });
        }
    });
    
    // Handle reschedule modals - add event listeners for date changes
    document.querySelectorAll('[id^="appointment_date"]').forEach(function(dateInput) {
        const appointmentId = dateInput.id.replace('appointment_date', '');
        const timeSelect = document.getElementById('appointment_time' + appointmentId);
        const timeInfo = document.getElementById('reschedule-time-info' + appointmentId);
        
        if (dateInput && timeSelect && timeInfo) {
            dateInput.addEventListener('change', function() {
                updateRescheduleTimeSlots(appointmentId, dateInput.value, timeSelect, timeInfo);
            });
        }
    });
    
    function updateModifyTimeSlots(bookingId, date, bookingType, timeSelect, timeInfo) {
        if (!date || !bookingType) {
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Please select date and session type first</option>';
            timeInfo.textContent = 'Select a date and session type to see available time slots.';
            return;
        }
        
        fetch(`{{ url('/api/available-slots') }}?date=${date}&type=${bookingType}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            timeSelect.disabled = false;
            timeSelect.innerHTML = '<option value="">Select time</option>';
            
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = formatTime(slot);
                    timeSelect.appendChild(option);
                });
                timeInfo.textContent = `Available slots: ${data.slots.length} time slots`;
            } else {
                timeSelect.innerHTML = '<option value="">No slots available for this date</option>';
                timeInfo.textContent = 'No available slots for this date.';
            }
        })
        .catch(error => {
            console.error('Error fetching slots for modify modal:', error);
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            timeInfo.textContent = 'Error loading available slots.';
        });
    }
    
    function updateRescheduleTimeSlots(appointmentId, date, timeSelect, timeInfo) {
        if (!date) {
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Please select date first</option>';
            timeInfo.textContent = 'Select a date to see available time slots.';
            return;
        }
        
        // For reschedule, we'll assume virtual as default since we don't have booking type info
        // In a real implementation, you might want to store the original booking type
        const bookingType = 'virtual';
        
        fetch(`{{ url('/api/available-slots') }}?date=${date}&type=${bookingType}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            timeSelect.disabled = false;
            timeSelect.innerHTML = '<option value="">Select time</option>';
            
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = formatTime(slot);
                    timeSelect.appendChild(option);
                });
                timeInfo.textContent = `Available slots: ${data.slots.length} time slots`;
            } else {
                timeSelect.innerHTML = '<option value="">No slots available for this date</option>';
                timeInfo.textContent = 'No available slots for this date.';
            }
        })
        .catch(error => {
            console.error('Error fetching slots for reschedule modal:', error);
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            timeInfo.textContent = 'Error loading available slots.';
        });
    }
});
</script>
@endpush