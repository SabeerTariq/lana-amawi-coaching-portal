@extends('layouts.admin')

@section('title', 'Clients - Admin Dashboard')

@section('content')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}

/* Agreement File Link Styling */
.agreement-link {
    color: #0d6efd;
    transition: all 0.2s ease;
    cursor: pointer;
}

.agreement-link:hover {
    color: #0a58ca;
    text-decoration: underline !important;
    transform: translateY(-1px);
}

.agreement-link:active {
    transform: translateY(0);
}

.agreement-link.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Client Management</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.bookings') }}" class="btn btn-warning">
                <i class="fas fa-file-pdf me-2"></i>View All Agreements
            </a>
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

    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Clients</h6>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="agreementFilter" style="width: auto;">
                    <option value="">All Clients</option>
                    <option value="with-agreement">With Agreement File</option>
                    <option value="no-agreement">No Agreement File</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="clientsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Appointments</th>
                            <th>Messages</th>
                            <th data-bs-toggle="tooltip" data-bs-placement="top" title="Click 'Agreement File' to view the uploaded agreement">Agreement</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $client->name }}</div>
                                </td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $client->appointments_count }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $client->messages_count }}</span>
                                </td>
                                <td>
                                    @if($client->hasSignedAgreement())
                                        <a href="{{ $client->agreement_url }}" 
                                           target="_blank" 
                                           class="text-decoration-none agreement-link"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Click to view agreement (opens in new tab)"
                                           aria-label="View agreement file for {{ $client->name }}">
                                            <i class="fas fa-file-pdf text-success me-1"></i>
                                            <strong>Agreement File</strong>
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-times-circle me-1"></i>
                                            No Agreement
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $lastAppointment = $client->appointments()->latest()->first();
                                        $status = 'Inactive';
                                        if ($lastAppointment && $lastAppointment->created_at->diffInDays(now()) <= 30) {
                                            $status = 'Active';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $status === 'Active' ? 'success' : 'warning' }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.clients.profile', $client) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('admin.messages') }}?client_id={{ $client->id }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-comment me-1"></i>Message
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addNoteModal{{ $client->id }}">
                                            <i class="fas fa-sticky-note me-1"></i>Notes
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>No clients found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clients->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Notes Modals for Each Client -->
@foreach($clients as $client)
    <div class="modal fade" id="addNoteModal{{ $client->id }}" tabindex="-1" aria-labelledby="addNoteModalLabel{{ $client->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel{{ $client->id }}">
                        <i class="fas fa-sticky-note me-2"></i>Client Notes - {{ $client->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add New Note Form -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Note</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.clients.notes.add', $client) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="note{{ $client->id }}" class="form-label">Note Content</label>
                                    <textarea class="form-control" 
                                              id="note{{ $client->id }}" 
                                              name="note" 
                                              rows="3" 
                                              placeholder="Enter your note about this client..." 
                                              required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Add Note
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Existing Notes -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Existing Notes</h6>
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
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-sticky-note fa-3x mb-3"></i>
                                    <p>No notes yet for this client</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#clientsTable').DataTable({
            "pageLength": 25,
            "order": [[2, "desc"]], // Sort by joined date
            "language": {
                "search": "Search clients:",
                "lengthMenu": "Show _MENU_ clients per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ clients"
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Agreement filter functionality
        $('#agreementFilter').on('change', function() {
            var filterValue = $(this).val();
            
            // Custom filtering function
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (filterValue === '') return true; // Show all
                
                var agreementCell = data[5]; // Agreement column index
                
                if (filterValue === 'with-agreement') {
                    return agreementCell.includes('Agreement File');
                } else if (filterValue === 'no-agreement') {
                    return agreementCell.includes('No Agreement');
                }
                
                return true;
            });
            
            table.draw();
            
            // Remove the filter function after drawing
            $.fn.dataTable.ext.search.pop();
        });

        // Agreement link click handling
        $(document).on('click', '.agreement-link', function(e) {
            var $link = $(this);
            var originalText = $link.html();
            
            // Show loading state
            $link.html('<i class="fas fa-spinner fa-spin text-success me-1"></i><strong>Loading...</strong>');
            $link.addClass('disabled');
            
            // Reset after a short delay (allows the new tab to open)
            setTimeout(function() {
                $link.html(originalText);
                $link.removeClass('disabled');
            }, 1000);
        });
    });
</script>
@endpush
@endsection 