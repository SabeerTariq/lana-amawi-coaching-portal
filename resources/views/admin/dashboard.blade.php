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
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $totalClients ?? 0 }}</h4>
                        <p class="mb-0">Total Clients</p>
                    </div>
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $appointmentsToday ?? 0 }}</h4>
                        <p class="mb-0">Appointments Today</p>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $unreadMessages ?? 0 }}</h4>
                        <p class="mb-0">Unread Messages</p>
                    </div>
                    <i class="fas fa-envelope fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $totalRevenue ?? '$0' }}</h4>
                        <p class="mb-0">Total Revenue</p>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ \App\Models\Booking::where('status', 'pending')->count() }}</h4>
                        <p class="mb-0">Pending Bookings</p>
                    </div>
                    <i class="fas fa-calendar-plus fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Appointments -->
    <div class="col-md-8 mb-4">
        <div class="card">
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
                                    <th>Time</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_time }}</td>
                                        <td>{{ $appointment->client_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>
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
    <div class="col-md-4 mb-4">
        <div class="card">
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

        <!-- Recent Activity -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Recent Activity
                </h5>
            </div>
            <div class="card-body">
                @if(isset($recentActivity) && count($recentActivity) > 0)
                    @foreach($recentActivity->take(5) as $activity)
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-grow-1">
                                <p class="mb-1 small">{{ $activity->description }}</p>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">No recent activity</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Appointments This Week
                </h5>
            </div>
            <div class="card-body">
                <canvas id="appointmentsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(appointmentsCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Appointments',
                data: [3, 5, 2, 8, 4, 1, 0],
                borderColor: '#D2691E',
                backgroundColor: 'rgba(210, 105, 30, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });


});
</script>
@endsection 