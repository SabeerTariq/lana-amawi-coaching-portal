@extends('layouts.admin')

@section('title', 'Appointments - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Appointment Management</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Export
            </button>
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
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Appointments</h6>
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
                                    <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : ($appointment->status === 'completed' ? 'info' : 'danger')) }}">
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
                                        <p>No appointments found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($appointments->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#appointmentsTable').DataTable({
            "pageLength": 25,
            "order": [[1, "desc"]], // Sort by date
            "language": {
                "search": "Search appointments:",
                "lengthMenu": "Show _MENU_ appointments per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ appointments"
            }
        });
    });
</script>
@endpush
@endsection 