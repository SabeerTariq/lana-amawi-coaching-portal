@extends('layouts.admin')

@section('title', 'Programs Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Programs Management</h1>
                <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create New Program
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">All Programs</h6>
                </div>
                <div class="card-body">
                    @if($programs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Monthly Price</th>
                                        <th>Sessions/Bookings per Month</th>
                                        <th>Applications</th>
                                        <th>Subscriptions</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($programs as $program)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $program->name }}</strong>
                                                </div>
                                                <small class="text-muted">{{ Str::limit($program->description, 50) }}</small>
                                            </td>
                                            <td>
                                                @if($program->subscription_type)
                                                    <span class="badge bg-secondary">{{ $program->formatted_subscription_type }}</span>
                                                @else
                                                    <span class="badge bg-info">General</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($program->monthly_price)
                                                    <div>
                                                    <span class="fw-bold text-primary">${{ number_format($program->monthly_price, 2) }}/mo</span>
                                                        <br>
                                                        <small class="text-muted">
                                                            One-time: 
                                                            @if($program->one_time_payment_amount)
                                                                ${{ number_format($program->one_time_payment_amount, 2) }}
                                                            @else
                                                                Not set
                                                            @endif
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    @if($program->monthly_sessions)
                                                        <span class="badge bg-info">{{ $program->monthly_sessions }} sessions/month</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $program->user_programs_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $program->subscriptions_count }}</span>
                                            </td>
                                            <td>
                                                @if($program->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.programs.edit', $program) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       data-bs-toggle="tooltip" 
                                                       title="Edit Program">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('admin.programs.applications') }}?program={{ $program->id }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       data-bs-toggle="tooltip" 
                                                       title="View Applications">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('admin.programs.toggle-status', $program) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm {{ $program->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                                data-bs-toggle="tooltip" 
                                                                title="{{ $program->is_active ? 'Deactivate' : 'Activate' }} Program"
                                                                onclick="return confirm('Are you sure you want to {{ $program->is_active ? 'deactivate' : 'activate' }} this program?')">
                                                            <i class="fas {{ $program->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.programs.destroy', $program) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this program? This action cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="tooltip" 
                                                                title="Delete Program">
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
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Programs Found</h5>
                            <p class="text-muted">Get started by creating your first program.</p>
                            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Program
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
