@extends('layouts.admin')

@section('title', 'Calendar - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Appointment & Booking Calendar</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="addAppointmentBtn">
                <i class="fas fa-plus me-2"></i>Add Appointment
            </button>
            <button class="btn btn-outline-success" id="addBookingBtn">
                <i class="fas fa-calendar-plus me-2"></i>Add Booking
            <!-- </button>
            <button class="btn btn-outline-secondary" onclick="exportCalendar()">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print
            </button> -->
        </div>
    </div>

    <div class="row">
        <!-- Calendar -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Calendar View
                        </h6>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="todayBtn">Today</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading calendar...</span>
                            </div>
                            <p class="mt-2">Loading calendar...</p>
                        </div>
                    </div>
                    <div id="calendar-fallback" style="display: none;">
                        <h6>Appointments List (Calendar failed to load)</h6>
                        @if($appointments->count() > 0)
                            <div class="list-group">
                                @foreach($appointments as $appointment)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $appointment->user->name }}</h6>
                                                <p class="mb-1">{{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->formatted_time }}</p>
                                            </div>
                                            <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : ($appointment->status === 'completed' ? 'info' : 'danger')) }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No appointments found. Create your first appointment using the "Add Appointment" button.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Today's Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">{{ $todayAppointments ?? 0 }}</h4>
                            <small class="text-muted">Sessions</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">{{ $todayBookings ?? 0 }}</h4>
                            <small class="text-muted">Bookings</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning">{{ $pendingAppointments ?? 0 }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Calendar Legend
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center">
                            <div class="calendar-legend-item bg-primary me-2"></div>
                            <small>Confirmed Appointments</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="calendar-legend-item bg-warning me-2"></div>
                            <small>Pending Appointments</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="calendar-legend-item bg-success me-2"></div>
                            <small>Client Bookings</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="calendar-legend-item bg-info me-2"></div>
                            <small>Completed Sessions</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Upcoming Events
                    </h6>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0 || $bookings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($appointments->take(5) as $appointment)
                                <div class="list-group-item appointment-item" data-appointment-id="{{ $appointment->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="fas fa-calendar-check text-primary me-1"></i>
                                                {{ $appointment->user->name }}
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                {{ $appointment->appointment_date->format('M d, Y') }} at 
                                                {{ $appointment->formatted_time }}
                                            </p>
                                            <small class="text-muted">{{ Str::limit($appointment->message ?? 'No notes', 50) }}</small>
                                        </div>
                                        <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : ($appointment->status === 'completed' ? 'info' : 'danger')) }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            
                            @foreach($bookings->take(3) as $booking)
                                <div class="list-group-item booking-item" data-booking-id="{{ $booking->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="fas fa-calendar-plus text-success me-1"></i>
                                                {{ $booking->name }}
                                            </h6>
                                            <p class="mb-1 text-muted">
                                                {{ $booking->preferred_date->format('M d, Y') }} at 
                                                {{ $booking->formatted_time }}
                                            </p>
                                            <small class="text-muted">{{ Str::limit($booking->message ?? 'No notes', 50) }}</small>
                                        </div>
                                        <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if(($appointments->count() + $bookings->count()) > 8)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.appointments') }}" class="btn btn-outline-primary btn-sm me-2">
                                    View Appointments
                                </a>
                                <a href="{{ route('admin.bookings') }}" class="btn btn-outline-success btn-sm">
                                    View Bookings
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No upcoming events</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check me-2"></i>Appointment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editAppointmentBtn">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
                <button type="button" class="btn btn-danger" id="cancelAppointmentBtn">
                    <i class="fas fa-times me-1"></i>Cancel Appointment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Day Details Modal -->
