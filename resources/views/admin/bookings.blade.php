@extends('layouts.admin')

@section('title', 'Manage Bookings - Admin Portal')

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
</style>

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Bookings</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
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
            <small class="text-muted">Manage all active bookings - confirm, suggest alternatives, or view details</small>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $booking->full_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $booking->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $booking->preferred_date->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $booking->preferred_time }}</small>
                                    </td>
                                    <td>
                                        @if($booking->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($booking->status === 'suggested_alternative')
                                            <span class="badge bg-info">Alternative Suggested</span>
                                            @if($booking->admin_suggestion)
                                                <br><small class="text-muted">{{ Str::limit($booking->admin_suggestion, 50) }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-envelope me-2"></i>{{ $booking->email }}
                                            @if($booking->phone)
                                                <br>
                                                <i class="fas fa-phone me-2"></i>{{ $booking->phone }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-success btn-sm" 
                                                    onclick="showPopup('convertPopup{{ $booking->id }}')">
                                                <i class="fas fa-check me-1"></i>Confirm
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm"
                                                    onclick="showPopup('suggestPopup{{ $booking->id }}')">
                                                <i class="fas fa-clock me-1"></i>Suggest Time
                                            </button>
                                            @php
                                                $client = User::where('email', $booking->email)->first();
                                            @endphp
                                            @if($client)
                                                <a href="{{ route('admin.clients.profile', $client) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-user me-1"></i>View Client
                                                </a>
                                            @else
                                                <span class="text-muted">No client account</span>
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
                                                    <strong>Time:</strong> {{ $booking->preferred_time }}
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No active bookings</h5>
                    <p class="text-muted">All bookings have been processed or converted to appointments.</p>
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
    const popups = document.querySelectorAll('.popup-overlay');
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

// Handle clicking outside popup to close
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('popup-overlay')) {
        hidePopup(e.target.id);
    }
});

// Handle ESC key to close popup
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const activePopup = document.querySelector('.popup-overlay.active');
        if (activePopup) {
            hidePopup(activePopup.id);
        }
    }
});

// Prevent popup content clicks from closing the popup
document.addEventListener('click', function(e) {
    if (e.target.closest('.popup-container')) {
        e.stopPropagation();
    }
});
</script>
@endsection 