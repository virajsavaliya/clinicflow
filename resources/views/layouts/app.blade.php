<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ClinicFlow')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/crm.css') }}?v={{ time() }}">
    @yield('styles')
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <a class="brand-logo" href="/">
            @if(file_exists(public_path('assets/images/logo.png')))
                <img src="{{ asset('assets/images/logo.png') }}" alt="ClinicFlow">
            @else
                <i class="bi bi-heart-pulse-fill"></i>
                <span>ClinicFlow</span>
            @endif
        </a>
        
        @auth
            <!-- Main Navigation -->
            <nav class="main-nav">
                @if(auth()->user()->role === 'admin')
                    <a class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('admin.doctors*') ? 'active' : '' }}" href="{{ route('admin.doctors') }}">
                        <i class="bi bi-person-badge"></i>
                        <span>Doctors</span>
                    </a>
                @elseif(auth()->user()->role === 'doctor')
                    <a class="nav-item {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('doctor.appointments*') ? 'active' : '' }}" href="{{ route('doctor.appointments') }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('doctor.patients*') ? 'active' : '' }}" href="{{ route('doctor.patients') }}">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Patients</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('doctor.reports*') ? 'active' : '' }}" href="{{ route('doctor.reports') }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Reports</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('doctor.medicines*') || request()->routeIs('doctor.inventory*') ? 'active' : '' }}" href="{{ route('doctor.medicines') }}">
                        <i class="bi bi-capsule"></i>
                        <span>Medicines</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('doctor.receptions*') ? 'active' : '' }}" href="{{ route('doctor.receptions') }}">
                        <i class="bi bi-people"></i>
                        <span>Staff</span>
                    </a>
                @elseif(auth()->user()->role === 'reception')
                    <a class="nav-item {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}" href="{{ route('reception.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('reception.patients') ? 'active' : '' }}" href="{{ route('reception.patients') }}">
                        <i class="bi bi-person-plus"></i>
                        <span>Patients</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('reception.appointments*') ? 'active' : '' }}" href="{{ route('reception.appointments') }}">
                        <i class="bi bi-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('reception.pharmacy') ? 'active' : '' }}" href="{{ route('reception.pharmacy') }}">
                        <i class="bi bi-capsule"></i>
                        <span>Pharmacy</span>
                    </a>
                @endif
            </nav>
            
            <!-- User Section -->
            <div class="user-section">
                <div class="dropdown">
                    <button class="user-button" type="button" data-bs-toggle="dropdown">
                        <div class="user-profile-card">
                            <div class="user-avatar-large">
                                <span class="avatar-initials">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <span class="avatar-status online"></span>
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name }}</div>
                                <div class="user-role-badge">{{ ucfirst(auth()->user()->role) }}</div>
                            </div>
                            <i class="bi bi-chevron-down" style="margin-left: auto; font-size: 0.875rem; opacity: 0.7;"></i>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-circle"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @else
            <div class="user-section">
                <a href="{{ route('login') }}" class="user-button">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Login
                </a>
            </div>
        @endauth
    </header>

    <!-- Sidebar (CRM-style) -->
    @auth
    <aside class="sidebar">
        <nav class="nav-list">
            @if(auth()->user()->role === 'admin')
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.doctors*') ? 'active' : '' }}" href="{{ route('admin.doctors') }}">
                    <i class="bi bi-person-badge"></i>
                    <span>Doctors</span>
                </a>
            @elseif(auth()->user()->role === 'doctor')
                <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link {{ request()->routeIs('doctor.appointments*') ? 'active' : '' }}" href="{{ route('doctor.appointments') }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Appointments</span>
                </a>
                <a class="nav-link {{ request()->routeIs('doctor.patients*') ? 'active' : '' }}" href="{{ route('doctor.patients') }}">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Patients</span>
                </a>
                <a class="nav-link {{ request()->routeIs('doctor.reports*') ? 'active' : '' }}" href="{{ route('doctor.reports') }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Reports</span>
                </a>
                <a class="nav-link {{ request()->routeIs('doctor.medicines*') || request()->routeIs('doctor.inventory*') ? 'active' : '' }}" href="{{ route('doctor.medicines') }}">
                    <i class="bi bi-capsule"></i>
                    <span>Medicines</span>
                </a>
                <a class="nav-link {{ request()->routeIs('doctor.receptions*') ? 'active' : '' }}" href="{{ route('doctor.receptions') }}">
                    <i class="bi bi-people"></i>
                    <span>Staff</span>
                </a>
            @elseif(auth()->user()->role === 'reception')
                <a class="nav-link {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}" href="{{ route('reception.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link {{ request()->routeIs('reception.patients') ? 'active' : '' }}" href="{{ route('reception.patients') }}">
                    <i class="bi bi-person-plus"></i>
                    <span>Patients</span>
                </a>
                <a class="nav-link {{ request()->routeIs('reception.appointments*') ? 'active' : '' }}" href="{{ route('reception.appointments') }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Appointments</span>
                </a>
                <a class="nav-link {{ request()->routeIs('reception.pharmacy') ? 'active' : '' }}" href="{{ route('reception.pharmacy') }}">
                    <i class="bi bi-capsule"></i>
                    <span>Pharmacy</span>
                </a>
            @endif
        </nav>
    </aside>
    @endauth

    <div class="app-layout">
        <div class="main-content">
            <!-- Main Content -->
            <main class="content-wrapper">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.1/dist/axios.min.js"></script>
    <script type="application/json" id="app-flash-data">
        {!! json_encode([
            'success' => session('success'),
            'error' => session('error'),
            'validation' => $errors->any() ? $errors->first() : null
        ]) !!}
    </script>
    <script src="{{ asset('js/app-base.js') }}?v={{ time() }}"></script>
    @yield('scripts')
</body>
</html>
