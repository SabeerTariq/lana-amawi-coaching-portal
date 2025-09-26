@extends('layouts.app')

@section('title', 'Home - Lana Amawi Coaching Portal')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Welcome to Lana Amawi Coaching Portal</h1>
                    <p class="lead mb-4">A modern, responsive coaching platform built with Laravel, Bootstrap, and MySQL. Experience the power of professional coaching services.</p>
                    <div class="d-flex gap-3">
                        <a href="#about" class="btn btn-light btn-lg">Learn More</a>
                        <a href="#contact" class="btn btn-outline-light btn-lg">Get Started</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="bg-white bg-opacity-10 rounded p-4">
                        <i class="fas fa-laptop-code" style="font-size: 4rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="about">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Why Choose Our Platform?</h2>
                    <p class="lead text-muted">Built with modern technologies and best practices for optimal performance and user experience.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-rocket text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5 class="card-title">Fast & Responsive</h5>
                            <p class="card-text">Built with Laravel's powerful framework and Bootstrap's responsive design system for optimal performance across all devices.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-shield-alt text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5 class="card-title">Secure & Reliable</h5>
                            <p class="card-text">Laravel's built-in security features and MySQL database ensure your data is safe and your application is reliable.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-code text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5 class="card-title">Modern Development</h5>
                            <p class="card-text">Clean, maintainable code with modern PHP practices, elegant Blade templates, and powerful Eloquent ORM.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5 bg-light" id="services">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Our Services</h2>
                    <p class="lead text-muted">Comprehensive web development solutions tailored to your needs.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-palette text-primary"></i>
                                </div>
                                <h5 class="card-title mb-0">Web Design</h5>
                            </div>
                            <p class="card-text">Beautiful, responsive designs that work perfectly on all devices. We focus on user experience and modern design principles.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-cogs text-success"></i>
                                </div>
                                <h5 class="card-title mb-0">Web Development</h5>
                            </div>
                            <p class="card-text">Full-stack web development using Laravel, MySQL, and modern frontend technologies. Scalable and maintainable solutions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-mobile-alt text-warning"></i>
                                </div>
                                <h5 class="card-title mb-0">Mobile Responsive</h5>
                            </div>
                            <p class="card-text">Mobile-first approach ensuring your application looks and works great on smartphones, tablets, and desktops.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-database text-info"></i>
                                </div>
                                <h5 class="card-title mb-0">Database Management</h5>
                            </div>
                            <p class="card-text">Efficient database design and management with MySQL, ensuring data integrity and optimal performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Get In Touch</h2>
                    <p class="lead text-muted">Ready to start your next project? Let's discuss how we can help you achieve your goals.</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow">
                        <div class="card-body p-5">
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
                            
                            <form method="POST" action="{{ route('contact.store') }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" value="{{ old('firstName') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" value="{{ old('lastName') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg px-5">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 