<div class="modal fade" id="dayDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-day me-2"></i>Day Details - <span id="selectedDate"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Appointments for the day -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-calendar-check me-2"></i>Appointments
                                </h6>
                            </div>
                            <div class="card-body" id="dayAppointments">
                                <!-- Appointments will be loaded here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bookings for the day -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0 text-success">
                                    <i class="fas fa-calendar-plus me-2"></i>Client Bookings
                                </h6>
                            </div>
                            <div class="card-body" id="dayBookings">
                                <!-- Bookings will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Summary -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h5 class="text-primary" id="totalAppointments">0</h5>
                                        <small class="text-muted">Total Appointments</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-success" id="totalBookings">0</h5>
                                        <small class="text-muted">Total Bookings</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-warning" id="pendingCount">0</h5>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h5 class="text-info" id="confirmedCount">0</h5>
                                        <small class="text-muted">Confirmed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addEventForDay()">
                    <i class="fas fa-plus me-1"></i>Add Event for This Day
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Choice Modal -->
<div class="modal fade" id="eventChoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Choose Event Type
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-4">What type of event would you like to add for <strong><span id="eventChoiceDate"></span></strong>?</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-primary btn-lg" onclick="addAppointmentForDate()">
                        <i class="fas fa-calendar-check me-2"></i>Appointment
                    </button>
                    <button type="button" class="btn btn-success btn-lg" onclick="addBookingForDate()">
                        <i class="fas fa-calendar-plus me-2"></i>Client Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Appointment Modal -->
<div class="modal fade" id="addEditAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEditModalTitle">Add New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="appointmentForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client_id" class="form-label">Client *</label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Select Client</option>
                                @foreach($clients ?? [] as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="appointment_date" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="appointment_time" class="form-label">Time *</label>
                            <select class="form-select" id="appointment_time" name="appointment_time" required>
                                <option value="">Select Time</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about this appointment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
/* Calendar Legend Styling */
.calendar-legend-item {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    display: inline-block;
}

/* Calendar Event Styling */
.appointment-event {
    border-radius: 4px;
    font-weight: 500;
}

.booking-event {
    border-radius: 4px;
    font-weight: 500;
}

/* Day Details Modal Styling */
.appointment-item, .booking-item {
    transition: all 0.2s ease;
    cursor: pointer;
}

.appointment-item:hover, .booking-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.appointment-item {
    border-left: 4px solid #032a57 !important;
}

.booking-item {
    border-left: 4px solid #198754 !important;
}

/* Calendar Container Styling */
#calendar {
    min-height: 600px;
}

/* Responsive Calendar */
@media (max-width: 768px) {
    .fc-header-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
}

/* Toast Container */
.toast-container {
    z-index: 9999;
}

/* Loading States */
.calendar-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
}

/* Event Tooltips */
.fc-event {
    cursor: pointer;
}

.fc-event:hover {
    opacity: 0.8;
}

/* Day Click Styling */
.fc-day:hover {
    background-color: rgba(0,0,0,0.05);
    cursor: pointer;
}

.fc-day.fc-day-today {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

/* Status Badge Colors */
.badge.bg-pending {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-confirmed {
    background-color: #198754 !important;
}

.badge.bg-completed {
    background-color: #0dcaf0 !important;
}

.badge.bg-cancelled {
    background-color: #dc3545 !important;
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Calendar page loaded');
    
    let calendar;
    let currentAppointmentId = null;
    let isLoading = false;

    // Wait a bit for CDN to load
    setTimeout(() => {
        console.log('Attempting to initialize calendar...');
        initializeCalendar();
    }, 1000);

    // Add debug logging for day clicks
    console.log('Setting up calendar event listeners...');

    // Navigation buttons
    document.getElementById('prevMonth').addEventListener('click', function() {
        if (!isLoading) {
            calendar.prev();
        }
    });
    
    document.getElementById('nextMonth').addEventListener('click', function() {
        if (!isLoading) {
            calendar.next();
        }
    });

    document.getElementById('todayBtn').addEventListener('click', function() {
        if (!isLoading) {
            calendar.today();
        }
    });

    // Add appointment button
    document.getElementById('addAppointmentBtn').addEventListener('click', function() {
        if (!isLoading) {
            showAddAppointmentModal();
        }
    });

    // Add booking button
    document.getElementById('addBookingBtn').addEventListener('click', function() {
        if (!isLoading) {
            showAddBookingModal();
        }
    });

    // Appointment form submission
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!isLoading) {
            saveAppointment();
        }
    });

    // Sidebar appointment clicks
    document.querySelectorAll('.appointment-item').forEach(item => {
        item.addEventListener('click', function() {
            if (!isLoading) {
                const appointmentId = this.dataset.appointmentId;
                showAppointmentDetailsById(appointmentId);
            }
        });
    });

    // Sidebar booking clicks
    document.querySelectorAll('.booking-item').forEach(item => {
        item.addEventListener('click', function() {
            if (!isLoading) {
                const bookingId = this.dataset.bookingId;
                showBookingDetailsById(bookingId);
            }
        });
    });

    // Edit appointment button
    document.getElementById('editAppointmentBtn').addEventListener('click', function() {
        if (currentAppointmentId) {
            showEditAppointmentModal(currentAppointmentId);
        }
    });

    // Cancel appointment button
    document.getElementById('cancelAppointmentBtn').addEventListener('click', function() {
        if (currentAppointmentId) {
            cancelAppointment(currentAppointmentId);
        }
    });
});

