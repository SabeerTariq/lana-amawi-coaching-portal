@extends('layouts.app')

@section('title', 'Book Your Session - Lana Amawi Coaching')

@section('content')
<!-- Add CSRF token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Lana Amawi Coaching" class="mb-3" style="max-width: 200px; height: auto;">
                        <h2 class="fw-bold text-dark mb-2">Book Your Coaching Session</h2>
                        <p class="text-muted">Schedule your appointment with Lana Amawi</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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

                    <!-- Step Indicator -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="step-indicator active" id="step-1">
                                <div class="step-number">1</div>
                                <div class="step-label">Fill Form</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step-indicator" id="step-2">
                                <div class="step-number">2</div>
                                <div class="step-label">Download & Sign</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step-indicator" id="step-3">
                                <div class="step-number">3</div>
                                <div class="step-label">Upload & Submit</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Booking Form -->
                    <div id="step-1-content">
                        <form id="booking-form" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="{{ old('full_name') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number (Optional)</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone') }}">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="preferred_date" class="form-label">Preferred Date *</label>
                                    <input type="date" class="form-control" id="preferred_date" name="preferred_date" 
                                           value="{{ old('preferred_date') }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="preferred_time" class="form-label">Preferred Time *</label>
                                    <select class="form-select" id="preferred_time" name="preferred_time" required>
                                        <option value="">Select time...</option>
                                        <option value="09:00" {{ old('preferred_time') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                        <option value="10:00" {{ old('preferred_time') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                        <option value="11:00" {{ old('preferred_time') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                        <option value="12:00" {{ old('preferred_time') == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                                        <option value="13:00" {{ old('preferred_time') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                        <option value="14:00" {{ old('preferred_time') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                        <option value="15:00" {{ old('preferred_time') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                        <option value="16:00" {{ old('preferred_time') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                        <option value="17:00" {{ old('preferred_time') == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message / Notes</label>
                                <textarea class="form-control" id="message" name="message" rows="4" 
                                          placeholder="Tell us about your goals or any specific topics you'd like to discuss...">{{ old('message') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I accept the <a href="#" class="text-decoration-none">Terms and Conditions</a> *
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="button" class="btn btn-primary btn-lg" id="download-agreement-btn">
                                    <i class="fas fa-download me-2"></i>Download Agreement & Continue
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Agreement Download Instructions -->
                    <div id="step-2-content" style="display: none;">
                        <div class="text-center mb-4">
                            <i class="fas fa-file-pdf text-danger" style="font-size: 48px;"></i>
                            <h4 class="mt-3">Agreement Downloaded Successfully!</h4>
                            <p class="text-muted">Please follow these steps:</p>
                        </div>
                        
                        <div class="alert alert-info">
                            <ol class="mb-0">
                                <li><strong>Print</strong> the downloaded agreement</li>
                                <li><strong>Sign</strong> the agreement manually</li>
                                <li><strong>Scan</strong> or take a photo of the signed agreement</li>
                                <li><strong>Convert</strong> to PDF format</li>
                                <li>Click "Continue to Upload" when ready</li>
                            </ol>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" id="download-again-btn">
                                <i class="fas fa-download me-2"></i>Download Again
                            </button>
                            <button type="button" class="btn btn-primary" id="continue-upload-btn">
                                <i class="fas fa-upload me-2"></i>Continue to Upload
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Upload Signed Agreement -->
                    <div id="step-3-content" style="display: none;">
                        <div class="text-center mb-4">
                            <i class="fas fa-upload text-primary" style="font-size: 48px;"></i>
                            <h4 class="mt-3">Upload Your Signed Agreement</h4>
                            <p class="text-muted">Please upload the signed PDF agreement to complete your booking</p>
                        </div>

                        <form method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data" id="final-booking-form">
                            @csrf
                            
                            <!-- Hidden fields for form data -->
                            <input type="hidden" name="full_name" id="hidden_full_name">
                            <input type="hidden" name="email" id="hidden_email">
                            <input type="hidden" name="phone" id="hidden_phone">
                            <input type="hidden" name="preferred_date" id="hidden_preferred_date">
                            <input type="hidden" name="preferred_time" id="hidden_preferred_time">
                            <input type="hidden" name="message" id="hidden_message">
                            <input type="hidden" name="terms" value="1">

                            <div class="mb-4">
                                <label for="signed_agreement" class="form-label">Signed Agreement (PDF) *</label>
                                <input type="file" class="form-control" id="signed_agreement" name="signed_agreement" 
                                       accept=".pdf" required>
                                <div class="form-text">Please upload the signed agreement in PDF format (max 10MB)</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="back-to-form-btn">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Form
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check me-2"></i>Complete Booking
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            Already have an account? 
                            <a href="{{ route('client.login') }}" class="text-decoration-none">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #6c757d;
}

.step-indicator.active {
    color: #730623;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
}

.step-indicator.active .step-number {
    background-color: #730623;
    color: white;
}

.step-label {
    font-size: 12px;
    text-align: center;
    font-weight: 500;
}

.step-line {
    width: 60px;
    height: 2px;
    background-color: #e9ecef;
    margin: 20px 0;
}

.step-indicator.active + .step-line {
    background-color: #730623;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('booking-form');
    const downloadBtn = document.getElementById('download-agreement-btn');
    const downloadAgainBtn = document.getElementById('download-again-btn');
    const continueUploadBtn = document.getElementById('continue-upload-btn');
    const backToFormBtn = document.getElementById('back-to-form-btn');
    
    const step1Content = document.getElementById('step-1-content');
    const step2Content = document.getElementById('step-2-content');
    const step3Content = document.getElementById('step-3-content');
    
    const step1Indicator = document.getElementById('step-1');
    const step2Indicator = document.getElementById('step-2');
    const step3Indicator = document.getElementById('step-3');

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Download agreement
    downloadBtn.addEventListener('click', function() {
        if (form.checkValidity()) {
            // Store form data
            const formData = new FormData(form);
            
            // Download agreement
            fetch('{{ route("booking.agreement.download") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Network response was not ok');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'coaching_agreement.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                // Show step 2
                showStep(2);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error downloading agreement. Please try again.');
            });
        } else {
            form.reportValidity();
        }
    });

    // Download again
    downloadAgainBtn.addEventListener('click', function() {
        if (form.checkValidity()) {
            const formData = new FormData(form);
            
            fetch('{{ route("booking.agreement.download") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'coaching_agreement.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error downloading agreement. Please try again.');
            });
        } else {
            form.reportValidity();
        }
    });

    // Continue to upload
    continueUploadBtn.addEventListener('click', function() {
        // Transfer form data to hidden fields
        document.getElementById('hidden_full_name').value = document.getElementById('full_name').value;
        document.getElementById('hidden_email').value = document.getElementById('email').value;
        document.getElementById('hidden_phone').value = document.getElementById('phone').value;
        document.getElementById('hidden_preferred_date').value = document.getElementById('preferred_date').value;
        document.getElementById('hidden_preferred_time').value = document.getElementById('preferred_time').value;
        document.getElementById('hidden_message').value = document.getElementById('message').value;
        
        showStep(3);
    });

    // Back to form
    backToFormBtn.addEventListener('click', function() {
        showStep(1);
    });

    function showStep(step) {
        // Hide all content
        step1Content.style.display = 'none';
        step2Content.style.display = 'none';
        step3Content.style.display = 'none';
        
        // Remove active class from all indicators
        step1Indicator.classList.remove('active');
        step2Indicator.classList.remove('active');
        step3Indicator.classList.remove('active');
        
        // Show appropriate content and activate indicator
        switch(step) {
            case 1:
                step1Content.style.display = 'block';
                step1Indicator.classList.add('active');
                break;
            case 2:
                step2Content.style.display = 'block';
                step2Indicator.classList.add('active');
                break;
            case 3:
                step3Content.style.display = 'block';
                step3Indicator.classList.add('active');
                break;
        }
    }
});
</script>
@endsection 