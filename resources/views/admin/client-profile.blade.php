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
               class="btn btn-primary">
                <i class="fas fa-comment me-2"></i>Send Message
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Client Info Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Client Information</h6>
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $client->name }}</h5>
                    <p class="text-muted">{{ $client->email }}</p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="text-primary">{{ $appointments->count() }}</h6>
                            <small class="text-muted">Appointments</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-primary">{{ $messages->count() }}</h6>
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
                    <h6 class="m-0 font-weight-bold text-primary">Appointment History</h6>
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
                                            <td>{{ $appointment->formatted_time }}</td>
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

    <!-- Admin Notes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-sticky-note me-2"></i>Admin Notes
            </h6>
            <button type="button" 
                    class="btn btn-primary btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#addNoteModal">
                <i class="fas fa-plus me-2"></i>Add Note
            </button>
        </div>
        <div class="card-body">
            @if($client->notes->count() > 0)
                <div class="notes-list">
                    @foreach($client->notes()->orderBy('created_at', 'desc')->get() as $note)
                        <div class="note-item border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="text-primary">{{ $note->admin->name }}</strong>
                                    <small class="text-muted ms-2">{{ $note->created_at->format('M d, Y g:i A') }}</small>
                                </div>
                                @if(Auth::id() === $note->admin_id || Auth::user()->is_admin)
                                    <form action="{{ route('admin.clients.notes.delete', $note) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this note?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="note-content">
                                {{ $note->note }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-sticky-note fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No admin notes yet for this client</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="card shadow">
        <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Messages</h6>
        </div>
        <div class="card-body">
            @if($messages->count() > 0)
                <div class="timeline">
                    @foreach($messages->take(10) as $message)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $message->sender_type === 'admin' ? 'bg-primary' : 'bg-secondary' }}" 
                                 ></div>
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

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNoteModalLabel">
                    <i class="fas fa-sticky-note me-2"></i>Add Note for {{ $client->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.clients.notes.add', $client) }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="note" class="form-label">Note Content</label>
                        <textarea class="form-control" 
                                  id="note" 
                                  name="note" 
                                  rows="4" 
                                  placeholder="Enter your note about this client..." 
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Note
                    </button>
                </div>
            </form>
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
                border-left: 3px solid var(--bs-primary);
}

.timeline-title {
    margin-bottom: 5px;
}

.timeline-text {
    margin-bottom: 0;
}


</style>
@endsection 