@extends('layouts.admin')

@section('title', 'Create New Program')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create New Program</h1>
                <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Programs
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

            <form action="{{ route('admin.programs.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Program Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Program Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="subscription_type" class="form-label">Subscription Type</label>
                                        <select class="form-select" id="subscription_type" name="subscription_type">
                                            <option value="">Select Type</option>
                                            <option value="student" {{ old('subscription_type') == 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="resident" {{ old('subscription_type') == 'resident' ? 'selected' : '' }}>Resident/Fellow</option>
                                            <option value="medical" {{ old('subscription_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                                            <option value="concierge" {{ old('subscription_type') == 'concierge' ? 'selected' : '' }}>Medical Concierge</option>
                                            <option value="relationship" {{ old('subscription_type') == 'relationship' ? 'selected' : '' }}>Relationship</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label">One-time Price *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   value="{{ old('price') }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="duration_weeks" class="form-label">Duration (weeks)</label>
                                        <input type="number" class="form-control" id="duration_weeks" name="duration_weeks" 
                                               value="{{ old('duration_weeks') }}" min="1">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="sessions_included" class="form-label">Sessions Included</label>
                                        <input type="number" class="form-control" id="sessions_included" name="sessions_included" 
                                               value="{{ old('sessions_included') }}" min="1">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="features" class="form-label">Program Features</label>
                                    <div id="features-container">
                                        @if(old('features'))
                                            @foreach(old('features') as $index => $feature)
                                                <div class="input-group mb-2 feature-input">
                                                    <input type="text" class="form-control" name="features[]" 
                                                           value="{{ $feature }}" placeholder="Enter feature">
                                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2 feature-input">
                                                <input type="text" class="form-control" name="features[]" 
                                                       placeholder="Enter feature">
                                                <button type="button" class="btn btn-outline-danger remove-feature">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                                        <i class="fas fa-plus me-1"></i>Add Feature
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Subscription Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_subscription_based" 
                                           name="is_subscription_based" value="1" 
                                           {{ old('is_subscription_based') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_subscription_based">
                                        Enable Subscription Model
                                    </label>
                                </div>

                                <div id="subscription-fields" style="display: none;">
                                    <div class="mb-3">
                                        <label for="monthly_price" class="form-label">Monthly Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="monthly_price" name="monthly_price" 
                                                   value="{{ old('monthly_price') }}" step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="monthly_sessions" class="form-label">Monthly Sessions</label>
                                        <input type="number" class="form-control" id="monthly_sessions" name="monthly_sessions" 
                                               value="{{ old('monthly_sessions') }}" min="1">
                                    </div>

                                    <div class="mb-3">
                                        <label for="booking_limit_per_month" class="form-label">Booking Limit per Month</label>
                                        <input type="number" class="form-control" id="booking_limit_per_month" name="booking_limit_per_month" 
                                               value="{{ old('booking_limit_per_month') }}" min="1">
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
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Program
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mt-3">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Create Program
                                </button>
                                <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary w-100 mt-2">
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
    const subscriptionCheckbox = document.getElementById('is_subscription_based');
    const subscriptionFields = document.getElementById('subscription-fields');

    // Toggle subscription fields
    subscriptionCheckbox.addEventListener('change', function() {
        if (this.checked) {
            subscriptionFields.style.display = 'block';
        } else {
            subscriptionFields.style.display = 'none';
        }
    });

    // Initialize subscription fields visibility
    if (subscriptionCheckbox.checked) {
        subscriptionFields.style.display = 'block';
    }

    // Add feature functionality
    document.getElementById('add-feature').addEventListener('click', function() {
        const container = document.getElementById('features-container');
        const newFeature = document.createElement('div');
        newFeature.className = 'input-group mb-2 feature-input';
        newFeature.innerHTML = `
            <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
            <button type="button" class="btn btn-outline-danger remove-feature">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newFeature);
    });

    // Remove feature functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-feature') || e.target.parentElement.classList.contains('remove-feature')) {
            e.target.closest('.feature-input').remove();
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
