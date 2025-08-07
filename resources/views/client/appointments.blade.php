@extends('layouts.client')

@section('title', 'Appointments - Client Portal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">My Appointments</h1>
    <a href="{{ route('booking') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Book New Session
    </a>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="appointmentTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
            <i class="fas fa-calendar-check me-2"></i>Upcoming
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
            <i class="fas fa-calendar-times me-2"></i>Past
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="appointmentTabsContent">
    <!-- Upcoming Appointments -->
    <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
        @if(isset($upcomingAppointments) && count($upcomingAppointments) > 0)
            <div class="row">
                @foreach($upcomingAppointments as $appointment)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Coaching Session</h6>
                                <span class="badge bg-primary">{{ $appointment->status }}</span>
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
                                        {{ $appointment->appointment_time }}
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
                    <i class="fas fa-plus me-2"></i>Book Your First Session
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
                                <span class="badge bg-secondary">Completed</span>
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
                                        {{ $appointment->appointment_time }}
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
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-redo me-1"></i>Book Again
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-check text-muted mb-3" style="font-size: 4rem;"></i>
                <h4 class="text-muted">No past appointments</h4>
                <p class="text-muted">Your completed sessions will appear here.</p>
            </div>
        @endif
    </div>
</div>

<!-- Reschedule Modal Template -->
@if(isset($upcomingAppointments))
    @foreach($upcomingAppointments as $appointment)
        <div class="modal fade" id="rescheduleModal{{ $appointment->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reschedule Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('client.appointments.reschedule', $appointment->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="new_date" class="form-label">New Date</label>
                                <input type="date" class="form-control" id="new_date" name="new_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_time" class="form-label">New Time</label>
                                <select class="form-select" id="new_time" name="new_time" required>
                                    <option value="">Select time...</option>
                                    <option value="09:00">9:00 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="14:00">2:00 PM</option>
                                    <option value="15:00">3:00 PM</option>
                                    <option value="16:00">4:00 PM</option>
                                    <option value="17:00">5:00 PM</option>
                                </select>
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

        <!-- Cancel Modal -->
        <div class="modal fade" id="cancelModal{{ $appointment->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to cancel your appointment on 
                           {{ $appointment->appointment_date->format('l, F j, Y') }}?</p>
                        <p class="text-muted">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Appointment</button>
                        <form method="POST" action="{{ route('client.appointments.cancel', $appointment->id) }}" class="d-inline">
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
@endsection 