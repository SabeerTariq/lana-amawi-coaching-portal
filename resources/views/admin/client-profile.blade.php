@extends('layouts.admin')

@section('title', $client->name . ' - Client Profile')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Client Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clients') }}">Clients</a></li>
                    <li class="breadcrumb-item active">{{ $client->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.messages') }}?client_id={{ $client->id }}" 
               class="btn btn-primary">
                <i class="fas fa-comment me-2"></i>Send Message
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Client Info Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Client Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h5 class="card-title">{{ $client->name }}</h5>
                        <p class="text-muted">{{ $client->email }}</p>
                        @if($client->phone)
                            <p class="text-muted">
                                <i class="fas fa-phone me-2"></i>{{ $client->phone }}
                            </p>
                        @endif
                    </div>
                    <hr>
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h6 class="text-primary">{{ $appointments->count() }}</h6>
                            <small class="text-muted">Total Appointments</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-success">{{ $appointments->where('status', 'completed')->count() }}</h6>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h6 class="text-warning">{{ $appointments->where('status', 'pending')->count() }}</h6>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-info">{{ $messages->count() }}</h6>
                            <small class="text-muted">Messages</small>
                        </div>
                    </div>
                    <div class="text-center">
                        <h6 class="text-primary">{{ $appointments->where('status', 'completed')->count() * 1 }}</h6>
                        <small class="text-muted">Hours Coached</small>
                    </div>
                    <hr>
                    <div class="text-start">
                        <div class="mb-2">
                            <strong>Member since:</strong> {{ $client->created_at->format('M d, Y') }}
                        </div>
                        <div class="mb-2">
                            <strong>Last activity:</strong> {{ $client->updated_at->format('M d, Y') }}
                        </div>
                        <div class="mb-2">
                            <strong>Agreement Status:</strong> 
                            @if($client->hasSignedAgreement())
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Signed
                                </span>
                                @if($client->agreement_uploaded_at)
                                    <br><small class="text-muted">
                                        Uploaded: {{ $client->agreement_uploaded_at->format('M j, Y') }}
                                    </small>
                                @endif
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Not Uploaded
                                </span>
                            @endif
                        </div>
                        @if($client->hasSignedAgreement() && $client->agreement_url)
                        <div class="mb-2">
                            <a href="{{ $client->agreement_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>View Agreement
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Personal Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Full Name</label>
                            <p class="form-control-plaintext">{{ $client->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Email Address</label>
                            <p class="form-control-plaintext">{{ $client->email }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Phone Number</label>
                            <p class="form-control-plaintext">{{ $client->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Date of Birth</label>
                            <p class="form-control-plaintext">{{ $client->date_of_birth ? $client->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Age</label>
                            <p class="form-control-plaintext">{{ $client->age ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Gender</label>
                            <p class="form-control-plaintext">
                                @if($client->gender)
                                    {{ ucfirst(str_replace('_', ' ', $client->gender)) }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-muted">Address</label>
                            <p class="form-control-plaintext">{{ $client->address ?? 'Not provided' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-muted">Languages Spoken</label>
                            <p class="form-control-plaintext">
                                @if($client->languages_spoken && is_array($client->languages_spoken))
                                    {{ implode(', ', $client->languages_spoken) }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Professional Information -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-briefcase me-2"></i>Professional Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Institution/Hospital</label>
                            <p class="form-control-plaintext">{{ $client->institution_hospital ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Position</label>
                            <p class="form-control-plaintext">{{ $client->position ?? 'Not provided' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Position as of Date</label>
                            <p class="form-control-plaintext">{{ $client->position_as_of_date ? $client->position_as_of_date->format('M d, Y') : 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Specialty</label>
                            <p class="form-control-plaintext">{{ $client->specialty ?? 'Not provided' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Graduation Date</label>
                            <p class="form-control-plaintext">{{ $client->graduation_date ? $client->graduation_date->format('M d, Y') : 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Member Since</label>
                            <p class="form-control-plaintext">{{ $client->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Client Bookings & Notes -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>Booking Information & Client Notes
                    </h6>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Booking Date</th>
                                        <th>Preferred Date/Time</th>
                                        <th>Status</th>
                                        <th>Client Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->created_at->format('M d, Y g:i A') }}</td>
                                            <td>
                                                <strong>{{ $booking->preferred_date->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $booking->formatted_time }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $booking->getStatusBadgeColorAttribute() }}">
                                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($booking->message)
                                                    <div class="client-notes">
                                                        <div class="text-truncate" 
                                                             data-bs-toggle="tooltip" 
                                                             data-bs-placement="top" 
                                                             title="{{ $booking->message }}">
                                                            <i class="fas fa-sticky-note text-info me-2"></i>
                                                            {{ Str::limit($booking->message, 60) }}
                                                        </div>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-info mt-2"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#viewNotesModal{{ $booking->id }}">
                                                            <i class="fas fa-eye me-1"></i>View Full Notes
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-times-circle me-2"></i>
                                                        No notes provided
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No booking information found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Appointments -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Appointment History</h6>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                                            <td>{{ $appointment->formatted_time }}</td>
                                                                                         <td>
                                                 <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : ($appointment->status === 'completed' ? 'info' : 'danger')) }}">
                                                     {{ ucfirst($appointment->status) }}
                                                 </span>
                                             </td>
                                            <td>{{ $appointment->message ?? 'No notes' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No appointments found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Notes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-sticky-note me-2"></i>Admin Notes
            </h6>
            <button type="button" 
                    class="btn btn-primary btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#addNoteModal">
                <i class="fas fa-plus me-2"></i>Add Note
            </button>
        </div>
        <div class="card-body">
            @if($client->notes->count() > 0)
                <div class="notes-list">
                    @foreach($client->notes()->orderBy('created_at', 'desc')->get() as $note)
                        <div class="note-item border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="text-primary">{{ $note->admin->name }}</strong>
                                    <small class="text-muted ms-2">{{ $note->created_at->format('M d, Y g:i A') }}</small>
                                </div>
                                @if(Auth::id() === $note->admin_id || Auth::user()->is_admin)
                                    <form action="{{ route('admin.clients.notes.delete', $note) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this note?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="note-content">
                                {{ $note->note }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No admin notes yet for this client</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="card shadow">
        <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Messages</h6>
        </div>
        <div class="card-body">
            @if($messages->count() > 0)
                <div class="timeline">
                    @foreach($messages->take(10) as $message)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $message->sender_type === 'admin' ? 'bg-primary' : 'bg-secondary' }}" 
                                 ></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="timeline-title">{{ ucfirst($message->sender_type) }}</h6>
                                    <small class="text-muted">{{ $message->created_at->format('M d, Y g:i A') }}</small>
                                </div>
                                <p class="timeline-text">
                                    @if(!empty($message->message))
                                        {{ $message->message }}
                                    @elseif($message->hasAttachment())
                                        <em class="text-muted">File attached</em>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No messages found</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- View Client Notes Modals -->
@foreach($bookings as $booking)
    @if($booking->message)
        <div class="modal fade" id="viewNotesModal{{ $booking->id }}" tabindex="-1" aria-labelledby="viewNotesModalLabel{{ $booking->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewNotesModalLabel{{ $booking->id }}">
                            <i class="fas fa-sticky-note me-2"></i>Client Notes - {{ $client->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Booking Details</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Booking Date:</strong> {{ $booking->created_at->format('M d, Y g:i A') }}</li>
                                    <li><strong>Preferred Date:</strong> {{ $booking->preferred_date->format('M d, Y') }}</li>
                                    <li><strong>Preferred Time:</strong> {{ $booking->formatted_time }}</li>
                                    <li><strong>Status:</strong> 
                                        <span class="badge bg-{{ $booking->getStatusBadgeColorAttribute() }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Client Contact</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Email:</strong> {{ $booking->email }}</li>
                                    @if($booking->phone)
                                        <li><strong>Phone:</strong> {{ $booking->phone }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <h6 class="text-primary">Client Notes</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0">{{ $booking->message }}</p>
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
    @endif
@endforeach

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNoteModalLabel">
                    <i class="fas fa-sticky-note me-2"></i>Add Note for {{ $client->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.clients.notes.add', $client) }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="note" class="form-label">Note Content</label>
                        <textarea class="form-control" 
                                  id="note" 
                                  name="note" 
                                  rows="4" 
                                  placeholder="Enter your note about this client..." 
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Note
                    </button>
                </div>
            </form>
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
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid var(--bs-primary);
}

.timeline-title {
    margin-bottom: 5px;
}

.timeline-text {
    margin-bottom: 0;
}

/* Client Notes Styling */
.client-notes .text-truncate {
    cursor: help;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #17a2b8;
}

.client-notes .btn-outline-info {
    border-color: #17a2b8;
    color: #17a2b8;
}

.client-notes .btn-outline-info:hover {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: white;
}

/* Booking table styling */
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

/* Status badges */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips for client notes
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection 