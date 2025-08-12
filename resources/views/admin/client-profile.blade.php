@extends('layouts.admin')

@section('title', $client->name . ' - Client Profile')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Client Profile</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clients') }}">Clients</a></li>
                    <li class="breadcrumb-item active">{{ $client->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.messages') }}?client_id={{ $client->id }}" 
               class="btn btn-primary" style="background-color: #730623 !important; border-color: #730623 !important;">
                <i class="fas fa-comment me-2"></i>Send Message
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Client Info Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #730623 !important;">Client Information</h6>
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $client->name }}</h5>
                    <p class="text-muted">{{ $client->email }}</p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 style="color: #730623 !important;">{{ $appointments->count() }}</h6>
                            <small class="text-muted">Appointments</small>
                        </div>
                        <div class="col-6">
                            <h6 style="color: #730623 !important;">{{ $messages->count() }}</h6>
                            <small class="text-muted">Messages</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-start">
                        <p><strong>Member since:</strong> {{ $client->created_at->format('M d, Y') }}</p>
                        <p><strong>Last activity:</strong> {{ $client->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold" style="color: #730623 !important;">Appointment History</h6>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                                            <td>{{ $appointment->appointment_time }}</td>
                                                                                         <td>
                                                 <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'pending' ? 'warning' : ($appointment->status === 'completed' ? 'info' : 'danger')) }}">
                                                     {{ ucfirst($appointment->status) }}
                                                 </span>
                                             </td>
                                            <td>{{ $appointment->message ?? 'No notes' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No appointments found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="card shadow">
        <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold" style="color: #730623 !important;">Recent Messages</h6>
        </div>
        <div class="card-body">
            @if($messages->count() > 0)
                <div class="timeline">
                    @foreach($messages->take(10) as $message)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $message->sender_type === 'admin' ? 'bg-primary' : 'bg-secondary' }}" 
                                 style="{{ $message->sender_type === 'admin' ? 'background-color: #730623 !important;' : '' }}"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <h6 class="timeline-title">{{ ucfirst($message->sender_type) }}</h6>
                                    <small class="text-muted">{{ $message->created_at->format('M d, Y g:i A') }}</small>
                                </div>
                                <p class="timeline-text">
                                    @if(!empty($message->message))
                                        {{ $message->message }}
                                    @elseif($message->hasAttachment())
                                        <em class="text-muted">File attached</em>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No messages found</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
                border-left: 3px solid #730623;
}

.timeline-title {
    margin-bottom: 5px;
}

.timeline-text {
    margin-bottom: 0;
}

/* Update button colors to match the new theme */
.btn-primary {
                background-color: #730623 !important;
            border-color: #730623 !important;
}

.btn-primary:hover {
                background-color: #8a0a2a !important;
            border-color: #8a0a2a !important;
}
</style>
@endsection 