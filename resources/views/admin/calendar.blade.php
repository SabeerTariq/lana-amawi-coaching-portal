@extends('layouts.admin')

@section('title', 'Calendar - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Appointment Calendar</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="addAppointmentBtn">
                <i class="fas fa-plus me-2"></i>Add Appointment
            </button>
            <button class="btn btn-outline-secondary" onclick="exportCalendar()">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Calendar -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Calendar View</h6>
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
                    <h6 class="m-0 font-weight-bold text-primary">Today's Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $todayAppointments ?? 0 }}</h4>
                            <small class="text-muted">Today's Sessions</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $pendingAppointments ?? 0 }}</h4>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Appointments</h6>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($appointments->take(8) as $appointment)
                                <div class="list-group-item appointment-item" data-appointment-id="{{ $appointment->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $appointment->user->name }}</h6>
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
                        </div>
                        @if($appointments->count() > 8)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.appointments') }}" class="btn btn-outline-primary btn-sm">
                                    View All Appointments
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No upcoming appointments</p>
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
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editAppointmentBtn">Edit</button>
                <button type="button" class="btn btn-danger" id="cancelAppointmentBtn">Cancel Appointment</button>
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
#calendar {
    min-height: 400px;
    width: 100%;
    height: 500px;
}

.fc {
    height: 100%;
}
.fc-event {
    cursor: pointer;
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 0.85em;
}

.fc-event:hover {
    opacity: 0.8;
}

.appointment-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.appointment-item:hover {
    background-color: #f8f9fa;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .fc-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
    
    .fc-header-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc-toolbar-title {
        font-size: 1.2em;
        text-align: center;
    }
    
    .fc-button {
        font-size: 0.9em;
        padding: 0.375rem 0.75rem;
    }
    
    .fc-daygrid-day {
        min-height: 60px;
    }
    
    .fc-event {
        font-size: 0.75em;
        padding: 1px 2px;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
}

/* Tablet Optimizations */
@media (min-width: 769px) and (max-width: 1024px) {
    .fc-toolbar {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .fc-toolbar-chunk {
        flex: 1;
        justify-content: center;
    }
}

/* Desktop Enhancements */
@media (min-width: 1025px) {
    .fc-event {
        font-size: 0.9em;
        padding: 3px 6px;
    }
    
    .fc-daygrid-day {
        min-height: 80px;
    }
}

/* Calendar Event Colors */
.fc-event.bg-confirmed {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.fc-event.bg-pending {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000 !important;
}

.fc-event.bg-completed {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
}

.fc-event.bg-cancelled {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

/* Loading States */
.calendar-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

.calendar-loading .spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Print Styles */
@media print {
    .btn, .modal, .sidebar {
        display: none !important;
    }
    
    .fc {
        font-size: 12px;
    }
    
    .fc-event {
        font-size: 10px;
    }
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
        initializeCalendar();
    }, 1000);

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
        selectable: true,
        events: [
            // Test event
            {
                id: 'test',
                title: 'Test Appointment',
                start: new Date().toISOString().split('T')[0] + 'T10:00:00',
                        backgroundColor: '#730623',
        borderColor: '#730623',
                textColor: '#fff'
            },
            @foreach($appointments as $appointment)
            {
                id: '{{ $appointment->id }}',
                title: '{{ $appointment->user->name }}',
                start: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ $appointment->appointment_time }}:00',
                end: '{{ $appointment->appointment_date->format("Y-m-d") }}T{{ $appointment->appointment_time }}:00',
                backgroundColor: '{{ $appointment->status === "confirmed" ? "#032a57" : ($appointment->status === "pending" ? "#730623" : ($appointment->status === "completed" ? "#032a57" : "#730623")) }}',
        borderColor: '{{ $appointment->status === "confirmed" ? "#032a57" : ($appointment->status === "pending" ? "#730623" : ($appointment->status === "completed" ? "#032a57" : "#730623")) }}',
                textColor: '{{ $appointment->status === "pending" ? "#000" : "#fff" }}',
                className: 'bg-{{ $appointment->status }}',
                extendedProps: {
                    status: '{{ $appointment->status }}',
                    notes: '{{ $appointment->message ?? "" }}',
                    clientEmail: '{{ $appointment->user->email }}'
                }
            },
            @endforeach
        ],
        eventClick: function(info) {
            if (!isLoading) {
                showAppointmentDetails(info.event);
            }
        },
        eventDrop: function(info) {
            if (!isLoading) {
                updateAppointmentDate(info.event);
            }
        },
        dayMaxEvents: true,
        height: 'auto',
        editable: true,
        selectable: true,
        select: function(info) {
            if (!isLoading) {
                showAddAppointmentModal(info.startStr);
            }
        },
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
    currentAppointmentId = event.id;
    
    const modalBody = document.getElementById('appointmentModalBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Client Information</h6>
                <p><strong>Name:</strong> ${event.title}</p>
                <p><strong>Email:</strong> ${event.extendedProps.clientEmail}</p>
                <p><strong>Date:</strong> ${event.start.toLocaleDateString()}</p>
                <p><strong>Time:</strong> ${event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
            </div>
            <div class="col-md-6">
                <h6>Appointment Details</h6>
                <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(event.extendedProps.status)}">${event.extendedProps.status}</span></p>
                <p><strong>Notes:</strong> ${event.extendedProps.notes || 'No notes'}</p>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();
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