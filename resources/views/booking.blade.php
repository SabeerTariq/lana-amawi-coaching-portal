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
                        <h2 class="fw-bold text-dark mb-2">Professional Registration & Booking</h2>
                        <p class="text-muted">Join Lana Amawi's coaching portal for healthcare professionals</p>
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


                    <!-- Professional Registration & Booking Form -->
                    <form method="POST" action="{{ route('booking.store') }}">
                        @csrf
                        
                        <!-- Personal Information -->
                    <div class="mb-4">
                            <h5 class="text-primary mb-3">Personal Information</h5>
                            
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="{{ old('full_name') }}" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="2" 
                                          placeholder="Enter your full address" required>{{ old('address') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="{{ old('date_of_birth') }}" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                        <option value="prefer_not_to_say" {{ old('gender') == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="age" class="form-label">Age *</label>
                                <input type="number" class="form-control" id="age" name="age" 
                                       value="{{ old('age') }}" min="18" max="100" required>
                            </div>

                            <div class="mb-3">
                                <label for="languages_spoken" class="form-label">Languages Spoken *</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_english" name="languages_spoken[]" value="English" 
                                                   {{ in_array('English', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_english">English</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_arabic" name="languages_spoken[]" value="Arabic" 
                                                   {{ in_array('Arabic', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_arabic">Arabic</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_french" name="languages_spoken[]" value="French" 
                                                   {{ in_array('French', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_french">French</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_spanish" name="languages_spoken[]" value="Spanish" 
                                                   {{ in_array('Spanish', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_spanish">Spanish</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_other" name="languages_spoken[]" value="Other" 
                                                   {{ in_array('Other', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_other">Other</label>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                        <!-- Professional Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Professional Information</h5>
                            
                            <div class="mb-3">
                                <label for="institution_hospital" class="form-label">Institution/Hospital *</label>
                                <input type="text" class="form-control" id="institution_hospital" name="institution_hospital" 
                                       value="{{ old('institution_hospital') }}" placeholder="Enter your institution or hospital name" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="position" class="form-label">Position *</label>
                                    <input type="text" class="form-control" id="position" name="position" 
                                           value="{{ old('position') }}" placeholder="e.g., Doctor, Nurse, Administrator" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="position_as_of_date" class="form-label">Position as of Date *</label>
                                    <input type="date" class="form-control" id="position_as_of_date" name="position_as_of_date" 
                                           value="{{ old('position_as_of_date') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="specialty" class="form-label">Specialty *</label>
                                <input type="text" class="form-control" id="specialty" name="specialty" 
                                       value="{{ old('specialty') }}" placeholder="e.g., Cardiology, Pediatrics, Emergency Medicine" required>
                            </div>

                            <div class="mb-3">
                                <label for="graduation_date" class="form-label">Graduation Date (if applicable)</label>
                                <input type="date" class="form-control" id="graduation_date" name="graduation_date" 
                                       value="{{ old('graduation_date') }}">
                                <div class="form-text">Leave blank if not applicable</div>
                            </div>
                        </div>

                        <!-- Session Booking Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Session Booking</h5>

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
                            </div>

                        <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I accept the <a href="#" class="text-decoration-none">Terms and Conditions</a> *
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Register & Book Session
                            </button>
                        </div>
                    </form>


                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            Already have an account? 
                            <a href="{{ route('client.login') }}" class="text-decoration-none">Login here</a>
                        </p>
                        <p class="text-muted mb-0 mt-2">
                            New to the portal? 
                            <a href="{{ route('register') }}" class="text-decoration-none">Register as a healthcare professional</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Age calculation based on date of birth
    const dateOfBirthInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    
    if (dateOfBirthInput && ageInput) {
        dateOfBirthInput.addEventListener('change', function() {
            const birthDate = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            if (age >= 18 && age <= 100) {
                ageInput.value = age;
            }
        });
    }
    
    // Form validation
    const form = document.querySelector('form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Language validation - ensure at least one language is selected
            const checkedLanguages = document.querySelectorAll('input[name="languages_spoken[]"]:checked');
            if (checkedLanguages.length === 0) {
                e.preventDefault();
                alert('Please select at least one language you speak.');
                return false;
            }
            
            // Basic validation - let HTML5 validation handle most of it
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
});
</script>
@endsection 