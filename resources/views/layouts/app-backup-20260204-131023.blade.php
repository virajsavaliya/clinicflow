<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ClinicFlow')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --color-black: #1a1a1a;
            --color-gray-800: #262626;
            --color-gray-700: #404040;
            --color-gray-600: #525252;
            --color-gray-500: #737373;
            --color-gray-400: #a3a3a3;
            --color-gray-300: #d4d4d4;
            --color-gray-200: #e5e5e5;
            --color-gray-100: #f5f5f5;
            --color-gray-50: #fafafa;
            --color-white: #ffffff;
            --color-success: #22c55e;
            --color-danger: #ef4444;
            --color-warning: #f59e0b;
            --color-info: #3b82f6;
            --header-height: 56px;
            --radius-sm: 4px;
            --radius-md: 6px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --transition-fast: 0.15s ease;
            --transition-normal: 0.2s ease;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif; background: var(--color-gray-100); font-size: 14px; line-height: 1.5; }
        
        /* Header */
        .top-header { background: linear-gradient(135deg, var(--color-black) 0%, #2d2d2d 100%); height: var(--header-height); position: fixed; top: 0; left: 0; right: 0; z-index: 1000; display: flex; align-items: center; padding: 0 1rem; gap: 0.5rem; box-shadow: var(--shadow-md); }
        .brand { color: var(--color-white); font-size: 1.125rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: opacity var(--transition-fast); }
        .brand:hover { color: var(--color-white); opacity: 0.9; }
        .brand-logo { height: 70px; width: auto; object-fit: contain; }
        .top-nav { display: flex; align-items: center; gap: 0.25rem; margin-left: 1.5rem; }
        .top-nav .nav-link { color: var(--color-gray-400); padding: 0.5rem 0.75rem; border-radius: var(--radius-md); font-weight: 500; font-size: 0.8125rem; text-decoration: none; display: flex; align-items: center; gap: 0.375rem; transition: all var(--transition-fast); }
        .top-nav .nav-link:hover { color: var(--color-white); background: rgba(255,255,255,0.1); }
        .top-nav .nav-link.active { color: var(--color-white); background: rgba(255,255,255,0.15); }
        .top-nav .nav-link i { font-size: 1rem; }
        .user-section { margin-left: auto; }
        .user-btn { background: rgba(255,255,255,0.1); border: none; color: var(--color-white); padding: 0.4rem 0.75rem; border-radius: var(--radius-md); font-size: 0.8125rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: background var(--transition-fast); }
        .user-btn:hover { background: rgba(255,255,255,0.2); }
        .role-badge { background: var(--color-white); color: var(--color-black); padding: 0.125rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.625rem; font-weight: 600; text-transform: uppercase; }
        
        /* Content Area */
        .content-area { margin-top: var(--header-height); padding: 1.25rem; height: calc(100vh - var(--header-height)); overflow-y: auto; overflow-x: hidden; }
        
        /* Cards */
        .card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); transition: box-shadow var(--transition-normal), transform var(--transition-normal); }
        .card:hover { box-shadow: var(--shadow-md); }
        .card-header { background: var(--color-white); border-bottom: 1px solid var(--color-gray-200); padding: 0.875rem 1rem; font-weight: 600; border-radius: var(--radius-lg) var(--radius-lg) 0 0; }
        .card-body { padding: 1rem; }
        
        /* Buttons */
        .btn { padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 600; font-size: 0.8125rem; border: none; transition: all var(--transition-fast); cursor: pointer; }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.75rem; }
        .btn-lg { padding: 0.625rem 1.25rem; font-size: 0.875rem; }
        .btn-primary, .btn-dark { background: linear-gradient(135deg, var(--color-black) 0%, #333 100%); color: var(--color-white); box-shadow: var(--shadow-sm); }
        .btn-primary:hover, .btn-dark:hover { background: linear-gradient(135deg, #333 0%, #444 100%); box-shadow: var(--shadow-md); }
        .btn-success { background: linear-gradient(135deg, var(--color-success) 0%, #16a34a 100%); color: white; }
        .btn-success:hover { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); }
        .btn-danger { background: linear-gradient(135deg, var(--color-danger) 0%, #dc2626 100%); color: white; }
        .btn-danger:hover { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); }
        .btn-warning { background: linear-gradient(135deg, var(--color-warning) 0%, #d97706 100%); color: white; }
        .btn-warning:hover { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }
        .btn-info { background: linear-gradient(135deg, var(--color-info) 0%, #2563eb 100%); color: white; }
        .btn-info:hover { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
        .btn-outline-secondary { background: var(--color-white); border: 1px solid var(--color-gray-300); color: var(--color-gray-700); border-radius: var(--radius-md); }
        .btn-outline-secondary:hover { background: var(--color-gray-50); border-color: var(--color-gray-400); }
        .btn-outline-primary { background: var(--color-white); border: 1px solid var(--color-info); color: var(--color-info); border-radius: var(--radius-md); }
        .btn-outline-primary:hover { background: var(--color-info); color: white; }
        .btn-outline-danger { background: var(--color-white); border: 1px solid var(--color-danger); color: var(--color-danger); border-radius: var(--radius-md); }
        .btn-outline-danger:hover { background: var(--color-danger); color: white; }
        .btn-outline-light { background: transparent; border: 1px solid rgba(255,255,255,0.3); color: var(--color-white); border-radius: var(--radius-md); }
        .btn-outline-light:hover { background: rgba(255,255,255,0.1); color: var(--color-white); }
        .btn-secondary { background: var(--color-gray-500); color: white; }
        .btn-secondary:hover { background: var(--color-gray-600); }
        .btn-light { background: var(--color-gray-100); color: var(--color-gray-700); border: 1px solid var(--color-gray-200); }
        .btn-light:hover { background: var(--color-gray-200); }
        
        /* Forms */
        .form-control, .form-select { padding: 0.5rem 0.75rem; border: 1px solid var(--color-gray-300); border-radius: var(--radius-md); font-size: 0.8125rem; transition: border-color var(--transition-fast), box-shadow var(--transition-fast); }
        .form-control:focus, .form-select:focus { border-color: var(--color-info); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); outline: none; }
        .form-label { font-size: 0.75rem; font-weight: 600; color: var(--color-gray-700); margin-bottom: 0.375rem; }
        .input-group-text { border-radius: var(--radius-md) 0 0 var(--radius-md); border: 1px solid var(--color-gray-300); background: var(--color-gray-50); }
        .input-group .form-control { border-radius: 0 var(--radius-md) var(--radius-md) 0; }
        
        /* Tables */
        .table { margin-bottom: 0; }
        .table thead th { background: var(--color-gray-50); font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; padding: 0.75rem 1rem; border-bottom: 2px solid var(--color-gray-200); }
        .table tbody td { padding: 0.875rem 1rem; border-bottom: 1px solid var(--color-gray-100); font-size: 0.8125rem; vertical-align: middle; }
        .table tbody tr { transition: background var(--transition-fast); }
        .table tbody tr:hover { background: var(--color-gray-50); }
        .table-responsive { border-radius: var(--radius-lg); overflow: hidden; }
        
        /* Alerts */
        .alert { padding: 0.875rem 1rem; border-radius: var(--radius-md); border: none; margin-bottom: 1rem; font-size: 0.8125rem; display: flex; align-items: center; gap: 0.5rem; }
        .alert-success { background: linear-gradient(135deg, #dcfce7 0%, #d1fae5 100%); color: #166534; border-left: 4px solid var(--color-success); }
        .alert-danger { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border-left: 4px solid var(--color-danger); }
        .alert-warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border-left: 4px solid var(--color-warning); }
        .alert-info { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border-left: 4px solid var(--color-info); }
        
        /* Dropdowns */
        .dropdown-menu { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 0.5rem; margin-top: 0.25rem; }
        .dropdown-item { padding: 0.5rem 0.75rem; border-radius: var(--radius-md); font-size: 0.8125rem; color: var(--color-gray-700); transition: all var(--transition-fast); }
        .dropdown-item:hover { background: var(--color-gray-100); color: var(--color-black); }
        .dropdown-divider { margin: 0.375rem 0; border-color: var(--color-gray-200); }
        
        /* Badges */
        .badge { padding: 0.3rem 0.6rem; border-radius: var(--radius-sm); font-weight: 600; font-size: 0.6875rem; }
        .badge.bg-success { background: linear-gradient(135deg, var(--color-success) 0%, #16a34a 100%) !important; }
        .badge.bg-danger { background: linear-gradient(135deg, var(--color-danger) 0%, #dc2626 100%) !important; }
        .badge.bg-warning { background: linear-gradient(135deg, var(--color-warning) 0%, #d97706 100%) !important; color: white !important; }
        .badge.bg-info { background: linear-gradient(135deg, var(--color-info) 0%, #2563eb 100%) !important; }
        .badge.bg-secondary { background: linear-gradient(135deg, var(--color-gray-500) 0%, var(--color-gray-600) 100%) !important; }
        
        /* Modals */
        .modal-content { border: none; border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); overflow: hidden; }
        .modal-header { border-bottom: 1px solid var(--color-gray-200); padding: 1rem 1.25rem; }
        .modal-body { padding: 1.25rem; }
        .modal-footer { border-top: 1px solid var(--color-gray-200); padding: 1rem 1.25rem; }
        .modal-backdrop.show { opacity: 0.5; }
        .btn-close { border-radius: var(--radius-sm); transition: opacity var(--transition-fast); }
        
        /* Stat Cards (common pattern) */
        .stat-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm); transition: all var(--transition-normal); }
        .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .stat-icon { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--color-black); line-height: 1.2; }
        .stat-label { font-size: 0.75rem; color: var(--color-gray-500); text-transform: uppercase; letter-spacing: 0.025em; font-weight: 600; }
        
        /* Action Cards */
        .actions-card { background: var(--color-white); border: 1px solid var(--color-gray-200); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); }
        .actions-card .header { padding: 0.875rem 1rem; font-weight: 600; font-size: 0.875rem; border-bottom: 1px solid var(--color-gray-200); display: flex; align-items: center; gap: 0.5rem; background: var(--color-gray-50); }
        .action-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; text-decoration: none; color: var(--color-gray-700); border-bottom: 1px solid var(--color-gray-100); transition: all var(--transition-fast); }
        .action-link:hover { background: var(--color-gray-50); color: var(--color-black); padding-left: 1.25rem; }
        .action-link:last-child { border-bottom: none; }
        .action-link .icon { width: 36px; height: 36px; background: var(--color-gray-100); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1rem; transition: all var(--transition-fast); }
        .action-link:hover .icon { background: var(--color-black); color: var(--color-white); }
        .action-link h6 { margin: 0; font-size: 0.875rem; font-weight: 600; }
        .action-link small { color: var(--color-gray-500); font-size: 0.75rem; }
        
        /* Pagination */
        .pagination { gap: 0.25rem; }
        .page-link { border-radius: var(--radius-md); border: 1px solid var(--color-gray-200); color: var(--color-gray-700); padding: 0.4rem 0.75rem; font-size: 0.8125rem; transition: all var(--transition-fast); }
        .page-link:hover { background: var(--color-gray-100); color: var(--color-black); }
        .page-item.active .page-link { background: var(--color-black); border-color: var(--color-black); color: white; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--color-gray-100); border-radius: var(--radius-sm); }
        ::-webkit-scrollbar-thumb { background: var(--color-gray-300); border-radius: var(--radius-sm); }
        ::-webkit-scrollbar-thumb:hover { background: var(--color-gray-400); }
        
        /* Responsive */
        @media (max-width: 768px) { 
            .top-nav .nav-link span { display: none; } 
            .user-btn .user-name { display: none; } 
            .content-area { padding: 1rem; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="top-header">
        <a class="brand" href="/">
            <img src="{{ asset('assets/images/logo.png') }}?v={{ time() }}" alt="ClinicFlow Logo" class="brand-logo">
        </a>
        @auth
            <nav class="top-nav">
                @if(auth()->user()->isAdmin())
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                    <a class="nav-link {{ request()->routeIs('admin.doctors*') ? 'active' : '' }}" href="{{ route('admin.doctors') }}"><i class="bi bi-person-badge"></i><span>Doctors</span></a>
                @elseif(auth()->user()->isDoctor())
                    <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                    <a class="nav-link {{ request()->routeIs('doctor.appointments*') ? 'active' : '' }}" href="{{ route('doctor.appointments') }}"><i class="bi bi-calendar-check"></i><span>Appointments</span></a>
                    <a class="nav-link {{ request()->routeIs('doctor.patients*') ? 'active' : '' }}" href="{{ route('doctor.patients') }}"><i class="bi bi-person-lines-fill"></i><span>Patients</span></a>
                    <a class="nav-link {{ request()->routeIs('doctor.reports*') ? 'active' : '' }}" href="{{ route('doctor.reports') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
                    <a class="nav-link {{ request()->routeIs('doctor.medicines*') || request()->routeIs('doctor.inventory*') ? 'active' : '' }}" href="{{ route('doctor.medicines') }}"><i class="bi bi-capsule"></i><span>Medicines</span></a>
                    <a class="nav-link {{ request()->routeIs('doctor.receptions*') ? 'active' : '' }}" href="{{ route('doctor.receptions') }}"><i class="bi bi-people"></i><span>Staff</span></a>
                @elseif(auth()->user()->isReception())
                    <a class="nav-link {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}" href="{{ route('reception.dashboard') }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                    <a class="nav-link {{ request()->routeIs('reception.patients') ? 'active' : '' }}" href="{{ route('reception.patients') }}"><i class="bi bi-person-plus"></i><span>Patients</span></a>
                    <a class="nav-link {{ request()->routeIs('reception.appointments*') ? 'active' : '' }}" href="{{ route('reception.appointments') }}"><i class="bi bi-calendar-check"></i><span>Appointments</span></a>
                    <a class="nav-link {{ request()->routeIs('reception.pharmacy') ? 'active' : '' }}" href="{{ route('reception.pharmacy') }}"><i class="bi bi-capsule"></i><span>Pharmacy</span></a>
                @endif
            </nav>
            <div class="user-section">
                <div class="dropdown">
                    <button class="user-btn" type="button" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i><span class="user-name">{{ auth()->user()->name }}</span><span class="role-badge">{{ auth()->user()->role }}</span><i class="bi bi-chevron-down" style="font-size:0.6rem;opacity:0.7;"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button></form></li>
                    </ul>
                </div>
            </div>
        @else
            <div class="user-section"><a href="{{ route('login') }}" class="user-btn"><i class="bi bi-box-arrow-in-right"></i> Login</a></div>
        @endauth
    </header>
    <main class="content-area">
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.1/dist/axios.min.js"></script>
    <script>
        // Configure axios to include CSRF token on all requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            // Also set for specific request methods
            axios.defaults.headers.get['X-CSRF-TOKEN'] = csrfToken;
            axios.defaults.headers.post['X-CSRF-TOKEN'] = csrfToken;
            axios.defaults.headers.put['X-CSRF-TOKEN'] = csrfToken;
            axios.defaults.headers.delete['X-CSRF-TOKEN'] = csrfToken;
        }

        // CSRF Token Auto-Refresh - Prevents 419 errors on long sessions
        (function() {
            const CSRF_REFRESH_INTERVAL = 15 * 60 * 1000; // 15 minutes
            
            async function refreshCsrfToken() {
                try {
                    const response = await axios.get('/refresh-csrf');
                    const newToken = response.data.csrf_token;
                    
                    if (newToken) {
                        // Update meta tag
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            metaTag.setAttribute('content', newToken);
                        }
                        
                        // Update axios defaults
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                        axios.defaults.headers.get['X-CSRF-TOKEN'] = newToken;
                        axios.defaults.headers.post['X-CSRF-TOKEN'] = newToken;
                        axios.defaults.headers.put['X-CSRF-TOKEN'] = newToken;
                        axios.defaults.headers.delete['X-CSRF-TOKEN'] = newToken;
                        
                        // Update all hidden CSRF input fields in forms
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = newToken;
                        });
                        
                        console.log('CSRF token refreshed successfully');
                    }
                } catch (error) {
                    console.warn('CSRF token refresh failed:', error.message);
                }
            }
            
            // Refresh token every 15 minutes
            setInterval(refreshCsrfToken, CSRF_REFRESH_INTERVAL);
            
            // Also refresh on visibility change (user comes back to tab)
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    refreshCsrfToken();
                }
            });
        })();

        // Global Notification System - Shows alerts on any screen
        (function() {
            const CHECK_INTERVAL = 3000; // Check every 3 seconds
            const notificationContainer = document.createElement('div');
            notificationContainer.id = 'global-notification-container';
            notificationContainer.style.cssText = `
                position: fixed;
                top: 1rem;
                right: 1rem;
                z-index: 9999;
                max-width: 400px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            `;
            document.body.appendChild(notificationContainer);

            // Track which notifications we've already shown
            const shownNotifications = new Set();

            // Request notification permission from browser
            if ('Notification' in window && Notification.permission === 'default') {
                // Request with a slight delay to avoid blocking UI
                setTimeout(() => {
                    Notification.requestPermission().then(permission => {
                        console.log('Notification permission:', permission);
                    });
                }, 1000);
            }

            window.showGlobalNotification = function(message, type = 'info', duration = 4000) {
                const notification = document.createElement('div');
                const bgColor = {
                    success: '#10b981',
                    error: '#ef4444',
                    warning: '#f59e0b',
                    info: '#3b82f6'
                }[type] || '#3b82f6';

                const iconHTML = {
                    success: '<span style="font-size: 1.2rem;">✓</span>',
                    error: '<span style="font-size: 1.2rem;">✕</span>',
                    warning: '<span style="font-size: 1.2rem;">⚠</span>',
                    info: '<span style="font-size: 1.2rem;">ℹ</span>'
                }[type] || '<span style="font-size: 1.2rem;">ℹ</span>';

                notification.style.cssText = `
                    background: ${bgColor};
                    color: white;
                    padding: 1rem 1.5rem;
                    border-radius: 6px;
                    margin-bottom: 0.5rem;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    animation: slideInRight 0.3s ease;
                    font-weight: 500;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 1rem;
                `;
                
                notification.innerHTML = `
                    <span style="display: flex; align-items: center; gap: 0.75rem;">
                        ${iconHTML}
                        <span>${message}</span>
                    </span>
                    <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; padding: 0;">&times;</button>
                `;
                
                notificationContainer.appendChild(notification);
                
                // Also show browser system notification if permission granted
                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        const iconMap = {
                            success: '✓',
                            error: '✕',
                            warning: '⚠',
                            info: 'ℹ'
                        };
                        new Notification('ClinicFlow Alert', {
                            body: message.replace(/<[^>]*>/g, ''), // Strip HTML
                            icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect fill="' + encodeURIComponent(bgColor) + '" width="100" height="100"/><text x="50" y="65" font-size="50" text-anchor="middle" fill="white" font-weight="bold">' + iconMap[type] + '</text></svg>',
                            tag: 'clinicflow-' + Date.now(),
                            requireInteraction: false
                        });
                    } catch (e) {
                        console.log('Browser notification error:', e);
                    }
                }
                
                if (duration > 0) {
                    setTimeout(() => notification.remove(), duration);
                }
            };

            // Fetch unread notifications periodically
            async function checkForNotifications() {
                if (!document.hidden) { // Only check if tab is visible
                    try {
                        const response = await axios.get('/api/notifications/unread');
                        if (response.data.success && response.data.notifications.length > 0) {
                            response.data.notifications.forEach(notif => {
                                // Only show if we haven't shown this notification before
                                if (!shownNotifications.has(notif.id)) {
                                    shownNotifications.add(notif.id);
                                    
                                    const type = notif.type.includes('error') || notif.type.includes('critical') ? 'error' : 
                                                notif.type.includes('success') ? 'success' :
                                                notif.type.includes('warning') ? 'warning' : 'info';
                                    const message = notif.message || notif.title || 'New notification';
                                    window.showGlobalNotification(message, type);
                                    
                                    // Mark as read (optional - can remove this if you want notifications to persist)
                                    axios.post(`/api/notifications/${notif.id}/read`).catch(e => console.log('Mark read error:', e));
                                }
                            });
                        }
                    } catch (error) {
                        console.error('Notification check error:', error);
                    }
                }
            }

            // Start checking for notifications when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    setInterval(checkForNotifications, CHECK_INTERVAL);
                    checkForNotifications(); // Check immediately
                });
            } else {
                setInterval(checkForNotifications, CHECK_INTERVAL);
                checkForNotifications(); // Check immediately
            }
        })();

        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);

        // Show session flash messages via global notification (single notification, auto-dismiss)
        @if(session('success'))
            window.showGlobalNotification(@json(session('success')), 'success', 4000);
        @endif
        @if(session('error'))
            window.showGlobalNotification(@json(session('error')), 'error', 5000);
        @endif
        @if($errors->any())
            window.showGlobalNotification(@json($errors->first()), 'error', 5000);
        @endif
    </script>
    @yield('scripts')
</body>
</html>
