@extends('layouts.admin')

@section('title', 'Create New Subscription')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create New Subscription</h1>
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Subscriptions
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Subscription Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="user_id" class="form-label">Client *</label>
                                        <select class="form-select" id="user_id" name="user_id" required>
                                            <option value="">Select Client</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="program_id" class="form-label">Program *</label>
                                        <select class="form-select" id="program_id" name="program_id" required>
                                            <option value="">Select Program</option>
                                            @foreach($programs as $program)
                                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                    {{ $program->name }} - ${{ number_format($program->monthly_price, 2) }}/month
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="subscription_type" class="form-label">Subscription Type *</label>
                                        <select class="form-select" id="subscription_type" name="subscription_type" required>
                                            <option value="">Select Type</option>
                                            <option value="life_coaching" {{ old('subscription_type') == 'life_coaching' ? 'selected' : '' }}>Life Coaching</option>
                                            <option value="student" {{ old('subscription_type') == 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="professional" {{ old('subscription_type') == 'professional' ? 'selected' : '' }}>Professional</option>
                                            <option value="relationship" {{ old('subscription_type') == 'relationship' ? 'selected' : '' }}>Relationship</option>
                                            <option value="resident" {{ old('subscription_type') == 'resident' ? 'selected' : '' }}>Resident</option>
                                            <option value="fellow" {{ old('subscription_type') == 'fellow' ? 'selected' : '' }}>Fellow</option>
                                            <option value="concierge" {{ old('subscription_type') == 'concierge' ? 'selected' : '' }}>Concierge</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="monthly_price" class="form-label">Monthly Price *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="monthly_price" name="monthly_price" 
                                                   value="{{ old('monthly_price') }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="monthly_sessions" class="form-label">Monthly Sessions *</label>
                                    <input type="number" class="form-control" id="monthly_sessions" name="monthly_sessions" 
                                           value="{{ old('monthly_sessions') }}" min="1" required>
                                    <small class="form-text text-muted">Number of sessions/bookings included in monthly subscription</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="starts_at" class="form-label">Start Date *</label>
                                        <input type="date" class="form-control" id="starts_at" name="starts_at" 
                                               value="{{ old('starts_at', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ends_at" class="form-label">End Date (Optional)</label>
                                        <input type="date" class="form-control" id="ends_at" name="ends_at" 
                                               value="{{ old('ends_at') }}">
                                        <div class="form-text">Leave blank for ongoing subscription</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subscription_features" class="form-label">Subscription Features</label>
                                    <div id="subscription-features-container">
                                        @if(old('subscription_features'))
                                            @foreach(old('subscription_features') as $index => $feature)
                                                <div class="input-group mb-2 subscription-feature-input">
                                                    <input type="text" class="form-control" name="subscription_features[]" 
                                                           value="{{ $feature }}" placeholder="Enter subscription feature">
                                                    <button type="button" class="btn btn-outline-danger remove-subscription-feature">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2 subscription-feature-input">
                                                <input type="text" class="form-control" name="subscription_features[]" 
                                                       placeholder="Enter subscription feature">
                                                <button type="button" class="btn btn-outline-danger remove-subscription-feature">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-subscription-feature">
                                        <i class="fas fa-plus me-1"></i>Add Feature
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Additional notes about this subscription">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Program Information</h6>
                            </div>
                            <div class="card-body" id="program-info">
                                <p class="text-muted">Select a program to view details</p>
                            </div>
                        </div>

                        <div class="card shadow mt-3">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Create Subscription
                                </button>
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary w-100 mt-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('program_id');
    const programInfo = document.getElementById('program-info');
    const programs = @json($programs);

    // Update program info when selection changes
    programSelect.addEventListener('change', function() {
        const selectedProgramId = this.value;
        const program = programs.find(p => p.id == selectedProgramId);
        
        if (program) {
            programInfo.innerHTML = `
                <h6>${program.name}</h6>
                <p class="text-muted">${program.description}</p>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Monthly Price:</small><br>
                        <strong class="text-primary">$${parseFloat(program.monthly_price).toFixed(2)}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Monthly Sessions:</small><br>
                        <strong>${program.monthly_sessions}</strong>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">Booking Limit:</small><br>
                    <strong>${program.monthly_sessions} sessions per month</strong>
                </div>
                ${program.subscription_features && program.subscription_features.length > 0 ? `
                    <div class="mt-2">
                        <small class="text-muted">Features:</small><br>
                        <ul class="list-unstyled mb-0">
                            ${program.subscription_features.map(feature => `<li><small>• ${feature}</small></li>`).join('')}
                        </ul>
                    </div>
                ` : ''}
            `;
            
            // Auto-fill form fields
            document.getElementById('monthly_price').value = program.monthly_price;
            document.getElementById('monthly_sessions').value = program.monthly_sessions;
        } else {
            programInfo.innerHTML = '<p class="text-muted">Select a program to view details</p>';
        }
    });

    // Add subscription feature functionality
    document.getElementById('add-subscription-feature').addEventListener('click', function() {
        const container = document.getElementById('subscription-features-container');
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mb-2 subscription-feature-input';
        newFeature.innerHTML = `
            <input type="text" class="form-control" name="subscription_features[]" placeholder="Enter subscription feature">
            <button type="button" class="btn btn-outline-danger remove-subscription-feature">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newFeature);
    });

    // Remove subscription feature functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-subscription-feature') || e.target.parentElement.classList.contains('remove-subscription-feature')) {
            e.target.closest('.subscription-feature-input').remove();
        }
    });
});
</script>
@endsection