function initializeCalendar() {
    console.log('Initializing calendar...');
    
    // Debug: Check if FullCalendar is loaded
    console.log('FullCalendar loaded:', typeof FullCalendar !== 'undefined');
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar is not loaded!');
        document.getElementById('calendar').innerHTML = `
            <div class="alert alert-danger">
                <h5>Calendar Failed to Load</h5>
                <p>FullCalendar library could not be loaded. Please check your internet connection and try again.</p>
                <button class="btn btn-primary" onclick="location.reload()">Reload Page</button>
            </div>
        `;
        return;
    }

    // Initialize FullCalendar
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }
    
    console.log('Calendar element found:', calendarEl);
    console.log('Appointments count:', {{ $appointments->count() }});
    
    // Clear loading message
    calendarEl.innerHTML = '';
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        editable: true,
        selectable: false, // Changed from true to false to prevent automatic selection
        events: [
            // Appointments
            @foreach($appointments as $appointment)
            {
                id: 'appointment_{{ $appointment->id }}',
                title: '{{ $appointment->user->name }}',
                start: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ $appointment->appointment_time }}:00',
                end: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ $appointment->appointment_time }}:00',
                backgroundColor: '{{ $appointment->status === "confirmed" ? "#032a57" : ($appointment->status === "pending" ? "#730623" : ($appointment->status === "completed" ? "#032a57" : "#730623")) }}',
                borderColor: '{{ $appointment->status === "confirmed" ? "#032a57" : ($appointment->status === "pending" ? "#730623" : ($appointment->status === "completed" ? "#032a57" : "#730623")) }}',
                textColor: '{{ $appointment->status === "pending" ? "#000" : "#fff" }}',
                className: 'appointment-event bg-{{ $appointment->status }}',
                extendedProps: {
                    type: 'appointment',
                    status: '{{ $appointment->status }}',
                    notes: '{{ $appointment->message ?? "" }}',
                    clientEmail: '{{ $appointment->user->email }}',
                    eventId: '{{ $appointment->id }}'
                }
            },
            @endforeach
            
            // Bookings
            @foreach($bookings as $booking)
            {
                id: 'booking_{{ $booking->id }}',
                title: '{{ $booking->name }} (Booking)',
                start: '{{ $booking->preferred_date->format("Y-m-d") }}T{{ $booking->preferred_time }}:00',
                end: '{{ $booking->preferred_date->format("Y-m-d") }}T{{ $booking->preferred_time }}:00',
                backgroundColor: '#198754',
                borderColor: '#198754',
                textColor: '#fff',
                className: 'booking-event',
                extendedProps: {
                    type: 'booking',
                    status: '{{ $booking->status }}',
                    notes: '{{ $booking->message ?? "" }}',
                    clientEmail: '{{ $booking->email }}',
                    eventId: '{{ $booking->id }}'
                }
            },
            @endforeach
        ],
        eventClick: function(info) {
            if (!isLoading) {
                if (info.event.extendedProps.type === 'appointment') {
                    showAppointmentDetails(info.event);
                } else if (info.event.extendedProps.type === 'booking') {
                    showBookingDetails(info.event);
                }
            }
        },
        dayClick: function(info) {
            console.log('Day clicked!', info.dateStr);
            if (!isLoading) {
                console.log('Showing day details for:', info.dateStr);
                showDayDetails(info.dateStr);
            } else {
                console.log('Calendar is loading, ignoring day click');
            }
        },
        eventDrop: function(info) {
            if (!isLoading) {
                if (info.event.extendedProps.type === 'appointment') {
                    updateAppointmentDate(info.event);
                }
            }
        },
        dayMaxEvents: true,
        height: 'auto',
        editable: true,
        selectable: false, // Changed from true to false to prevent automatic selection
        // Removed the select function that was automatically showing appointment form
        loading: function(isLoading) {
            // Handle loading state
            if (isLoading) {
                showLoadingState();
            } else {
                hideLoadingState();
            }
        },
        eventDidMount: function(info) {
            // Add tooltip
            $(info.el).tooltip({
                title: `${info.event.title} - ${info.event.extendedProps.status}`,
                placement: 'top',
                trigger: 'hover'
            });
        }
    });
    
    // Debug: Log appointment count
    console.log('Appointments loaded:', {{ $appointments->count() }});
    
    try {
        calendar.render();
        console.log('Calendar rendered successfully');
        
        // Check if calendar actually rendered after a short delay
        setTimeout(() => {
            const calendarEl = document.getElementById('calendar');
            if (calendarEl && calendarEl.children.length === 0) {
                console.error('Calendar element is empty after render');
                showFallback();
            }
        }, 2000);
    } catch (error) {
        console.error('Error rendering calendar:', error);
        showFallback();
    }
}

