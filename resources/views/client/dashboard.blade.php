@extends('layouts.client')

@section('title', 'Dashboard - Client')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-sun me-2 text-warning"></i>Welcome back, {{ Auth::user()->name }}!
                </h5>
                <p class="card-text text-muted">Here's what's happening with your coaching journey today.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Next Appointment -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar-check me-2 text-primary"></i>Next Appointment
                </h5>
            </div>
            <div class="card-body">
                @if(isset($nextAppointment))
                    <div class="mb-3">
                        <h6 class="mb-1">Coaching Session</h6>
                        <p class="text-muted mb-0">
                            {{ $nextAppointment->appointment_date->format('l, F j, Y') }} at 
                            {{ $nextAppointment->formatted_time }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Reschedule
                        </button>
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No upcoming appointments</h6>
                        <p class="text-muted">Book your next session to continue your coaching journey.</p>
                        <a href="{{ route('client.appointments') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Book Session
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-comments me-2 text-success"></i>Recent Messages
                </h5>
            </div>
            <div class="card-body">
                @if(isset($recentMessages) && count($recentMessages) > 0)
                    @foreach($recentMessages->take(3) as $message)
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1">Lana Amawi</h6>
                                    <small class="text-muted">
                                        {{ $message->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <p class="text-muted mb-0">{{ Str::limit($message->message, 100) }}</p>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('client.messages') }}" class="btn btn-outline-primary btn-sm">
                            View All Messages
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-comments text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No recent messages</h6>
                        <p class="text-muted">Start a conversation with Lana to get the most out of your coaching.</p>
                        <a href="{{ route('client.messages') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Send Message
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-calendar-alt text-primary mb-3" style="font-size: 2rem;"></i>
                <h4 class="card-title">{{ $totalAppointments ?? 0 }}</h4>
                <p class="card-text text-muted">Total Sessions</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-comments text-success mb-3" style="font-size: 2rem;"></i>
                <h4 class="card-title">{{ $totalMessages ?? 0 }}</h4>
                <p class="card-text text-muted">Messages Exchanged</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock text-warning mb-3" style="font-size: 2rem;"></i>
                <h4 class="card-title">{{ $hoursCoached ?? 0 }}</h4>
                <p class="card-text text-muted">Hours Coached</p>
            </div>
        </div>
    </div>
</div>
@endsection 