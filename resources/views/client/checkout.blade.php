@extends('layouts.client')

@section('title', 'Checkout')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Checkout</h1>
    <a href="{{ route('client.programs.payment-selection', $userProgram) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
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

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>Payment Information
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('client.programs.checkout.submit', $userProgram) }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="payment_type" value="{{ $paymentType }}">
                    <input type="hidden" name="payment_intent_id" id="payment_intent_id" value="">
                    <input type="hidden" name="subscription_id" id="subscription_id" value="">

                    <!-- Package Details -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-box me-2"></i>Package Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h5 class="mb-1">{{ $userProgram->program->name }}</h5>
                                <span class="badge bg-info">{{ $userProgram->program->formatted_subscription_type }}</span>
                            </div>
                            
                            @if($userProgram->program->description)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Description</h6>
                                <p class="mb-0">{{ $userProgram->program->description }}</p>
                            </div>
                            @endif

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Contract Duration</h6>
                                    <p class="mb-0"><strong>3 months</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Sessions per Month</h6>
                                    <p class="mb-0"><strong>{{ $userProgram->program->monthly_sessions ?? 0 }} sessions</strong></p>
                                </div>
                            </div>

                            @if($userProgram->program->features && count($userProgram->program->features) > 0)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Package Features</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($userProgram->program->features as $feature)
                                        <li class="mb-1">
                                            <i class="fas fa-check-circle text-success me-2"></i>{{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if($userProgram->program->additional_booking_charge)
                            <div class="alert alert-warning mb-0">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Additional Sessions:</strong> 
                                    Additional 60-minute sessions beyond the monthly limit can be booked for 
                                    <strong>${{ number_format($userProgram->program->additional_booking_charge, 0) }}</strong> per session.
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-3"><strong><i class="fas fa-receipt me-2"></i>Payment Summary</strong></h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Package:</span>
                            <strong>{{ $userProgram->program->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Package Type:</span>
                            <strong>{{ $userProgram->program->formatted_subscription_type }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Type:</span>
                            <strong>{{ $paymentType === 'monthly' ? 'Monthly Payments' : 'One-Time Payment' }}</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Amount:</span>
                            <strong class="h5 mb-0">
                                @if($paymentType === 'monthly')
                                    ${{ number_format($userProgram->program->monthly_price ?? 0, 0) }}/month
                                @else
                                    ${{ number_format($oneTimeAmount, 0) }}
                                @endif
                            </strong>
                        </div>
                        @if($paymentType === 'monthly')
                        <div class="d-flex justify-content-between">
                            <span>Total (3 months):</span>
                            <strong class="h5 mb-0">${{ number_format(($userProgram->program->monthly_price ?? 0) * 3, 0) }}</strong>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Payment Method</label>
                        <div class="alert alert-info">
                            <i class="fas fa-credit-card me-2"></i>
                            <strong>Credit/Debit Card</strong>
                        </div>
                    </div>

                    <!-- Stripe Elements Container -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Card Details *</label>
                        <div id="card-element" class="form-control" style="padding: 12px;">
                            <!-- Stripe Elements will create form elements here -->
                        </div>
                        <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                    </div>

                    <!-- Billing Address -->
                    <div class="mb-4">
                        <h6 class="mb-3">Billing Address</h6>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="billing_address" class="form-label">Street Address *</label>
                                <input type="text" class="form-control" id="billing_address" name="billing_address" 
                                       value="{{ old('billing_address', Auth::user()->address ?? '') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="billing_city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="billing_city" name="billing_city" 
                                       value="{{ old('billing_city', Auth::user()->city ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="billing_state" class="form-label">State/Province *</label>
                                <input type="text" class="form-control" id="billing_state" name="billing_state" 
                                       value="{{ old('billing_state') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="billing_postal_code" class="form-label">Postal Code *</label>
                                <input type="text" class="form-control" id="billing_postal_code" name="billing_postal_code" 
                                       value="{{ old('billing_postal_code', Auth::user()->postal_code ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="billing_country" class="form-label">Country *</label>
                                <select class="form-control" id="billing_country" name="billing_country" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $code => $name)
                                        <option value="{{ $code }}" {{ old('billing_country', Auth::user()->country ?? '') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select your country using the 2-letter ISO code (required by Stripe)</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1" required>
                        <label class="form-check-label" for="terms_accepted">
                            I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#" target="_blank">Privacy Policy</a> *
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="submit-button">
                            <i class="fas fa-lock me-2"></i>Complete Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="mb-1">{{ $userProgram->program->name }}</h6>
                    <span class="badge bg-info mb-2">{{ $userProgram->program->formatted_subscription_type }}</span>
                    @if($userProgram->program->description)
                    <p class="text-muted small mb-0 mt-2">{{ Str::limit($userProgram->program->description, 120) }}</p>
                    @endif
                </div>
                <hr>
                <div class="mb-3">
                    <h6 class="text-muted small mb-2">Package Information</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Contract Duration:</span>
                        <strong class="small">3 months</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Sessions/Month:</span>
                        <strong class="small">{{ $userProgram->program->monthly_sessions ?? 0 }} sessions</strong>
                    </div>
                    @if($userProgram->program->additional_booking_charge)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Additional Session:</span>
                        <strong class="small">${{ number_format($userProgram->program->additional_booking_charge, 0) }}</strong>
                    </div>
                    @endif
                </div>
                <hr>
                <div class="mb-3">
                    <h6 class="text-muted small mb-2">Payment Details</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Payment Type:</span>
                        <strong class="small">{{ $paymentType === 'monthly' ? 'Monthly' : 'One-Time' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Amount:</span>
                        <strong>
                            @if($paymentType === 'monthly')
                                ${{ number_format($userProgram->program->monthly_price ?? 0, 0) }}/mo
                            @else
                                ${{ number_format($oneTimeAmount, 0) }}
                            @endif
                        </strong>
                    </div>
                    @if($paymentType === 'monthly')
                    <div class="d-flex justify-content-between">
                        <span class="small">Total (3 months):</span>
                        <strong class="h6 mb-0">${{ number_format(($userProgram->program->monthly_price ?? 0) * 3, 0) }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($userProgram->program->features && count($userProgram->program->features) > 0)
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0"><i class="fas fa-star me-2"></i>Package Features</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($userProgram->program->features as $feature)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>{{ $feature }}</small>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stripe = Stripe('{{ $stripeKey }}');
    const elements = stripe.elements();
    
    // Create card element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a',
            },
        },
    });
    
    cardElement.mount('#card-element');
    
    // Handle real-time validation errors
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    
    // Handle form submission
    const form = document.getElementById('checkout-form');
    const submitButton = document.getElementById('submit-button');
    
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        // Disable submit button
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        
        // Get payment intent or subscription client secret from server
        try {
            const response = await fetch('{{ route("client.programs.checkout.create-payment-intent", $userProgram) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    payment_type: '{{ $paymentType }}',
                    amount: {{ $paymentType === 'monthly' ? ($userProgram->program->monthly_price ?? 0) : $oneTimeAmount }},
                }),
            });
            
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Confirm payment
            let result;
            if (data.payment_intent) {
                // One-time payment
                result = await stripe.confirmCardPayment(data.client_secret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: '{{ Auth::user()->name }}',
                            address: {
                                line1: document.getElementById('billing_address').value,
                                city: document.getElementById('billing_city').value,
                                state: document.getElementById('billing_state').value,
                                postal_code: document.getElementById('billing_postal_code').value,
                                country: document.getElementById('billing_country').value,
                            },
                        },
                    },
                });
                
                if (result.error) {
                    throw new Error(result.error.message);
                }
                
                // Verify payment succeeded
                if (result.paymentIntent.status !== 'succeeded') {
                    throw new Error('Payment was not successful. Please try again.');
                }
                
                // Set payment intent ID
                document.getElementById('payment_intent_id').value = result.paymentIntent.id;
            } else if (data.subscription) {
                // Monthly subscription
                result = await stripe.confirmCardPayment(data.client_secret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: '{{ Auth::user()->name }}',
                            address: {
                                line1: document.getElementById('billing_address').value,
                                city: document.getElementById('billing_city').value,
                                state: document.getElementById('billing_state').value,
                                postal_code: document.getElementById('billing_postal_code').value,
                                country: document.getElementById('billing_country').value,
                            },
                        },
                    },
                });
                
                if (result.error) {
                    throw new Error(result.error.message);
                }
                
                // Verify payment succeeded
                if (result.paymentIntent.status !== 'succeeded') {
                    throw new Error('Payment was not successful. Please try again.');
                }
                
                // Set subscription ID
                document.getElementById('subscription_id').value = data.subscription_id;
                document.getElementById('payment_intent_id').value = result.paymentIntent.id;
            }
            
            // Submit form via AJAX to handle redirect properly
            const formData = new FormData(form);
            
            const submitResponse = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });
            
            let responseData;
            try {
                responseData = await submitResponse.json();
            } catch (e) {
                // If response is not JSON, it might be a redirect or HTML
                if (submitResponse.redirected || submitResponse.status === 302) {
                    // Follow redirect
                    window.location.href = submitResponse.url || '{{ route("client.programs.checkout.success", $userProgram) }}';
                    return;
                }
                throw new Error('Payment processing failed. Please try again.');
            }
            
            if (submitResponse.ok && responseData.success) {
                // Redirect to success page
                window.location.href = responseData.redirect || '{{ route("client.programs.checkout.success", $userProgram) }}';
            } else {
                // Handle validation errors (422) and other errors
                let errorMessage = responseData.message || responseData.error || 'Payment processing failed. Please try again.';
                if (responseData.errors) {
                    const errorMessages = Object.values(responseData.errors).flat();
                    errorMessage = errorMessages.join(', ');
                }
                throw new Error(errorMessage);
            }
            
        } catch (error) {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-lock me-2"></i>Complete Payment';
            
            // Show error
            const displayError = document.getElementById('card-errors');
            displayError.textContent = error.message;
        }
    });
});
</script>

@endsection
