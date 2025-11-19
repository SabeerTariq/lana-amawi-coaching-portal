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

            <form action="{{ route('admin.programs.store') }}" method="POST" enctype="multipart/form-data">
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
                                            <option value="life_coaching" {{ old('subscription_type') == 'life_coaching' ? 'selected' : '' }}>Life Coaching</option>
                                            <option value="student" {{ old('subscription_type') == 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="professional" {{ old('subscription_type') == 'professional' ? 'selected' : '' }}>Professional</option>
                                            <option value="relationship" {{ old('subscription_type') == 'relationship' ? 'selected' : '' }}>Relationship</option>
                                            <option value="resident" {{ old('subscription_type') == 'resident' ? 'selected' : '' }}>Resident</option>
                                            <option value="fellow" {{ old('subscription_type') == 'fellow' ? 'selected' : '' }}>Fellow</option>
                                            <option value="concierge" {{ old('subscription_type') == 'concierge' ? 'selected' : '' }}>Concierge</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
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
                                <h6 class="m-0 font-weight-bold text-primary">3-Month Contract Settings</h6>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="is_subscription_based" value="1">
                                
                                <div class="mb-3">
                                    <label for="monthly_price" class="form-label">Monthly Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="monthly_price" name="monthly_price" 
                                               value="{{ old('monthly_price') }}" step="0.01" min="0" required
                                               oninput="updatePaymentOptions()">
                                    </div>
                                    <small class="form-text text-muted">This is the monthly subscription price</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="one_time_payment_amount" class="form-label">One-Time Payment Amount (3 months)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="one_time_payment_amount" name="one_time_payment_amount" 
                                               value="{{ old('one_time_payment_amount') }}" step="0.01" min="0">
                                    </div>
                                    <small class="form-text text-muted">Custom amount for one-time payment covering all 3 months (optional)</small>
                                </div>
                                
                                <div class="mb-3 p-3 bg-light rounded">
                                    <strong>Payment Options Preview:</strong>
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Monthly (3 payments):</span>
                                            <strong id="monthly-preview">$0.00/month</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>One-Time (3 months):</span>
                                            <strong class="text-success" id="onetime-preview">$0.00</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="monthly_sessions" class="form-label">Sessions per Month *</label>
                                    <input type="number" class="form-control" id="monthly_sessions" name="monthly_sessions" 
                                           value="{{ old('monthly_sessions') }}" min="1" required>
                                    <small class="form-text text-muted">Number of sessions/bookings included in monthly subscription</small>
                                </div>

                                <div class="mb-3">
                                    <label for="additional_booking_charge" class="form-label">Additional Booking Charge (60-min sessions)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="additional_booking_charge" name="additional_booking_charge" 
                                               value="{{ old('additional_booking_charge') }}" step="0.01" min="0">
                                    </div>
                                    <small class="form-text text-muted">Charge for additional 60-minute sessions beyond monthly limit</small>
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
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Agreement Template</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="agreement_template" class="form-label">Program Agreement PDF</label>
                                    <input type="file" class="form-control" id="agreement_template" name="agreement_template" 
                                           accept=".pdf">
                                    <small class="form-text text-muted">
                                        Upload a PDF agreement template specific to this program. If not provided, default template will be used.
                                    </small>
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

    // Initialize payment options preview
    updatePaymentOptions();
});

// Update payment options preview
function updatePaymentOptions() {
    const monthlyPrice = parseFloat(document.getElementById('monthly_price').value) || 0;
    const oneTimeAmount = parseFloat(document.getElementById('one_time_payment_amount').value) || 0;
    
    document.getElementById('monthly-preview').textContent = '$' + monthlyPrice.toFixed(2) + '/month';
    document.getElementById('onetime-preview').textContent = oneTimeAmount > 0 ? '$' + oneTimeAmount.toFixed(2) : 'Not set';
}

// Update one-time preview when one-time amount changes
document.addEventListener('DOMContentLoaded', function() {
    const oneTimeInput = document.getElementById('one_time_payment_amount');
    if (oneTimeInput) {
        oneTimeInput.addEventListener('input', updatePaymentOptions);
    }
});
</script>
@endsection
