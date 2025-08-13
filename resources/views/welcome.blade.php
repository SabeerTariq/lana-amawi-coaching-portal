<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lana Amawi Coaching - Transform Your Life</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: #730623 !important;
        }
        
        .btn-primary {
            background-color: #730623;
            border-color: #730623;
        }
        
        .btn-primary:hover {
            background-color: #8a0a2a;
            border-color: #8a0a2a;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #730623 0%, #8a0a2a 100%);
            color: white;
            padding: 100px 0;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .logo-large {
            max-width: 300px;
            height: auto;
        }
        
        .logo-navbar {
            height: 40px;
            width: auto;
        }
        
        .navbar {
            position: relative;
        }
        
        .navbar .logo-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .navbar .logo-center img {
            height: 60px;
            width: auto;
        }
        
        .navbar .logo-center .brand-text {
            margin-left: 15px;
            font-size: 1.5rem;
            font-weight: 600;
            color: #730623;
        }
            </style>
    </head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <!-- Left side - Navigation toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Center - Logo and Brand -->
            <div class="logo-center">
                <img src="{{ asset('images/logo.png') }}" alt="Lana Amawi Coaching">
                <span class="brand-text">Lana Amawi Coaching</span>
            </div>
            
            <!-- Right side - Navigation menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(Auth::user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                            </li>
                    @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('client.dashboard') }}">Client</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('booking') }}">Book Session</a>
                        </li>
                    @endauth
                    </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Lana Amawi Coaching" class="logo-large mb-4">
                    </div>
                    <h1 class="display-4 fw-bold mb-4">Transform Your Life with Professional Coaching</h1>
                    <p class="lead mb-4">Discover your potential and achieve your goals with personalized coaching sessions tailored to your unique journey.</p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('booking') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-calendar-plus me-2"></i>Book Your Session
                        </a>
                        @guest
                            <a href="{{ route('client.login') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Client Login
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-heart fa-8x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-dark mb-3">Our Coaching Services</h2>
                    <p class="lead text-muted">Discover how professional coaching can help you achieve your goals</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Life Coaching</h5>
                            <p class="card-text">Navigate life transitions and discover your true purpose with personalized guidance.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Career Coaching</h5>
                            <p class="card-text">Advance your career, find your passion, and achieve professional success.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Relationship Coaching</h5>
                            <p class="card-text">Build stronger connections and improve your relationships with others.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-leaf fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Wellness Coaching</h5>
                            <p class="card-text">Achieve balance and improve your overall physical and mental well-being.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-4">Ready to Start Your Transformation?</h3>
                    <p class="lead mb-4">Book your first coaching session today and take the first step towards achieving your goals.</p>
                    <a href="{{ route('booking') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-plus me-2"></i>Book Your Session Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Lana Amawi Coaching</h5>
                    <p class="mb-0">Transform your life with professional coaching services tailored to your unique journey.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>Contact</h5>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i>info@lana-amawi.com<br>
                        <i class="fas fa-phone me-2"></i>+1 (555) 123-4567
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} Lana Amawi Coaching. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
