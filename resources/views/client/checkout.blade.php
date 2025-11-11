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

                    <!-- Payment Summary -->
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-2"><strong>Payment Summary</strong></h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Program:</span>
                            <strong>{{ $userProgram->program->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Payment Type:</span>
                            <strong>{{ $paymentType === 'monthly' ? 'Monthly Payments' : 'One-Time Payment' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
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
                            <strong>${{ number_format(($userProgram->program->monthly_price ?? 0) * 3, 0) }}</strong>
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
                        <input type="hidden" name="payment_method" value="credit_card">
                    </div>

                    <!-- Credit Card Fields -->
                    <div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="card_holder_name" class="form-label">Card Holder Name *</label>
                                <input type="text" class="form-control" id="card_holder_name" name="card_holder_name" 
                                       value="{{ old('card_holder_name', Auth::user()->name) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="card_number" class="form-label">Card Number *</label>
                                <input type="text" class="form-control" id="card_number" name="card_number" 
                                       placeholder="1234 5678 9012 3456" maxlength="19" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="expiry_month" class="form-label">Expiry Month *</label>
                                <select class="form-select" id="expiry_month" name="expiry_month" required>
                                    <option value="">MM</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="expiry_year" class="form-label">Expiry Year *</label>
                                <select class="form-select" id="expiry_year" name="expiry_year" required>
                                    <option value="">YYYY</option>
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="cvv" class="form-label">CVV *</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" 
                                       placeholder="123" maxlength="4" required>
                            </div>
                        </div>
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
                                <input type="text" class="form-control" id="billing_country" name="billing_country" 
                                       value="{{ old('billing_country', Auth::user()->country ?? '') }}" required>
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
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-lock me-2"></i>Complete Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">Order Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>{{ $userProgram->program->name }}</strong>
                    <p class="text-muted small mb-0">{{ Str::limit($userProgram->program->description, 100) }}</p>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Contract Duration:</span>
                    <strong>3 months</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Sessions/Month:</span>
                    <strong>{{ $userProgram->program->monthly_sessions ?? 0 }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Payment Type:</span>
                    <strong>{{ $paymentType === 'monthly' ? 'Monthly' : 'One-Time' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Amount:</span>
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
                    <span>Total (3 months):</span>
                    <strong class="h5 mb-0">${{ number_format(($userProgram->program->monthly_price ?? 0) * 3, 0) }}</strong>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const creditCardFields = document.getElementById('credit-card-fields');
    const bankTransferFields = document.getElementById('bank-transfer-fields');

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'credit_card') {
                creditCardFields.style.display = 'block';
                bankTransferFields.style.display = 'none';
                // Make credit card fields required
                creditCardFields.querySelectorAll('input, select').forEach(field => {
                    field.required = true;
                });
                // Make bank transfer fields not required
                bankTransferFields.querySelectorAll('input').forEach(field => {
                    field.required = false;
                });
            } else {
                creditCardFields.style.display = 'none';
                bankTransferFields.style.display = 'block';
                // Make credit card fields not required
                creditCardFields.querySelectorAll('input, select').forEach(field => {
                    field.required = false;
                });
                // Make bank transfer fields not required (optional)
                bankTransferFields.querySelectorAll('input').forEach(field => {
                    field.required = false;
                });
            }
        });
    });

    // Format card number
    const cardNumber = document.getElementById('card_number');
    if (cardNumber) {
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    }

    // Format CVV
    const cvv = document.getElementById('cvv');
    if (cvv) {
        cvv.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }
});
</script>

@endsection

