<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Client - Lana Amawi Coaching')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .btn-primary {
            background-color: #730623;
            border-color: #730623;
        }
        
        .btn-primary:hover {
            background-color: #8a0a2a;
            border-color: #8a0a2a;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        
        .sidebar {
            background: #032a57;
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            width: 16.666667%; /* col-md-2 equivalent */
        }
        
        .sidebar .logo-section {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .sidebar .logo-section img {
            height: 127px;
            width: auto;
        }
        
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #730623;
            color: white;
        }
        
        .main-content {
            padding: 2rem;
            margin-left: 16.666667%; /* Offset for fixed sidebar */
        }
        
        .navbar-brand {
            font-weight: 600;
            color: #730623 !important;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 16.666667%; /* Start after sidebar */
            z-index: 1020;
            background: white;
        }
        
        .content-wrapper {
            margin-top: 80px; /* Space for fixed navbar */
        }
        
        /* Mobile responsive */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                width: 250px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .navbar {
                left: 0;
            }
            
            .sidebar-toggle {
                display: block !important;
            }
        }
        
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1040;
            background: #730623;
            border: none;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button (Mobile) -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <!-- Right side - User menu -->
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('client.profile') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="position-sticky d-flex flex-column" style="height: 100vh;">
                    <div class="logo-section">
                        <img src="{{ asset('images/logo.png') }}" alt="Lana Amawi Coaching">
                    </div>
                    <ul class="nav flex-column flex-grow-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}" 
                               href="{{ route('client.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.programs*') ? 'active' : '' }}" 
                               href="{{ route('client.programs') }}">
                                <i class="fas fa-graduation-cap me-2"></i>Programs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.appointments') ? 'active' : '' }}" 
                               href="{{ route('client.appointments') }}">
                                <i class="fas fa-calendar-alt me-2"></i>Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.messages') ? 'active' : '' }}" 
                               href="{{ route('client.messages') }}">
                                <i class="fas fa-comments me-2"></i>Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.subscriptions') ? 'active' : '' }}" 
                               href="{{ route('client.subscriptions') }}">
                                <i class="fas fa-credit-card me-2"></i>Subscriptions & Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.profile') ? 'active' : '' }}" 
                               href="{{ route('client.profile') }}">
                                <i class="fas fa-user-circle me-2"></i>Profile
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Logout at bottom -->
                    <div class="mt-auto pb-3">
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        @else
                            <a class="nav-link" href="{{ route('client.login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="content-wrapper">
                <main class="main-content">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside
                document.addEventListener('click', function(e) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html> 