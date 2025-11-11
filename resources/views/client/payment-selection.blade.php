@extends('layouts.client')

@section('title', 'Select Payment Method')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Select Payment Method</h1>
    <a href="{{ route('client.programs') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Programs
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>{{ $userProgram->program->name }} - Payment Selection
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Congratulations!</strong> Your application has been approved. Please select your preferred payment method to proceed.
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-3">Program Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Program:</strong> {{ $userProgram->program->name }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Sessions/Month:</strong> {{ $userProgram->program->monthly_sessions ?? 0 }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Contract Duration:</strong> 3 months
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Monthly Sessions:</strong> {{ $userProgram->program->monthly_sessions ?? 0 }} sessions/month
                        </div>
                    </div>
                </div>

                <form action="{{ route('client.programs.checkout', $userProgram) }}" method="GET" id="payment-selection-form">
                    <input type="hidden" name="payment_type" id="selected_payment_type" value="">

                    <h6 class="text-muted mb-3">Choose Payment Option</h6>
                    <div class="row g-3">
                        <!-- Monthly Payment Option -->
                        <div class="col-md-6">
                            <div class="card h-100 payment-option-card" data-payment-type="monthly">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Monthly Payments</h5>
                                    <div class="h3 text-primary mb-2 fw-bold">
                                        ${{ number_format($userProgram->program->monthly_price ?? 0, 0) }}/mo
                                    </div>
                                    <p class="text-muted small mb-3">3 monthly payments</p>
                                    <div class="text-start">
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-success me-2"></i>Pay monthly for 3 months</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Total: ${{ number_format(($userProgram->program->monthly_price ?? 0) * 3, 0) }}</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Flexible payment schedule</li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary w-100 select-payment-btn" data-type="monthly">
                                        Select Monthly
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- One-Time Payment Option -->
                        <div class="col-md-6">
                            <div class="card h-100 payment-option-card" data-payment-type="one_time">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">One-Time Payment</h5>
                                    <div class="h3 text-success mb-2 fw-bold">
                                        ${{ number_format($oneTimeAmount, 0) }}
                                    </div>
                                    <p class="text-muted small mb-3">Pay once for 3 months</p>
                                    <div class="text-start">
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-success me-2"></i>Pay upfront for full 3 months</li>
                                            <li><i class="fas fa-check text-success me-2"></i>No monthly payments</li>
                                            @if($userProgram->program->one_time_payment_amount)
                                                <li><i class="fas fa-check text-success me-2"></i>Special pricing</li>
                                            @endif
                                        </ul>
                                    </div>
                                    <button type="button" class="btn btn-outline-success w-100 select-payment-btn" data-type="one_time">
                                        Select One-Time
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-primary btn-lg" id="proceed-btn" disabled>
                            <i class="fas fa-arrow-right me-2"></i>Proceed to Checkout
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.payment-option-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e0e0e0;
}

.payment-option-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.payment-option-card.selected {
    border-color: #007bff;
    background-color: #f0f8ff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.payment-option-card.selected .select-payment-btn {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentCards = document.querySelectorAll('.payment-option-card');
    const selectButtons = document.querySelectorAll('.select-payment-btn');
    const proceedBtn = document.getElementById('proceed-btn');
    const hiddenInput = document.getElementById('selected_payment_type');
    const form = document.getElementById('payment-selection-form');

    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            paymentCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Get payment type from data attribute
            const paymentType = this.dataset.paymentType;
            hiddenInput.value = paymentType;
            
            // Enable proceed button
            proceedBtn.disabled = false;
        });
    });

    selectButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const paymentType = this.dataset.type;
            const card = this.closest('.payment-option-card');
            card.click();
        });
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if payment type is selected
        if (!hiddenInput.value) {
            alert('Please select a payment option first.');
            return;
        }
        
        // Submit the form
        this.submit();
    });
});
</script>
@endsection

