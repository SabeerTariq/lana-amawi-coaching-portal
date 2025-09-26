@extends('layouts.admin')

@section('title', 'Enhanced Slot Management - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-clock me-2 text-primary"></i>Enhanced Slot Management
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-secondary" onclick="refreshAvailability()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <a href="{{ route('admin.slot-management.schedules') }}" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-2"></i>Manage Schedules
            </a>
            <a href="{{ route('admin.slot-management.exceptions') }}" class="btn btn-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Manage Exceptions
            </a>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $schedules->count() }}</h4>
                        <p class="card-text">Active Schedules</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $exceptions->count() }}</h4>
                        <p class="card-text">Upcoming Exceptions</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $schedules->where('booking_type', 'virtual')->count() }}</h4>
                        <p class="card-text">Virtual Schedules</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-video fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $schedules->where('booking_type', 'in-office')->count() }}</h4>
                        <p class="card-text">In-Office Schedules</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Current Schedule Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($scheduleDisplay as $day => $availability)
                    <div class="col-md-4 col-lg-3 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold text-primary">{{ $day }}</h6>
                            @if(is_array($availability))
                                @foreach($availability as $type => $time)
                                    <div class="mb-1">
                                        <span class="badge bg-{{ $type == 'Virtual' ? 'info' : 'success' }} me-2">
                                            {{ $type }}
                                        </span>
                                        <small class="text-muted">{{ $time }}</small>
                                    </div>
                                @endforeach
                            @else
                                <span class="badge bg-secondary">{{ $availability }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Exceptions -->
@if($exceptions->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Upcoming Exceptions (Next 30 Days)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Booking Type</th>
                                <th>Time Range</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exceptions as $exception)
                            <tr>
                                <td>{{ $exception->exception_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $exception->exception_type_badge_color }}">
                                        {{ $exception->exception_type_formatted }}
                                    </span>
                                </td>
                                <td>{{ $exception->booking_type_formatted }}</td>
                                <td>{{ $exception->time_range }}</td>
                                <td>{{ $exception->reason ?? 'No reason provided' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Next 7 Days Availability -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-week me-2"></i>Next 7 Days Availability
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Virtual Slots</th>
                                <th>In-Office Slots</th>
                                <th>Total Available</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($next7Days as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $day['day_name'] }}</span>
                                </td>
                                <td>
                                    @if(count($day['virtual_slots']) > 0)
                                        <span class="badge bg-info">{{ count($day['virtual_slots']) }} slots</span>
                                        <small class="text-muted d-block">
                                            {{ implode(', ', array_slice($day['virtual_slots'], 0, 3)) }}
                                            @if(count($day['virtual_slots']) > 3) ... @endif
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">No slots</span>
                                    @endif
                                </td>
                                <td>
                                    @if(count($day['in_office_slots']) > 0)
                                        <span class="badge bg-success">{{ count($day['in_office_slots']) }} slots</span>
                                        <small class="text-muted d-block">
                                            {{ implode(', ', array_slice($day['in_office_slots'], 0, 3)) }}
                                            @if(count($day['in_office_slots']) > 3) ... @endif
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">No slots</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ count($day['virtual_slots']) + count($day['in_office_slots']) }} total
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="addException('{{ $day['date'] }}')">
                                        <i class="fas fa-plus"></i> Add Exception
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Slot Checker Tool -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-search me-2"></i>Slot Availability Checker
                </h5>
            </div>
            <div class="card-body">
                <form id="slotCheckerForm">
                    <div class="mb-3">
                        <label for="check_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="check_date" name="date" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="check_time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="check_time" name="time" required>
                    </div>
                    <div class="mb-3">
                        <label for="check_type" class="form-label">Session Type</label>
                        <select class="form-select" id="check_type" name="booking_type" required>
                            <option value="">Select type</option>
                            <option value="virtual">Virtual</option>
                            <option value="in-office">In-Office</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Check Availability
                    </button>
                </form>
                <div id="checkResult" class="mt-3"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-range me-2"></i>Date Range Availability
                </h5>
            </div>
            <div class="card-body">
                <form id="rangeCheckerForm">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="range_type" class="form-label">Session Type (Optional)</label>
                        <select class="form-select" id="range_type" name="booking_type">
                            <option value="">All types</option>
                            <option value="virtual">Virtual only</option>
                            <option value="in-office">In-Office only</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Get Availability
                    </button>
                </form>
                <div id="rangeResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Exception Modal -->
<div class="modal fade" id="addExceptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Exception</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addExceptionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exception_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="exception_date" name="exception_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="exception_booking_type" class="form-label">Booking Type</label>
                        <select class="form-select" id="exception_booking_type" name="booking_type" required>
                            <option value="both">Both Types</option>
                            <option value="virtual">Virtual Only</option>
                            <option value="in-office">In-Office Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exception_type" class="form-label">Exception Type</label>
                        <select class="form-select" id="exception_type" name="exception_type" required>
                            <option value="blocked">Block Specific Time</option>
                            <option value="modified">Modified Hours</option>
                            <option value="closed">Close Entire Day</option>
                        </select>
                    </div>
                    <div class="mb-3" id="timeRangeFields">
                        <label for="exception_start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="exception_start_time" name="start_time">
                    </div>
                    <div class="mb-3" id="timeRangeFields2">
                        <label for="exception_end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" id="exception_end_time" name="end_time">
                    </div>
                    <div class="mb-3">
                        <label for="exception_reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="exception_reason" name="reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Exception</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('check_date').value = tomorrow.toISOString().split('T')[0];
    document.getElementById('start_date').value = tomorrow.toISOString().split('T')[0];
    
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    document.getElementById('end_date').value = nextWeek.toISOString().split('T')[0];

    // Handle exception type change
    document.getElementById('exception_type').addEventListener('change', function() {
        const timeFields = document.getElementById('timeRangeFields');
        const timeFields2 = document.getElementById('timeRangeFields2');
        
        if (this.value === 'closed') {
            timeFields.style.display = 'none';
            timeFields2.style.display = 'none';
        } else {
            timeFields.style.display = 'block';
            timeFields2.style.display = 'block';
        }
    });

    // Slot checker form
    document.getElementById('slotCheckerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById('checkResult');
        
        resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Checking...';
        
        fetch('/admin/slot-management/check-slot', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Available!</strong> The ${data.booking_type} slot at ${data.time} on ${data.date} is available.
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Not Available!</strong> The ${data.booking_type} slot at ${data.time} on ${data.date} is not available.
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error checking slot availability.
                </div>
            `;
        });
    });

    // Range checker form
    document.getElementById('rangeCheckerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const resultDiv = document.getElementById('rangeResult');
        
        resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Loading availability...';
        
        fetch('/admin/slot-management/availability', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Date</th><th>Day</th><th>Availability</th></tr></thead><tbody>';
            
            data.availability.forEach(day => {
                html += `<tr>
                    <td>${new Date(day.date).toLocaleDateString()}</td>
                    <td><span class="badge bg-primary">${day.day_name}</span></td>
                    <td>`;
                
                if (day.booking_type) {
                    html += `<span class="badge bg-info">${day.slot_count} ${day.booking_type} slots</span>`;
                } else {
                    const virtualCount = day.virtual_slots ? day.virtual_slots.length : 0;
                    const officeCount = day.in_office_slots ? day.in_office_slots.length : 0;
                    html += `<span class="badge bg-info">${virtualCount} virtual</span> `;
                    html += `<span class="badge bg-success">${officeCount} in-office</span>`;
                }
                
                html += '</td></tr>';
            });
            
            html += '</tbody></table></div>';
            resultDiv.innerHTML = html;
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading availability data.
                </div>
            `;
        });
    });

    // Add exception form
    document.getElementById('addExceptionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/admin/slot-management/exceptions', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error adding exception');
            }
        })
        .catch(error => {
            alert('Error adding exception');
        });
    });
});

function refreshAvailability() {
    location.reload();
}

function addException(date) {
    document.getElementById('exception_date').value = date;
    const modal = new bootstrap.Modal(document.getElementById('addExceptionModal'));
    modal.show();
}
</script>
@endpush
