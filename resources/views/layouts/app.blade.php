<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* User dropdown styling */
        .navbar-nav .dropdown-toggle {
            color: #fff !important;
        }
        
        .navbar-nav .dropdown-toggle::after {
            margin-left: 8px;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            min-width: 200px;
        }
        
        .dropdown-header {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 8px 16px 4px;
            margin-bottom: 4px;
        }
        
        .dropdown-item {
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }
        
        .dropdown-item.text-danger:hover {
            background-color: #f8d7da;
            color: #721c24 !important;
        }
        
        .fas.fa-user-circle {
            font-size: 1.1rem;
        }
        
        /* Welcome message styling */
        .navbar-nav .nav-link {
            font-weight: 500;
        }
        
        /* Success message styling */
        .alert-success {
            border-radius: 8px;
            border: none;
            background: linear-gradient(45deg, #d4e7d4, #c3e6c3);
            color: #155724;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Inventory APP</a>
        <div class="collapse navbar-collapse">
        @auth
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') || request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.list') }}">
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        Categories
                    </a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('server.*') ? 'active' : '' }}" href="{{ route('server.info') }}">
                            Server Info
                        </a>
                    </li>
            </ul>
            @endauth
            
            <!-- User Authentication Section -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <!-- User is logged in -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            Welcome, 
                            @if(Auth::user()->first_name && Auth::user()->last_name)
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            @elseif(Auth::user()->name)
                                {{ Auth::user()->name }}
                            @else
                                {{ Auth::user()->email }}
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="fas fa-user me-1"></i>
                                    Account
                                </h6>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user-edit me-2"></i>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i>
                                    Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- User is not logged in -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>
                            Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <h2 class="mb-4">@yield('title')</h2>

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
