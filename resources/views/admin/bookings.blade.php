@extends('layouts.admin')

@section('title', 'Manage Bookings - Admin')

@php
use App\Models\User;
@endphp

<style>
/* Full Screen Modal Solution */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
}

.popup-overlay.active {
    display: flex !important;
}

.popup-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
    max-width: 500px;
    width: 90%;
    max-height: 85vh;
    overflow: hidden;
    position: relative;
    animation: popupSlideIn 0.3s ease-out;
    display: flex;
    flex-direction: column;
    margin: 20px;
}

@keyframes popupSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-30px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.popup-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
}

.popup-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.popup-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #666;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.popup-close:hover {
    background: #e9ecef;
    color: #333;
}

.popup-body {
    padding: 20px;
    overflow-y: auto;
    max-height: 60vh;
    flex: 1;
    min-height: 0;
    scrollbar-width: thin;
    scrollbar-color: #730623 #f8f9fa;
}

/* Notes and Agreement Column Styling */
.notes-cell {
    max-width: 200px;
    word-wrap: break-word;
}

.notes-cell .text-truncate {
    cursor: help;
}

.agreement-cell a {
    color: #0d6efd;
    transition: all 0.2s ease;
}

.agreement-cell a:hover {
    color: #0a58ca;
    text-decoration: underline !important;
}

.agreement-cell .text-muted {
    font-style: italic;
}

/* Table column widths */
.table th:nth-child(1), /* Client column */
.table td:nth-child(1) {
    width: 15%;
    min-width: 120px;
}

.table th:nth-child(2), /* Date/Time column */
.table td:nth-child(2) {
    width: 14%;
    min-width: 100px;
}

.table th:nth-child(3), /* Status column */
.table td:nth-child(3) {
    width: 12%;
    min-width: 90px;
}

.table th:nth-child(4), /* Contact Info column */
.table td:nth-child(4) {
    width: 19%;
    min-width: 120px;
}

.table th:nth-child(5), /* Notes column */
.table td:nth-child(5) {
    width: 14%;
    min-width: 110px;
}

.table th:nth-child(6), /* Agreement column */
.table td:nth-child(6) {
    width: 12%;
    min-width: 90px;
}

.table th:nth-child(7), /* Actions column */
.table td:nth-child(7) {
    width: 14%;
    min-width: 140px;
}

/* Responsive Table Styling */
@media (max-width: 1400px) {
    .table th:nth-child(5),
    .table td:nth-child(5) {
        width: 13%;
        min-width: 100px;
    }
    
    .table th:nth-child(6),
    .table td:nth-child(6) {
        width: 12%;
        min-width: 90px;
    }
    
    .table th:nth-child(7),
    .table td:nth-child(7) {
        width: 13%;
        min-width: 130px;
    }
}

@media (max-width: 1200px) {
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 16%;
        min-width: 110px;
    }
    
    .table th:nth-child(2),
    .table td:nth-child(2) {
        width: 14%;
        min-width: 90px;
    }
    
    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 12%;
        min-width: 90px;
    }
    
    .table th:nth-child(4),
    .table td:nth-child(4) {
        width: 20%;
        min-width: 110px;
    }
    
    .table th:nth-child(5),
    .table td:nth-child(5) {
        width: 15%;
        min-width: 100px;
    }
    
    .table th:nth-child(6),
    .table td:nth-child(6) {
        width: 12%;
        min-width: 80px;
    }
    
    .table th:nth-child(7),
    .table td:nth-child(7) {
        width: 11%;
        min-width: 100px;
    }
}

@media (max-width: 992px) {
    .table th:nth-child(1),
    .table td:nth-child(1) {
        width: 17%;
        min-width: 100px;
    }
    
    .table th:nth-child(2),
    .table td:nth-child(2) {
        width: 15%;
        min-width: 80px;
    }
    
    .table th:nth-child(3),
    .table td:nth-child(3) {
        width: 12%;
        min-width: 80px;
    }
    
    .table th:nth-child(4),
    .table td:nth-child(4) {
        width: 21%;
        min-width: 100px;
    }
    
    .table th:nth-child(5),
    .table td:nth-child(5) {
        width: 15%;
        min-width: 90px;
    }
    
    .table th:nth-child(6),
    .table td:nth-child(6) {
        width: 9%;
        min-width: 70px;
    }
    
    .table th:nth-child(7),
    .table td:nth-child(7) {
        width: 11%;
        min-width: 90px;
    }
}