function showFallback() {
    document.getElementById('calendar').style.display = 'none';
    document.getElementById('calendar-fallback').style.display = 'block';
}

function showLoadingState() {
    isLoading = true;
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl.querySelector('.calendar-loading')) {
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'calendar-loading';
        loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        calendarEl.appendChild(loadingDiv);
    }
}

function hideLoadingState() {
    isLoading = false;
    const loadingEl = document.querySelector('.calendar-loading');
    if (loadingEl) {
        loadingEl.remove();
    }
}

function showAppointmentDetails(event) {
    currentAppointmentId = event.extendedProps.eventId;
    
    const modalBody = document.getElementById('appointmentModalBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">
                    <i class="fas fa-user me-2"></i>Client Information
                </h6>
                <p><strong>Name:</strong> ${event.title}</p>
                <p><strong>Email:</strong> ${event.extendedProps.clientEmail}</p>
                <p><strong>Date:</strong> ${event.start.toLocaleDateString()}</p>
                <p><strong>Time:</strong> ${event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">
                    <i class="fas fa-calendar-check me-2"></i>Appointment Details
                </h6>
                <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(event.extendedProps.status)}">${event.extendedProps.status}</span></p>
                <p><strong>Notes:</strong> ${event.extendedProps.notes || 'No notes'}</p>
                <p><strong>Type:</strong> <span class="badge bg-primary">Appointment</span></p>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();
}

function showBookingDetails(event) {
    const modalBody = document.getElementById('appointmentModalBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-success">
                    <i class="fas fa-user me-2"></i>Client Information
                </h6>
                <p><strong>Name:</strong> ${event.title.replace(' (Booking)', '')}</p>
                <p><strong>Email:</strong> ${event.extendedProps.clientEmail}</p>
                <p><strong>Date:</strong> ${event.start.toLocaleDateString()}</p>
                <p><strong>Time:</strong> ${event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-success">
                    <i class="fas fa-calendar-plus me-2"></i>Booking Details
                </h6>
                <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(event.extendedProps.status)}">${event.extendedProps.status}</span></p>
                <p><strong>Notes:</strong> ${event.extendedProps.notes || 'No notes'}</p>
                <p><strong>Type:</strong> <span class="badge bg-success">Client Booking</span></p>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();
}

function showDayDetails(dateStr) {
    console.log('showDayDetails called with:', dateStr);
    const selectedDate = new Date(dateStr);
    const formattedDate = selectedDate.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    console.log('Formatted date:', formattedDate);
    document.getElementById('selectedDate').textContent = formattedDate;
    
    // Get appointments for the selected day
    const dayAppointments = @json($appointments).filter(apt => 
        apt.appointment_date === dateStr
    );
    
    // Get bookings for the selected day
    const dayBookings = @json($bookings).filter(booking => 
        booking.preferred_date === dateStr
    );
    
    // Display appointments
    const appointmentsContainer = document.getElementById('dayAppointments');
    if (dayAppointments.length > 0) {
        appointmentsContainer.innerHTML = dayAppointments.map(apt => `
            <div class="appointment-item mb-3 p-3 border rounded" data-appointment-id="${apt.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 text-primary">
                            <i class="fas fa-calendar-check me-2"></i>${apt.user.name}
                        </h6>
                        <p class="mb-1 text-muted">${apt.formatted_time}</p>
                        <small class="text-muted">${apt.message || 'No notes'}</small>
                    </div>
                    <span class="badge bg-${getStatusColor(apt.status)}">${apt.status}</span>
                </div>
            </div>
        `).join('');
    } else {
        appointmentsContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No appointments for this day</p>
            </div>
        `;
    }
    
    // Display bookings
    const bookingsContainer = document.getElementById('dayBookings');
    if (dayBookings.length > 0) {
        bookingsContainer.innerHTML = dayBookings.map(booking => `
            <div class="booking-item mb-3 p-3 border rounded" data-booking-id="${booking.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 text-success">
                            <i class="fas fa-calendar-plus me-2"></i>${booking.name}
                        </h6>
                        <p class="mb-1 text-muted">${booking.formatted_time}</p>
                        <small class="text-muted">${booking.message || 'No notes'}</small>
                    </div>
                    <span class="badge bg-${getStatusColor(booking.status)}">${booking.status.replace('_', ' ')}</span>
                </div>
            </div>
        `).join('');
    } else {
        bookingsContainer.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No bookings for this day</p>
            </div>
        `;
    }
    
    // Update summary counts
    document.getElementById('totalAppointments').textContent = dayAppointments.length;
    document.getElementById('totalBookings').textContent = dayBookings.length;
    
    const pendingCount = dayAppointments.filter(apt => apt.status === 'pending').length + 
                        dayBookings.filter(booking => booking.status === 'pending').length;
    const confirmedCount = dayAppointments.filter(apt => apt.status === 'confirmed').length + 
                          dayBookings.filter(booking => booking.status === 'confirmed').length;
    
    document.getElementById('pendingCount').textContent = pendingCount;
    document.getElementById('confirmedCount').textContent = confirmedCount;
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('dayDetailsModal'));
    modal.show();
}

function addEventForDay() {
    // Close the day details modal
    const dayModal = bootstrap.Modal.getInstance(document.getElementById('dayDetailsModal'));
    dayModal.hide();
    
    // Show choice modal first
    const choiceModal = new bootstrap.Modal(document.getElementById('eventChoiceModal'));
    choiceModal.show();
    
    // Set the selected date in the choice modal
    const selectedDate = document.getElementById('selectedDate').textContent;
    const dateMatch = selectedDate.match(/(\w+)\s+(\w+)\s+(\d+),\s+(\d+)/);
    if (dateMatch) {
        const month = new Date(Date.parse(selectedDate + ' 1, 2000')).getMonth() + 1;
        const day = dateMatch[3];
        const year = dateMatch[4];
        const formattedDate = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        document.getElementById('eventChoiceDate').textContent = formattedDate;
        document.getElementById('eventChoiceDate').dataset.date = formattedDate;
    }
}

function addAppointmentForDate() {
    const selectedDate = document.getElementById('eventChoiceDate').dataset.date;
    showAddAppointmentModal(selectedDate);
    const choiceModal = bootstrap.Modal.getInstance(document.getElementById('eventChoiceModal'));
    choiceModal.hide();
}

function addBookingForDate() {
    const selectedDate = document.getElementById('eventChoiceDate').dataset.date;
    // For now, redirect to the bookings page
    // In a real implementation, you'd show a modal form
    window.location.href = '{{ route("admin.bookings") }}?date=' + selectedDate;
    const choiceModal = bootstrap.Modal.getInstance(document.getElementById('eventChoiceModal'));
    choiceModal.hide();
}

function showAppointmentDetailsById(appointmentId) {
    showLoadingState();
    
    fetch(`/admin/appointments/${appointmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const event = {
                id: data.id,
                title: data.user.name,
                start: new Date(data.appointment_date + 'T' + data.appointment_time),
                extendedProps: {
                    status: data.status,
                    notes: data.notes,
                    clientEmail: data.user.email
                }
            };
            showAppointmentDetails(event);
        })
        .catch(error => {
            console.error('Error fetching appointment details:', error);
            showError('Error loading appointment details');
        })
        .finally(() => {
            hideLoadingState();
        });
}

function showBookingDetailsById(bookingId) {
    showLoadingState();
    
    // For now, we'll show a simple modal since we don't have a booking API endpoint
    // In a real implementation, you'd fetch the booking details from the server
    const modalBody = document.getElementById('appointmentModalBody');
    modalBody.innerHTML = `
        <div class="alert alert-info">
            <h6 class="text-success">
                <i class="fas fa-calendar-plus me-2"></i>Client Booking Details
            </h6>
            <p>Booking ID: ${bookingId}</p>
            <p class="text-muted">This is a client booking. Use the Bookings management page to view full details.</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();
    hideLoadingState();
}

function showAddBookingModal() {
    // For now, redirect to the bookings page
    // In a real implementation, you'd show a modal form
    window.location.href = '{{ route("admin.bookings") }}';
}

function showAddAppointmentModal(selectedDate = null) {
    document.getElementById('addEditModalTitle').textContent = 'Add New Appointment';
    document.getElementById('appointmentForm').reset();
    
    if (selectedDate) {
        document.getElementById('appointment_date').value = selectedDate.split('T')[0];
    }
    
    const modal = new bootstrap.Modal(document.getElementById('addEditAppointmentModal'));
    modal.show();
}

function showEditAppointmentModal(appointmentId) {
    showLoadingState();
    
    fetch(`/admin/appointments/${appointmentId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('addEditModalTitle').textContent = 'Edit Appointment';
            document.getElementById('client_id').value = data.user_id;
            document.getElementById('appointment_date').value = data.appointment_date;
            document.getElementById('appointment_time').value = data.appointment_time;
            document.getElementById('status').value = data.status;
            document.getElementById('notes').value = data.notes || '';
            
            const modal = new bootstrap.Modal(document.getElementById('addEditAppointmentModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error loading appointment details');
        })
        .finally(() => {
            hideLoadingState();
        });
}

function saveAppointment() {
    if (isLoading) return;
    
    showLoadingState();
    const formData = new FormData(document.getElementById('appointmentForm'));
    
    fetch('/admin/appointments', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Appointment saved successfully');
            location.reload();
        } else {
            showError('Error saving appointment: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error saving appointment');
    })
    .finally(() => {
        hideLoadingState();
    });
}

function updateAppointmentDate(event) {
    if (isLoading) return;
    
    showLoadingState();
    const newDate = event.start.toISOString().split('T')[0];
    const newTime = event.start.toTimeString().split(' ')[0];
    
    fetch(`/admin/appointments/${event.id}/reschedule`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            appointment_date: newDate,
            appointment_time: newTime
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Appointment rescheduled successfully');
        } else {
            showError('Error updating appointment: ' + data.message);
            calendar.refetchEvents();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error updating appointment');
        calendar.refetchEvents();
    })
    .finally(() => {
        hideLoadingState();
    });
}

function cancelAppointment(appointmentId) {
    if (isLoading) return;
    
    if (confirm('Are you sure you want to cancel this appointment?')) {
        showLoadingState();
        
        fetch(`/admin/appointments/${appointmentId}/cancel`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Appointment cancelled successfully');
                location.reload();
            } else {
                showError('Error cancelling appointment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error cancelling appointment');
        })
        .finally(() => {
            hideLoadingState();
        });
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'confirmed': return 'success';
        case 'pending': return 'warning';
        case 'completed': return 'info';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function exportCalendar() {
    if (isLoading) return;
    
    showLoadingState();
    
    fetch('/admin/calendar/export')
        .then(response => {
            if (!response.ok) {
                throw new Error('Export failed');
            }
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'appointments.ics';
            a.click();
            window.URL.revokeObjectURL(url);
            showSuccess('Calendar exported successfully');
        })
        .catch(error => {
            console.error('Error exporting calendar:', error);
            showError('Error exporting calendar');
        })
        .finally(() => {
            hideLoadingState();
        });
}

function showSuccess(message) {
    // Create success toast
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-success border-0';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function showError(message) {
    // Create error toast
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-danger border-0';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}
</script>
@endpush
@endsection 