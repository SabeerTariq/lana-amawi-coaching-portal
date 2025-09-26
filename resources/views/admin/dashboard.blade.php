@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Admin Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Print</button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fs-4">{{ $totalClients ?? 0 }}</h4>
                        <p class="mb-0 small">Total Clients</p>
                    </div>
                    <i class="fas fa-users fa-2x opacity-75 ms-2"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fs-4">{{ $appointmentsToday ?? 0 }}</h4>
                        <p class="mb-0 small">Appointments Today</p>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-75 ms-2"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fs-4">{{ $unreadMessages ?? 0 }}</h4>
                        <p class="mb-0 small">Unread Messages</p>
                    </div>
                    <i class="fas fa-envelope fa-2x opacity-75 ms-2"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fs-4">{{ $totalRevenue ?? '$0' }}</h4>
                        <p class="mb-0 small">Total Revenue</p>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-75 ms-2"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-0 fs-4">{{ \App\Models\Booking::whereNotIn('status', ['completed', 'cancelled'])->count() }}</h4>
                        <p class="mb-0 small">Active Bookings</p>
                    </div>
                    <i class="fas fa-calendar-plus fa-2x opacity-75 ms-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Appointments -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Today's Appointments
                </h5>
            </div>
            <div class="card-body">
                @if(isset($todayAppointments) && count($todayAppointments) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="d-none d-md-table-cell">Time</th>
                                    <th>Client</th>
                                    <th class="d-none d-lg-table-cell">Status</th>
                                    <th class="d-none d-md-table-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayAppointments as $appointment)
                                    <tr>
                                        <td class="d-none d-md-table-cell">{{ $appointment->formatted_time }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $appointment->client_name }}</span>
                                                <small class="text-muted d-md-none">{{ $appointment->formatted_time }}</small>
                                            </div>
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No appointments today</h6>
                        <p class="text-muted">Enjoy your free day!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.clients') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>View All Clients
                    </a>
                    <a href="{{ route('admin.messages') }}" class="btn btn-outline-success">
                        <i class="fas fa-comments me-2"></i>Check Messages
                    </a>
                    <a href="{{ route('admin.calendar') }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar-alt me-2"></i>View Calendar
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </div>
            </div>
        </div>


    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mt-4">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Appointments This Week
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="appointmentsChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <h6 class="text-muted mb-1">Total Appointments This Week</h6>
                    <h4 class="text-primary">{{ collect($weeklyAppointments)->sum('count') }}</h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Appointment Status
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="row mt-3 text-center">
                    <div class="col-3">
                        <div class="border-end">
                            <h6 class="text-success mb-1">{{ $appointmentStatusCounts['confirmed'] }}</h6>
                            <small class="text-muted">Confirmed</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border-end">
                            <h6 class="text-warning mb-1">{{ $appointmentStatusCounts['pending'] }}</h6>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border-end">
                            <h6 class="text-info mb-1">{{ $appointmentStatusCounts['completed'] }}</h6>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <h6 class="text-danger mb-1">{{ $appointmentStatusCounts['cancelled'] }}</h6>
                        <small class="text-muted">Cancelled</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-card .card-body {
        padding: 1rem;
    }
    
    .stats-card h4 {
        font-size: 1.5rem !important;
    }
    
    .stats-card i {
        font-size: 1.5rem !important;
    }
    
    .btn-toolbar {
        margin-top: 1rem;
    }
    
    .btn-toolbar .btn-group {
        width: 100%;
    }
    
    .btn-toolbar .btn {
        flex: 1;
    }
}

@media (max-width: 576px) {
    .stats-card .card-body {
        padding: 0.75rem;
    }
    
    .stats-card h4 {
        font-size: 1.25rem !important;
    }
    
    .stats-card p {
        font-size: 0.75rem !important;
    }
    
    .stats-card i {
        font-size: 1.25rem !important;
    }
}

.chart-container {
    min-height: 250px;
}

/* Ensure cards have consistent height */
.card {
    height: 100%;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table td, .table th {
        padding: 0.5rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(appointmentsCtx, {
        type: 'line',
        data: {
            labels: @json(collect($weeklyAppointments)->pluck('date')),
            datasets: [{
                label: 'Appointments',
                data: @json(collect($weeklyAppointments)->pluck('count')),
                borderColor: '#730623',
                backgroundColor: 'rgba(115, 6, 35, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Confirmed', 'Pending', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $appointmentStatusCounts['confirmed'] }},
                    {{ $appointmentStatusCounts['pending'] }},
                    {{ $appointmentStatusCounts['completed'] }},
                    {{ $appointmentStatusCounts['cancelled'] }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
</script>
@endsection 