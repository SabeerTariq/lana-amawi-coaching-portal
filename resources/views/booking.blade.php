@extends('layouts.app')

@section('title', 'Book Your Session - Lana Amawi Coaching')

@section('content')
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

                    <form method="POST" action="{{ route('booking.store') }}">
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
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-check me-2"></i>Book Now
                            </button>
                        </div>
                    </form>

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
@endsection 