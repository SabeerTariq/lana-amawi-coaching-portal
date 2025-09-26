@extends('layouts.admin')

@section('title')
    @if(request('search') || request('status'))
        Filtered Appointments - Admin Dashboard
    @else
        Appointments - Admin Dashboard
    @endif
@endsection

@push('styles')
<style>
    .filter-form .form-label {
        font-weight: 600;
        color: #495057;
    }
    
    .filter-form .form-control,
    .filter-form .form-select {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .filter-form .form-control:focus,
    .filter-form .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .filter-form .btn {
        border-radius: 0.375rem;
        font-weight: 500;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    .filter-form.submitting {
        opacity: 0.7;
        pointer-events: none;
    }
    
    .filter-form.submitting .btn {
        position: relative;
    }
    
    .filter-form.submitting .btn::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
    
    @keyframes spin {
        0% { transform: translateY(-50%) rotate(0deg); }
        100% { transform: translateY(-50%) rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Appointment Management</h1>
            @if(request('search') || request('status'))
                <small class="text-muted">
                    Filtered results: {{ $appointments->total() }} appointment{{ $appointments->total() != 1 ? 's' : '' }}
                    @if(request('search'))
                        matching "{{ request('search') }}"
                    @endif
                    @if(request('status'))
                        with status "{{ ucfirst(request('status')) }}"
                    @endif
                </small>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 filter-form">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Search by client name or email..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.appointments') }}" class="btn btn-outline-secondary" onclick="clearAllFilters()">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Appointments</h6>
            <div class="d-flex align-items-center gap-3">
                @if(request('search') || request('status'))
                    <small class="text-muted">
                        Showing {{ $appointments->total() }} result{{ $appointments->total() != 1 ? 's' : '' }}
                        @if(request('search'))
                            for "{{ request('search') }}"
                        @endif
                    </small>
                @endif
                <small class="text-muted">
                    Total: {{ \App\Models\Appointment::count() }} appointments
                </small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="appointmentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-bold">{{ $appointment->user->name }}</div>
                                        <small class="text-muted">{{ $appointment->user->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $appointment->appointment_date->format('M d, Y') }}</div>
                                                                            <small class="text-muted">{{ $appointment->formatted_time }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $appointment->status_badge_color }} status-badge">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                                                          title="{{ $appointment->message ?? 'No notes' }}">
                                {{ $appointment->message ?? 'No notes' }}
                                    </span>
                                </td>
                                <td>{{ $appointment->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($appointment->status === 'pending')
                                            <form action="{{ route('admin.appointments.confirm', $appointment) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        onclick="return confirm('Confirm this appointment?')">
                                                    <i class="fas fa-check me-1"></i>Confirm
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->status === 'confirmed')
                                            <form action="{{ route('admin.appointments.complete', $appointment) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-info" 
                                                        onclick="return confirm('Mark this appointment as completed?')">
                                                    <i class="fas fa-check-double me-1"></i>Complete
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                            <form action="{{ route('admin.appointments.cancel', $appointment) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Cancel this appointment?')">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('admin.clients.profile', $appointment->user) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-user me-1"></i>View Client
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar fa-3x mb-3"></i>
                                        @if(request('search') || request('status'))
                                            <p>No appointments found matching your filters</p>
                                            <small>Try adjusting your search criteria or <a href="{{ route('admin.appointments') }}">clear all filters</a></small>
                                        @else
                                            <p>No appointments found</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($appointments->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $appointments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#appointmentsTable').DataTable({
            "pageLength": 25,
            "order": [[1, "desc"]], // Sort by date
            "language": {
                "search": "Search appointments:",
                "lengthMenu": "Show _MENU_ appointments per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ appointments"
            }
        });

        // Auto-submit form when status changes
        $('#status').change(function() {
            if ($(this).val() !== '') {
                submitForm();
            }
        });

        // Handle search input with debouncing
        let searchTimeout;
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if ($('#search').val().length >= 2 || $('#search').val().length === 0) {
                    submitForm();
                }
            }, 500);
        });

        // Prevent double form submissions
        function submitForm() {
            if (!$('form').hasClass('submitting')) {
                $('form').addClass('submitting');
                $('form').submit();
            }
        }

        // Handle form submission
        $('form').on('submit', function() {
            if ($(this).hasClass('submitting')) {
                return true;
            }
            $(this).addClass('submitting');
            return true;
        });

        // Show active filters
        function showActiveFilters() {
            let activeFilters = [];
            if ($('#search').val()) {
                activeFilters.push('Search: "' + $('#search').val() + '"');
            }
            if ($('#status').val()) {
                activeFilters.push('Status: ' + $('#status option:selected').text());
            }
            
            if (activeFilters.length > 0) {
                let filterInfo = '<div class="alert alert-info alert-dismissible fade show mt-3" role="alert">' +
                    '<strong>Active Filters:</strong> ' + activeFilters.join(', ') +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                
                if ($('.alert-info').length === 0) {
                    $('.card-body').after(filterInfo);
                }
            }
        }

        // Show active filters on page load
        showActiveFilters();

        // Function to clear all filters
        window.clearAllFilters = function() {
            // Clear form inputs
            $('#search').val('');
            $('#status').val('');
            
            // Clear session filters by redirecting with a clear parameter
            window.location.href = '{{ route("admin.appointments") }}?clear=1';
        };
    });
</script>
@endpush
@endsection 