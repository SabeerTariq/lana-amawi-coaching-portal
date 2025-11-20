@extends('layouts.app')

@section('title', 'Registration - Lana Amawi Coaching')

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
                        <h2 class="fw-bold text-dark mb-2">Registration</h2>
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
                            @if($errors->has('email'))
                                <div class="mt-3">
                                    <a href="{{ route('client.login') }}" class="btn btn-primary btn-sm">Go to Login Page</a>
                                </div>
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif


                    <!-- Professional Registration & Booking Form -->
                    <form method="POST" action="{{ route('booking.store') }}">
                        @csrf
                        
                        <!-- Personal Information -->
                    <div class="mb-4">
                            <h5 class="text-primary mb-3">Personal</h5>
                            
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
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone') }}" placeholder="Enter your phone number" required>
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
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_spanish" name="languages_spoken[]" value="Spanish" 
                                                   {{ in_array('Spanish', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_spanish">Spanish</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_mandarin" name="languages_spoken[]" value="Mandarin Chinese" 
                                                   {{ in_array('Mandarin Chinese', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_mandarin">Mandarin Chinese</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_german" name="languages_spoken[]" value="German" 
                                                   {{ in_array('German', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_german">German</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_japanese" name="languages_spoken[]" value="Japanese" 
                                                   {{ in_array('Japanese', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_japanese">Japanese</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="lang_vietnamese" name="languages_spoken[]" value="Vietnamese" 
                                                   {{ in_array('Vietnamese', old('languages_spoken', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="lang_vietnamese">Vietnamese</label>
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
                            <h5 class="text-primary mb-3">Professional</h5>
                            
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
                                <label for="education_institution" class="form-label">Education Institution *</label>
                                <input type="text" class="form-control" id="education_institution" name="education_institution" 
                                       value="{{ old('education_institution') }}" placeholder="e.g., Harvard Medical School, Johns Hopkins University" required>
                            </div>

                            <div class="mb-3">
                                <label for="graduation_date" class="form-label">Graduation Date (if applicable)</label>
                                <input type="date" class="form-control" id="graduation_date" name="graduation_date" 
                                       value="{{ old('graduation_date') }}">
                                <div class="form-text">Leave blank if not applicable</div>
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
                                <i class="fas fa-user-plus me-2"></i>Register Account
                            </button>
                        </div>
                    </form>


                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            Already have an account? 
                            <a href="{{ route('client.login') }}" class="text-decoration-none">Login here</a>
                        </p>
                        <p class="text-muted mb-0 mt-2">
                            After registration, you can book sessions from your dashboard.
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