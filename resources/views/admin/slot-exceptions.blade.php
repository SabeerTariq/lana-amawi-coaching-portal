@extends('layouts.admin')

@section('title', 'Exception Management - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Exception Management
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.enhanced-slot-management') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExceptionModal">
                <i class="fas fa-plus me-2"></i>Add Exception
            </button>
        </div>
    </div>
</div>

<!-- Quick Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="filter_date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="filter_date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="filter_date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="filter_type" class="form-label">Exception Type</label>
                        <select class="form-select" id="filter_type" name="type">
                            <option value="">All Types</option>
                            <option value="blocked" {{ request('type') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            <option value="modified" {{ request('type') == 'modified' ? 'selected' : '' }}>Modified</option>
                            <option value="closed" {{ request('type') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="filter_booking_type" class="form-label">Booking Type</label>
                        <select class="form-select" id="filter_booking_type" name="booking_type">
                            <option value="">All Types</option>
                            <option value="virtual" {{ request('booking_type') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="in-office" {{ request('booking_type') == 'in-office' ? 'selected' : '' }}>In-Office</option>
                            <option value="both" {{ request('booking_type') == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('admin.slot-management.exceptions') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Exceptions Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Slot Exceptions
                </h5>
            </div>
            <div class="card-body">
                @if($exceptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Booking Type</th>
                                <th>Exception Type</th>
                                <th>Time Range</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exceptions as $exception)
                            <tr class="{{ $exception->exception_date->isPast() ? 'text-muted' : '' }}">
                                <td>
                                    <div>
                                        <strong>{{ $exception->exception_date->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $exception->exception_date->format('l') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $exception->booking_type == 'virtual' ? 'info' : ($exception->booking_type == 'in-office' ? 'success' : 'primary') }}">
                                        {{ $exception->booking_type_formatted }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $exception->exception_type_badge_color }}">
                                        {{ $exception->exception_type_formatted }}
                                    </span>
                                </td>
                                <td>{{ $exception->time_range }}</td>
                                <td>
                                    @if($exception->reason)
                                        <span data-bs-toggle="tooltip" title="{{ $exception->reason }}">
                                            {{ Str::limit($exception->reason, 30) }}
                                        </span>
                                    @else
                                        <span class="text-muted">No reason</span>
                                    @endif
                                </td>
                                <td>
                                    @if($exception->is_active)
                                        @if($exception->exception_date->isPast())
                                            <span class="badge bg-secondary">Expired</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="editException({{ $exception->id }}, {{ $exception->toJson() }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.slot-management.exceptions.delete', $exception) }}" 
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
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $exceptions->appends(request()->query())->links() }}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                    <h5>No Exceptions Found</h5>
                    <p class="text-muted">No slot exceptions have been created yet.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExceptionModal">
                        <i class="fas fa-plus me-2"></i>Add Exception
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Exception Modal -->
<div class="modal fade" id="addExceptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Exception</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.slot-management.exceptions.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exception_date" class="form-label">Exception Date</label>
                        <input type="date" class="form-control" id="exception_date" name="exception_date" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="booking_type" class="form-label">Booking Type</label>
                        <select class="form-select" id="booking_type" name="booking_type" required>
                            <option value="">Select Type</option>
                            <option value="both">Both Types</option>
                            <option value="virtual">Virtual Only</option>
                            <option value="in-office">In-Office Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exception_type" class="form-label">Exception Type</label>
                        <select class="form-select" id="exception_type" name="exception_type" required>
                            <option value="">Select Exception Type</option>
                            <option value="blocked">Block Specific Time Range</option>
                            <option value="modified">Modified Hours</option>
                            <option value="closed">Close Entire Day</option>
                        </select>
                    </div>
                    <div class="row" id="timeRangeFields" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" 
                                  placeholder="Optional reason for this exception"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Exception</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Exception Modal -->
<div class="modal fade" id="editExceptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Exception</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editExceptionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_exception_date" class="form-label">Exception Date</label>
                        <input type="date" class="form-control" id="edit_exception_date" name="exception_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_booking_type" class="form-label">Booking Type</label>
                        <select class="form-select" id="edit_booking_type" name="booking_type" required>
                            <option value="both">Both Types</option>
                            <option value="virtual">Virtual Only</option>
                            <option value="in-office">In-Office Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_exception_type" class="form-label">Exception Type</label>
                        <select class="form-select" id="edit_exception_type" name="exception_type" required>
                            <option value="blocked">Block Specific Time Range</option>
                            <option value="modified">Modified Hours</option>
                            <option value="closed">Close Entire Day</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_is_active" class="form-label">Status</label>
                        <select class="form-select" id="edit_is_active" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="row" id="editTimeRangeFields">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="edit_start_time" name="start_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="edit_end_time" name="end_time">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="edit_reason" name="reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Exception</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle exception type change for add modal
    document.getElementById('exception_type').addEventListener('change', function() {
        const timeFields = document.getElementById('timeRangeFields');
        
        if (this.value === 'closed') {
            timeFields.style.display = 'none';
        } else if (this.value === 'blocked' || this.value === 'modified') {
            timeFields.style.display = 'block';
        } else {
            timeFields.style.display = 'none';
        }
    });

    // Handle exception type change for edit modal
    document.getElementById('edit_exception_type').addEventListener('change', function() {
        const timeFields = document.getElementById('editTimeRangeFields');
        
        if (this.value === 'closed') {
            timeFields.style.display = 'none';
        } else if (this.value === 'blocked' || this.value === 'modified') {
            timeFields.style.display = 'block';
        }
    });
});

function editException(id, exception) {
    // Populate the edit form
    document.getElementById('edit_exception_date').value = exception.exception_date;
    document.getElementById('edit_booking_type').value = exception.booking_type;
    document.getElementById('edit_exception_type').value = exception.exception_type;
    document.getElementById('edit_is_active').value = exception.is_active ? '1' : '0';
    document.getElementById('edit_start_time').value = exception.start_time || '';
    document.getElementById('edit_end_time').value = exception.end_time || '';
    document.getElementById('edit_reason').value = exception.reason || '';
    
    // Handle time fields visibility
    const timeFields = document.getElementById('editTimeRangeFields');
    if (exception.exception_type === 'closed') {
        timeFields.style.display = 'none';
    } else {
        timeFields.style.display = 'block';
    }
    
    // Set form action
    document.getElementById('editExceptionForm').action = `/admin/slot-management/exceptions/${id}`;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editExceptionModal'));
    modal.show();
}
</script>
@endpush
