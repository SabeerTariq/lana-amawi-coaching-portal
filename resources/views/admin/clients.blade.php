@extends('layouts.admin')

@section('title', 'Clients - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Client Management</h1>
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

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Clients</h6>
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#clientsTable').DataTable({
            "pageLength": 25,
            "order": [[2, "desc"]], // Sort by joined date
            "language": {
                "search": "Search clients:",
                "lengthMenu": "Show _MENU_ clients per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ clients"
            }
        });
    });
</script>
@endpush
@endsection 