/* Ensure table fits screen width */
.table-responsive {
    overflow-x: hidden;
}

.table {
    width: 100%;
    table-layout: fixed;
    margin-bottom: 0;
}

.table th,
.table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 0.5rem;
    vertical-align: middle;
}

/* Compact text for better fit */
.table th {
    font-size: 0.875rem;
    font-weight: 600;
}

.table td {
    font-size: 0.875rem;
}

/* Compact badges and buttons */
.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Compact icons */
.fas {
    font-size: 0.875rem;
}

/* Ensure content fits in cells */
.notes-cell .text-truncate {
    max-width: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.agreement-cell a {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

/* Action buttons styling */
.btn-group {
    gap: 0.5rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.btn-group .btn {
    min-width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-group .btn:active {
    transform: translateY(0);
}

/* Ensure action column has enough space */
.table th:nth-child(7),
.table td:nth-child(7) {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    text-align: center;
}

/* Button colors and states */
.btn-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-info {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

@media (max-width: 768px) {
    /* Stack table on mobile */
    .table-responsive {
        border: 0;
    }
    
    .table {
        border: 0;
    }
    
    .table thead {
        display: none;
    }
    
    .table tbody,
    .table tr,
    .table td {
        display: block;
        width: 100%;
    }
    
    .table tr {
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .table td {
        border: none;
        padding: 0.5rem 0;
        text-align: left;
        position: relative;
        padding-left: 50%;
    }
    
    .table td:before {
        content: attr(data-label);
        position: absolute;
        left: 0;
        width: 45%;
        padding-right: 10px;
        font-weight: bold;
        color: #495057;
        font-size: 0.875rem;
    }
    
    /* Mobile-specific styling */
    .notes-cell,
    .agreement-cell {
        max-width: none;
        width: 100%;
    }
    
    .notes-cell .text-truncate {
        max-width: none;
        white-space: normal;
        word-wrap: break-word;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-group .btn {
        width: 100%;
        margin: 0;
    }
}

@media (max-width: 576px) {
    .table td {
        padding-left: 0;
        padding-top: 2rem;
    }
    
    .table td:before {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0.5rem;
    }
    
    .card-header {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

.popup-body::-webkit-scrollbar {
    width: 6px;
}

.popup-body::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 3px;
}

.popup-body::-webkit-scrollbar-thumb {
    background: #730623;
    border-radius: 3px;
}

.popup-body::-webkit-scrollbar-thumb:hover {
    background: #8a0a2a;
}

.popup-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f8f9fa;
}

/* Prevent body scroll when popup is open */
body.popup-open {
    overflow: hidden !important;
    position: fixed !important;
    width: 100% !important;
    height: 100% !important;
}

/* Ensure popup is above all other elements */
.popup-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    z-index: 99999 !important;
    width: 100vw !important;
    height: 100vh !important;
}

/* Override any parent container constraints */
.popup-overlay * {
    box-sizing: border-box;
}

/* Responsive design */
@media (max-width: 768px) {
    .popup-container {
        width: 95%;
        margin: 15px;
        max-height: 90vh;
    }
    
    .popup-header,
    .popup-body,
    .popup-footer {
        padding: 15px;
    }
    
    .popup-body {
        max-height: 65vh;
    }
}

@media (max-width: 480px) {
    .popup-container {
        width: 98%;
        margin: 10px;
        max-height: 95vh;
    }
    
    .popup-title {
        font-size: 16px;
    }
    
    .popup-body {
        max-height: 75vh;
    }
}

@media (max-width: 576px) {
    .popup-container {
        width: 95%;
        margin: 10px;
        max-height: 90vh;
    }
    
    .popup-header {
        padding: 15px;
    }
    
    .popup-body {
        padding: 15px;
    }
    
    .popup-footer {
        padding: 15px;
    }
}
</style>

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Bookings</h1>
        <!-- <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a> -->
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Active Bookings</h6>
            <small class="text-muted d-none d-md-block">Manage all active bookings - confirm, suggest alternatives, or view details</small>
            <div class="alert alert-info mt-2 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Bookings can only be converted to appointments after the client has uploaded their signed agreement (stored in their user profile).
            </div>
        </div>
        <div class="card-body">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Preferred Date/Time</th>
                                <th>Status</th>
                                <th>Contact Info</th>
                                <th>Notes</th>
                                <th>Agreement</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td data-label="Client">
                                        <div>
                                            <strong>{{ Str::limit($booking->full_name, 15) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($booking->email, 20) }}</small>
                                        </div>
                                    </td>
                                    <td data-label="Preferred Date/Time">
                                        <strong>{{ $booking->preferred_date->format('M d') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $booking->formatted_time }}</small>
                                    </td>
                                    <td data-label="Status">
                                        @if($booking->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($booking->status === 'suggested_alternative')
                                            <span class="badge bg-info">Alt. Time</span>
                                            @if($booking->admin_suggestion)
                                                <br><small class="text-muted">{{ Str::limit($booking->admin_suggestion, 30) }}</small>
                                            @endif
                                        @elseif($booking->status === 'accepted')
                                            <span class="badge bg-success">Accepted</span>
                                            @if($booking->client_response)
                                                <br><small class="text-muted">{{ Str::limit($booking->client_response, 30) }}</small>
                                            @endif
                                        @elseif($booking->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                            @if($booking->client_response)
                                                <br><small class="text-muted">{{ Str::limit($booking->client_response, 30) }}</small>
                                            @endif
                                        @elseif($booking->status === 'modified')
                                            <span class="badge bg-primary">Modified</span>
                                            @if($booking->client_response)
                                                <br><small class="text-muted">{{ Str::limit($booking->client_response, 30) }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                                        @endif
                                    </td>
                                    <td data-label="Contact Info">
                                        <div>
                                            <i class="fas fa-envelope me-1"></i>{{ Str::limit($booking->email, 18) }}
                                            @if($booking->phone)
                                                <br>
                                                <i class="fas fa-phone me-1"></i>{{ Str::limit($booking->phone, 12) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="notes-cell" data-label="Notes">
                                        @if($booking->message)
                                            <div class="text-truncate" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="{{ $booking->message }}">
                                                <i class="fas fa-sticky-note text-info me-1"></i>
                                                {{ Str::limit($booking->message, 25) }}
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-times-circle me-1"></i>
                                                <span class="d-none d-lg-inline">No notes</span>
                                                <span class="d-lg-none">None</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="agreement-cell" data-label="Agreement">
                                        @php
                                            $user = \App\Models\User::where('email', $booking->email)->first();
                                            $hasAgreement = false;
                                            $agreementUrl = null;
                                            
                                            // Check for agreement in user_programs table first
                                            if($user) {
                                                $userProgram = \App\Models\UserProgram::where('user_id', $user->id)
                                                    ->whereNotNull('signed_agreement_path')
                                                    ->first();
                                                
                                                if($userProgram) {
                                                    $hasAgreement = true;
                                                    $agreementUrl = \Illuminate\Support\Facades\Storage::url($userProgram->signed_agreement_path);
                                                } else {
                                                    // Fallback to user table agreement
                                                    if($user->hasSignedAgreement()) {
                                                        $hasAgreement = true;
                                                        $agreementUrl = $user->agreement_url;
                                                    }
                                                }
                                            }
                                            
                                            // Also check if booking has its own agreement
                                            if(!$hasAgreement && $booking->hasSignedAgreement()) {
                                                $hasAgreement = true;
                                                $agreementUrl = $booking->agreement_url;
                                            }
                                        @endphp
                                        @if($hasAgreement)
                                            <a href="{{ $agreementUrl }}" 
                                               target="_blank" 
                                               class="text-decoration-none"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Click to view agreement (opens in new tab)">
                                                <i class="fas fa-file-pdf text-success me-1"></i>
                                                <strong>View</strong>
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-times-circle me-1"></i>
                                                None
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Actions">
                                        <div class="btn-group d-flex flex-column flex-md-row" role="group">
                                            @if($booking->status === 'pending')
                                                <button type="button" class="btn btn-success btn-sm mb-1 mb-md-0" 
                                                        onclick="showPopup('convertPopup{{ $booking->id }}')"
                                                        title="Confirm booking">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-info btn-sm mb-1 mb-md-0"
                                                        onclick="showPopup('suggestPopup{{ $booking->id }}')"
                                                        title="Suggest alternative time">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                            @elseif($booking->status === 'suggested_alternative')
                                                <button type="button" class="btn btn-warning btn-sm mb-1 mb-md-0" disabled
                                                        title="Waiting for client response">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                            @elseif($booking->status === 'accepted')
                                                <button type="button" class="btn btn-success btn-sm mb-1 mb-md-0" 
                                                        onclick="showPopup('convertAcceptedPopup{{ $booking->id }}')"
                                                        title="Convert to appointment">
                                                    <i class="fas fa-calendar-check"></i>
                                                </button>
                                            @elseif($booking->status === 'rejected')
                                                <button type="button" class="btn btn-info btn-sm mb-1 mb-md-0"
                                                        onclick="showPopup('handleRejectionPopup{{ $booking->id }}')"
                                                        title="Suggest new time">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm mb-1 mb-md-0"
                                                        onclick="showPopup('cancelRejectedPopup{{ $booking->id }}')"
                                                        title="Cancel booking">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @elseif($booking->status === 'modified')
                                                <button type="button" class="btn btn-success btn-sm mb-1 mb-md-0"
                                                        onclick="showPopup('acceptModificationPopup{{ $booking->id }}')"
                                                        title="Accept changes">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-info btn-sm mb-1 mb-md-0"
                                                        onclick="showPopup('suggestAlternativeToModificationPopup{{ $booking->id }}')"
                                                        title="Suggest alternative">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                            @endif
                                            
                                            @php
                                                $client = User::where('email', $booking->email)->first();
                                            @endphp
                                            @if($client)
                                                <a href="{{ route('admin.clients.profile', $client) }}" 
                                                   class="btn btn-outline-primary btn-sm mb-1 mb-md-0"
                                                   title="View client profile">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                            @else
                                                <span class="text-muted small">No client</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Convert to Appointment Popup -->
                                <div class="popup-overlay" id="convertPopup{{ $booking->id }}">
                                    <div class="popup-container">
                                        <div class="popup-header">
                                            <h5 class="popup-title">Confirm & Move to Appointments</h5>
                                            <button type="button" class="popup-close" onclick="hidePopup('convertPopup{{ $booking->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.bookings.convert', $booking) }}" method="POST">
                                            @csrf
                                            <div class="popup-body">
                                                <p>Are you sure you want to confirm this booking and move it to appointments?</p>
                                                <div class="alert alert-info">
                                                    <strong>Client:</strong> {{ $booking->full_name }}<br>
                                                    <strong>Date:</strong> {{ $booking->preferred_date->format('M d, Y') }}<br>
                                                    <strong>Time:</strong> {{ $booking->formatted_time }}
                                                </div>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Note:</strong> This booking will be moved to the appointments section where you can manage it (complete/cancel).
                                                </div>
                                            </div>
                                            <div class="popup-footer">
                                                <button type="button" class="btn btn-secondary" onclick="hidePopup('convertPopup{{ $booking->id }}')">Cancel</button>
                                                <button type="submit" class="btn btn-success">Confirm & Move to Appointments</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Suggest Alternative Time Popup -->
                                <div class="popup-overlay" id="suggestPopup{{ $booking->id }}">
                                    <div class="popup-container">
                                        <div class="popup-header">
                                            <h5 class="popup-title">Suggest Alternative Time</h5>
                                            <button type="button" class="popup-close" onclick="hidePopup('suggestPopup{{ $booking->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.bookings.suggest-time', $booking) }}" method="POST">
                                            @csrf
                                            <div class="popup-body">
                                                <div class="mb-3">
                                                    <label for="suggested_date{{ $booking->id }}" class="form-label">Suggested Date</label>
                                                    <input type="date" class="form-control" id="suggested_date{{ $booking->id }}" 
                                                           name="suggested_date" required min="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="suggested_time{{ $booking->id }}" class="form-label">Suggested Time</label>
                                                    <select class="form-control" id="suggested_time{{ $booking->id }}" name="suggested_time" required>
                                                        <option value="">Select time</option>
                                                        <option value="09:00">9:00 AM</option>
                                                        <option value="10:00">10:00 AM</option>
                                                        <option value="11:00">11:00 AM</option>
                                                        <option value="12:00">12:00 PM</option>
                                                        <option value="13:00">1:00 PM</option>
                                                        <option value="14:00">2:00 PM</option>
                                                        <option value="15:00">3:00 PM</option>
                                                        <option value="16:00">4:00 PM</option>
                                                        <option value="17:00">5:00 PM</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="message{{ $booking->id }}" class="form-label">Message to Client (Optional)</label>
                                                    <textarea class="form-control" id="message{{ $booking->id }}" name="message" rows="3" 
                                                              placeholder="Explain why you're suggesting this alternative time..."></textarea>
                                                </div>
                                            </div>
                                            <div class="popup-footer">
                                                <button type="button" class="btn btn-secondary" onclick="hidePopup('suggestPopup{{ $booking->id }}')">Cancel</button>
                                                <button type="submit" class="btn btn-info">Send Suggestion</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Convert Accepted Booking to Appointment Popup -->
                                <div class="popup-overlay" id="convertAcceptedPopup{{ $booking->id }}">
                                    <div class="popup-container">
                                        <div class="popup-header">
                                            <h5 class="popup-title">Convert Accepted Booking to Appointment</h5>
                                            <button type="button" class="popup-close" onclick="hidePopup('convertAcceptedPopup{{ $booking->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.bookings.convert-accepted', $booking) }}" method="POST">
                                            @csrf
                                            <div class="popup-body">
                                                <p>This booking has been accepted by the client. Convert it to a confirmed appointment?</p>
                                                <div class="alert alert-success">
                                                    <strong>Client:</strong> {{ $booking->full_name }}<br>
                                                    <strong>Date:</strong> {{ $booking->preferred_date->format('M d, Y') }}<br>
                                                    <strong>Time:</strong> {{ $booking->formatted_time }}<br>
                                                    <strong>Status:</strong> Accepted by Client
                                                </div>
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Note:</strong> This will create a confirmed appointment and move it to the appointments section.
                                                </div>
                                            </div>
                                            <div class="popup-footer">
                                                <button type="button" class="btn btn-secondary" onclick="hidePopup('convertAcceptedPopup{{ $booking->id }}')">Cancel</button>
                                                <button type="submit" class="btn btn-success">Convert to Appointment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Handle Rejection Popup -->
                                <div class="popup-overlay" id="handleRejectionPopup{{ $booking->id }}">
                                    <div class="popup-container">
                                        <div class="popup-header">
                                            <h5 class="popup-title">Handle Client Rejection</h5>
                                            <button type="button" class="popup-close" onclick="hidePopup('handleRejectionPopup{{ $booking->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.bookings.handle-rejection', $booking) }}" method="POST">
                                            @csrf
                                            <div class="popup-body">
                                                <p>The client rejected the suggested time. What would you like to do?</p>
                                                <div class="alert alert-danger">
                                                    <strong>Client:</strong> {{ $booking->full_name }}<br>
                                                    <strong>Rejection Reason:</strong> {{ $booking->client_response }}
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Action:</label>
                                                    <select class="form-control" name="action" id="rejectionAction{{ $booking->id }}" required>
                                                        <option value="">Select action</option>
                                                        <option value="suggest_new_time">Suggest New Alternative Time</option>
                                                        <option value="cancel">Cancel the Booking</option>
                                                    </select>
                                                </div>
                                                <div id="newTimeFields{{ $booking->id }}" style="display: none;">
                                                    <div class="mb-3">
                                                        <label for="new_suggested_date{{ $booking->id }}" class="form-label">New Suggested Date</label>
                                                        <input type="date" class="form-control" id="new_suggested_date{{ $booking->id }}" 
                                                               name="new_suggested_date" min="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="new_suggested_time{{ $booking->id }}" class="form-label">New Suggested Time</label>
                                                        <select class="form-control" id="new_suggested_time{{ $booking->id }}" name="new_suggested_time">
                                                            <option value="">Select time</option>
                                                            <option value="09:00">9:00 AM</option>
                                                            <option value="10:00">10:00 AM</option>
                                                            <option value="11:00">11:00 AM</option>
                                                            <option value="12:00">12:00 PM</option>
                                                            <option value="13:00">1:00 PM</option>
                                                            <option value="14:00">2:00 PM</option>
                                                            <option value="15:00">3:00 PM</option>
                                                            <option value="16:00">4:00 PM</option>
                                                            <option value="17:00">5:00 PM</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="admin_message{{ $booking->id }}" class="form-label">Message to Client</label>
                                                        <textarea class="form-control" id="admin_message{{ $booking->id }}" name="admin_message" rows="3" 
                                                                  placeholder="Explain the new suggested time..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="popup-footer">
                                                <button type="button" class="btn btn-secondary" onclick="hidePopup('handleRejectionPopup{{ $booking->id }}')">Cancel</button>
                                                <button type="submit" class="btn btn-info">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Accept Modification Popup -->
                                <div class="popup-overlay" id="acceptModificationPopup{{ $booking->id }}">
                                    <div class="popup-container">
                                        <div class="popup-header">
                                            <h5 class="popup-title">Accept Client Modification</h5>
                                            <button type="button" class="popup-close" onclick="hidePopup('acceptModificationPopup{{ $booking->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.bookings.handle-modification', $booking) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="accept_modification">
                                            <div class="popup-body">
                                                <p>The client has requested modifications to the suggested time. Accept their changes?</p>
                                                <div class="alert alert-primary">
                                                    <strong>Client:</strong> {{ $booking->full_name }}<br>
                                                    <strong>New Preference:</strong> {{ $booking->preferred_date->format('M d, Y') }} at {{ $booking->formatted_time }}<br>
                                                    <strong>Reason:</strong> {{ $booking->client_response }}
                                                </div>
                                                <div class="alert alert-success">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Note:</strong> This will mark the booking as accepted and ready for conversion to appointment.
                                                </div>
                                            </div>
                                            <div class="popup-footer">
                                                <button type="button" class="btn btn-secondary" onclick="hidePopup('acceptModificationPopup{{ $booking->id }}')">Cancel</button>
                                                <button type="submit" class="btn btn-success">Accept Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Suggest Alternative to Modification Popup -->
                                <div class="popup-overlay" id="suggestAlternativeToModificationPopup{{ $booking->id }}">
                                    <div class="popup-container">
                                        <div class="popup-header">
                                            <h5 class="popup-title">Suggest Alternative to Client Modification</h5>
                                            <button type="button" class="popup-close" onclick="hidePopup('suggestAlternativeToModificationPopup{{ $booking->id }}')">&times;</button>
                                        </div>
                                        <form action="{{ route('admin.bookings.handle-modification', $booking) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="suggest_alternative">
                                            <div class="popup-body">
                                                <p>The client requested modifications, but you'd like to suggest an alternative time instead.</p>
                                                <div class="alert alert-primary">
                                                    <strong>Client's Request:</strong> {{ $booking->preferred_date->format('M d, Y') }} at {{ $booking->formatted_time }}<br>
                                                    <strong>Reason:</strong> {{ $booking->client_response }}
                                                </div>
                                                <div class="mb-3">
                                                    <label for="suggested_date_mod{{ $booking->id }}" class="form-label">Alternative Date</label>
                                                    <input type="date" class="form-control" id="suggested_date_mod{{ $booking->id }}" 
                                                           name="suggested_date" required min="{{ date('Y-m-d') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="suggested_time_mod{{ $booking->id }}" class="form-label">Alternative Time</label>
                                                    <select class="form-control" id="suggested_time_mod{{ $booking->id }}" name="suggested_time" required>
                                                        <option value="">Select time</option>
                                                        <option value="09:00">9:00 AM</option>
                                                        <option value="10:00">10:00 AM</option>
                                                        <option value="11:00">11:00 AM</option>
                                                        <option value="12:00">12:00 PM</option>
                                                        <option value="13:00">1:00 PM</option>
                                                        <option value="14:00">2:00 PM</option>
                                                        <option value="15:00">3:00 PM</option>
                                                        <option value="16:00">4:00 PM</option>
                                                        <option value="17:00">5:00 PM</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="admin_message_mod{{ $booking->id }}" class="form-label">Message to Client</label>
                                                    <textarea class="form-control" id="admin_message_mod{{ $booking->id }}" name="admin_message" rows="3" 
                                                              placeholder="Explain why you're suggesting this alternative time..."></textarea>
                                                </div>
                                            </div>
                                            <div class="popup-footer">
                                                <button type="button" class="btn btn-secondary" onclick="hidePopup('suggestAlternativeToModificationPopup{{ $booking->id }}')">Cancel</button>
                                                <button type="submit" class="btn btn-info">Suggest Alternative</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p class="h5">No bookings found</p>
                        <p>There are currently no active bookings in the system.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Simple and Reliable Popup Functions
function showPopup(popupId) {
    const popup = document.getElementById(popupId);
    if (popup) {
        // Hide any other open popups
        hideAllPopups();
        
        // Move popup to body if it's not already there
        if (popup.parentElement !== document.body) {
            document.body.appendChild(popup);
        }
        
        // Show the popup
        popup.classList.add('active');
        
        // Prevent body scroll
        document.body.classList.add('popup-open');
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        document.body.style.height = '100%';
        
        // Focus on first input if exists
        const firstInput = popup.querySelector('input, select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
        
        // Ensure popup body is scrollable
        const popupBody = popup.querySelector('.popup-body');
        if (popupBody) {
            popupBody.scrollTop = 0;
        }
    }
}

function hidePopup(popupId) {
    const popup = document.getElementById(popupId);
    if (popup) {
        popup.classList.remove('active');
        
        // Restore body scroll
        document.body.classList.remove('popup-open');
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.height = '';
    }
}

function hideAllPopups() {
    const popups = document.querySelectorAll('.popup-overlay.active');
    popups.forEach(popup => {
        popup.classList.remove('active');
    });
    
    // Restore body scroll
    document.body.classList.remove('popup-open');
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.width = '';
    document.body.style.height = '';
}

// Handle rejection action change
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add event listeners for rejection action changes
    const rejectionActions = document.querySelectorAll('[id^="rejectionAction"]');
    rejectionActions.forEach(action => {
        action.addEventListener('change', function() {
            const bookingId = this.id.replace('rejectionAction', '');
            const newTimeFields = document.getElementById('newTimeFields' + bookingId);
            
            if (this.value === 'suggest_new_time') {
                newTimeFields.style.display = 'block';
                // Make required fields actually required
                newTimeFields.querySelectorAll('input, select').forEach(field => {
                    field.required = true;
                });
            } else {
                newTimeFields.style.display = 'none';
                // Remove required from hidden fields
                newTimeFields.querySelectorAll('input, select').forEach(field => {
                    field.required = false;
                });
            }
        });
    });

    // Close popups when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('popup-overlay')) {
            hidePopup(e.target.id);
        }
    });

    // Close popups with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAllPopups();
        }
    });
});
</script>
@endsection 