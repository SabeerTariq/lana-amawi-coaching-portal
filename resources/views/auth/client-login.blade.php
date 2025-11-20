@extends('layouts.app')

@section('title', 'Client Login - Lana Amawi Coaching')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Lana Amawi Coaching" class="mb-3" style="max-width: 200px; height: auto;">
                        @if(session('success'))
                            <h2 class="fw-bold text-success mb-2">🎉 Booking Complete!</h2>
                            <p class="text-success mb-2">Your coaching session has been scheduled successfully</p>
                        @else
                            <h2 class="fw-bold text-dark mb-2">Welcome Back</h2>
                            <p class="text-muted">Sign in to your coaching portal</p>
                        @endif
                    </div>

                    @if(session('success') && session('email_check'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @elseif(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('email_check'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-envelope me-2"></i>
                            {{ session('email_check') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
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

                    <form method="POST" action="{{ route('client.login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ $email ?? old('email') }}" required autofocus>
                            @if($email)
                                <div class="form-text text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Welcome back! Your email has been pre-filled from your recent booking
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Client Sign In
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                Forgot your password?
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                                         <div class="text-center">
                         <p class="text-muted mb-2">
                             Don't have an account? 
                             <a href="{{ route('booking') }}" class="text-decoration-none">Register here</a>
                         </p>
                         <a href="{{ route('booking') }}" class="btn btn-outline-secondary btn-sm">
                             <i class="fas fa-arrow-left me-2"></i>Back to Registration Form
                         </a>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 