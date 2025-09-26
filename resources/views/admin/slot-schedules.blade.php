@extends('layouts.admin')

@section('title', 'Schedule Management - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar-plus me-2 text-primary"></i>Schedule Management
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.enhanced-slot-management') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                <i class="fas fa-plus me-2"></i>Add Schedule
            </button>
        </div>
    </div>
</div>

<!-- Schedules Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Current Schedules
                </h5>
            </div>
            <div class="card-body">
                @if($schedules->count() > 0)
                <form id="bulkActionForm" method="POST" action="{{ route('admin.slot-management.schedules.bulk') }}">
                    @csrf
                    <div class="d-flex justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <select name="action" class="form-select form-select-sm me-2" style="width: auto;">
                                <option value="">Bulk Actions</option>
                                <option value="activate">Activate</option>
                                <option value="deactivate">Deactivate</option>
                                <option value="delete">Delete</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Apply</button>
                        </div>
                        <div>
                            <input type="checkbox" id="selectAll" class="form-check-input me-2">
                            <label for="selectAll" class="form-check-label">Select All</label>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40px">
                                        <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                    </th>
                                    <th>Day</th>
                                    <th>Type</th>
                                    <th>Time Range</th>
                                    <th>Duration</th>
                                    <th>Max Bookings</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="schedule_ids[]" value="{{ $schedule->id }}" class="form-check-input schedule-checkbox">
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($schedule->day_of_week) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $schedule->booking_type == 'virtual' ? 'info' : 'success' }}">
                                            {{ $schedule->booking_type_formatted }}
                                        </span>
                                    </td>
                                    <td>{{ $schedule->time_range }}</td>
                                    <td>{{ $schedule->slot_duration }} min</td>
                                    <td>{{ $schedule->max_bookings_per_slot }}</td>
                                    <td>
                                        @if($schedule->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="editSchedule({{ $schedule->id }}, {{ $schedule->toJson() }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('admin.slot-management.schedules.delete', $schedule) }}" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5>No Schedules Found</h5>
                    <p class="text-muted">Create your first schedule to get started.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                        <i class="fas fa-plus me-2"></i>Add Schedule
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.slot-management.schedules.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Schedule Name (Optional)</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., Default Schedule">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="day_of_week" class="form-label">Day of Week</label>
                                <select class="form-select" id="day_of_week" name="day_of_week" required>
                                    <option value="">Select Day</option>
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                    <option value="sunday">Sunday</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="booking_type" class="form-label">Booking Type</label>
                                <select class="form-select" id="booking_type" name="booking_type" required>
                                    <option value="">Select Type</option>
                                    <option value="virtual">Virtual</option>
                                    <option value="in-office">In-Office</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_bookings_per_slot" class="form-label">Max Bookings per Slot</label>
                                <input type="number" class="form-control" id="max_bookings_per_slot" 
                                       name="max_bookings_per_slot" value="1" min="1" max="10">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slot_duration" class="form-label">Slot Duration (minutes)</label>
                                <select class="form-select" id="slot_duration" name="slot_duration">
                                    <option value="15">15 minutes</option>
                                    <option value="30">30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60" selected>60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">120 minutes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="break_duration" class="form-label">Break Between Slots (minutes)</label>
                                <select class="form-select" id="break_duration" name="break_duration">
                                    <option value="0" selected>No break</option>
                                    <option value="5">5 minutes</option>
                                    <option value="10">10 minutes</option>
                                    <option value="15">15 minutes</option>
                                    <option value="30">30 minutes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Optional notes about this schedule"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Schedule Name (Optional)</label>
                                <input type="text" class="form-control" id="edit_name" name="name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_day_of_week" class="form-label">Day of Week</label>
                                <select class="form-select" id="edit_day_of_week" name="day_of_week" required>
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                    <option value="sunday">Sunday</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_booking_type" class="form-label">Booking Type</label>
                                <select class="form-select" id="edit_booking_type" name="booking_type" required>
                                    <option value="virtual">Virtual</option>
                                    <option value="in-office">In-Office</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_is_active" class="form-label">Status</label>
                                <select class="form-select" id="edit_is_active" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_slot_duration" class="form-label">Slot Duration (min)</label>
                                <input type="number" class="form-control" id="edit_slot_duration" 
                                       name="slot_duration" min="15" max="240">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_break_duration" class="form-label">Break Duration (min)</label>
                                <input type="number" class="form-control" id="edit_break_duration" 
                                       name="break_duration" min="0" max="60">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_max_bookings_per_slot" class="form-label">Max Bookings</label>
                                <input type="number" class="form-control" id="edit_max_bookings_per_slot" 
                                       name="max_bookings_per_slot" min="1" max="10">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.schedule-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    document.getElementById('selectAllHeader').addEventListener('change', function() {
        document.getElementById('selectAll').checked = this.checked;
        document.getElementById('selectAll').dispatchEvent(new Event('change'));
    });

    // Bulk action form validation
    document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
        const action = this.querySelector('select[name="action"]').value;
        const checkedBoxes = this.querySelectorAll('.schedule-checkbox:checked');
        
        if (!action) {
            e.preventDefault();
            alert('Please select an action.');
            return;
        }
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one schedule.');
            return;
        }
        
        if (action === 'delete') {
            if (!confirm(`Are you sure you want to delete ${checkedBoxes.length} schedule(s)?`)) {
                e.preventDefault();
                return;
            }
        }
    });
});

function editSchedule(id, schedule) {
    // Populate the edit form
    document.getElementById('edit_name').value = schedule.name || '';
    document.getElementById('edit_day_of_week').value = schedule.day_of_week;
    document.getElementById('edit_booking_type').value = schedule.booking_type;
    document.getElementById('edit_is_active').value = schedule.is_active ? '1' : '0';
    document.getElementById('edit_start_time').value = schedule.start_time;
    document.getElementById('edit_end_time').value = schedule.end_time;
    document.getElementById('edit_slot_duration').value = schedule.slot_duration;
    document.getElementById('edit_break_duration').value = schedule.break_duration || 0;
    document.getElementById('edit_max_bookings_per_slot').value = schedule.max_bookings_per_slot;
    document.getElementById('edit_notes').value = schedule.notes || '';
    
    // Set form action
    document.getElementById('editScheduleForm').action = `/admin/slot-management/schedules/${id}`;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
    modal.show();
}
</script>
@endpush
