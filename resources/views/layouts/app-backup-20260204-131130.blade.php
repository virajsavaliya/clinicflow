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
        /* ============================================
           CLINICFLOW DESIGN SYSTEM v2.0
           Professional, Consistent, Client-Ready
        ============================================ */

        :root {
            /* Color Palette */
            --primary-black: #1a1a1a;
            --primary-dark: #0f0f0f;
            --gray-900: #111827;
            --gray-800: #1f2937;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-500: #6b7280;
            --gray-400: #9ca3af;
            --gray-300: #d1d5db;
            --gray-200: #e5e7eb;
            --gray-100: #f3f4f6;
            --gray-50: #f9fafb;
            --white: #ffffff;
            
            /* Semantic Colors */
            --success: #10b981;
            --success-light: #d1fae5;
            --success-dark: #059669;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --warning-dark: #d97706;
            --info: #3b82f6;
            --info-light: #dbeafe;
            --info-dark: #2563eb;
            
            /* Layout */
            --header-height: 64px;
            --sidebar-width: 260px;
            
            /* Spacing */
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --space-2xl: 3rem;
            
            /* Border Radius */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            
            /* Transitions */
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 200ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Typography */
            --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-mono: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, monospace;
        }
        
        /* ============================================
           RESET & BASE STYLES
        ============================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; }
        body { 
            font-family: var(--font-sans); 
            background: var(--gray-50); 
            color: var(--gray-900); 
            font-size: 14px; 
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* ============================================
           HEADER
        ============================================ */
        .app-header {
            background: linear-gradient(135deg, var(--primary-black) 0%, var(--gray-800) 100%);
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 var(--space-lg);
            gap: var(--space-md);
            box-shadow: var(--shadow-lg);
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            text-decoration: none;
            color: var(--white);
            font-weight: 700;
            font-size: 1.125rem;
            transition: opacity var(--transition-fast);
        }
        
        .brand-logo:hover {
            color: var(--white);
            opacity: 0.9;
        }
        
        .brand-logo img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }
        
        .brand-logo i {
            font-size: 1.5rem;
        }
        
        /* Main Navigation */
        .main-nav {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            margin-left: var(--space-lg);
            flex: 1;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--gray-400);
            font-weight: 500;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
            white-space: nowrap;
        }
        
        .nav-item:hover {
            color: var(--white);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-item.active {
            color: var(--white);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .nav-item i {
            font-size: 1.125rem;
        }
        
        /* User Section */
        .user-section {
            margin-left: auto;
        }
        
        .user-button {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: var(--radius-md);
            color: var(--white);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background var(--transition-fast);
        }
        
        .user-button:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-full);
            background: var(--white);
            color: var(--primary-black);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.75rem;
        }
        
        .user-badge {
            background: var(--white);
            color: var(--primary-black);
            padding: 0.125rem 0.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* ============================================
           CONTENT AREA
        ============================================ */
        .content-wrapper {
            margin-top: var(--header-height);
            padding: var(--space-lg);
            height: calc(100vh - var(--header-height));
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* ============================================
           PAGE HEADER
        ============================================ */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--space-lg);
        }
        
        .page-title-section h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: var(--space-xs);
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }
        
        .page-title-section h1 i {
            font-size: 1.5rem;
            color: var(--primary-black);
        }
        
        .page-subtitle {
            color: var(--gray-500);
            font-size: 0.875rem;
            margin: 0;
        }
        
        .page-actions {
            display: flex;
            gap: var(--space-sm);
            align-items: center;
        }
        
        /* ============================================
           BUTTONS - UNIFIED DESIGN
        ============================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: all var(--transition-fast);
            text-decoration: none;
            white-space: nowrap;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn i {
            font-size: 1rem;
        }
        
        /* Button Sizes */
        .btn-sm {
            padding: 0.4rem 0.875rem;
            font-size: 0.8125rem;
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
        
        /* Button Variants */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-black) 0%, var(--gray-800) 100%);
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-700) 100%);
            color: var(--white);
            box-shadow: var(--shadow-md);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: var(--white);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, var(--success-dark) 0%, #047857 100%);
            color: var(--white);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
            color: var(--white);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, var(--danger-dark) 0%, #b91c1c 100%);
            color: var(--white);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);
            color: var(--white);
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
            color: var(--white);
        }
        
        .btn-outline-secondary {
            background: var(--white);
            border: 1.5px solid var(--gray-300);
            color: var(--gray-700);
        }
        
        .btn-outline-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
            color: var(--gray-900);
        }
        
        .btn-outline-primary {
            background: var(--white);
            border: 1.5px solid var(--primary-black);
            color: var(--primary-black);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-black);
            color: var(--white);
        }
        
        /* ============================================
           CARDS - UNIFIED DESIGN
        ============================================ */
        .card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all var(--transition-base);
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 1.25rem;
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--gray-900);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header i {
            margin-right: var(--space-sm);
            color: var(--gray-600);
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .card-footer {
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
            padding: 1rem 1.25rem;
        }
        
        /* ============================================
           STAT CARDS - PROFESSIONAL DESIGN
        ============================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: var(--space-lg);
            margin-bottom: var(--space-lg);
        }
        
        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: var(--space-lg);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
            text-decoration: none;
            color: inherit;
        }
        
        .stat-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
            border-color: var(--gray-300);
            color: inherit;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        .stat-icon.black {
            background: linear-gradient(135deg, var(--primary-black) 0%, var(--gray-700) 100%);
            color: var(--white);
        }
        
        .stat-icon.success {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: var(--white);
        }
        
        .stat-icon.danger {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
            color: var(--white);
        }
        
        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);
            color: var(--white);
        }
        
        .stat-icon.info {
            background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
            color: var(--white);
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            font-size: 0.8125rem;
            color: var(--gray-600);
            font-weight: 500;
            text-transform: capitalize;
        }
        
        /* ============================================
           TABLES - UNIFIED DESIGN
        ============================================ */
        .table-responsive {
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }
        
        .table thead th {
            background: var(--gray-50);
            color: var(--gray-700);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.875rem 1rem;
            border-bottom: 2px solid var(--gray-200);
            text-align: left;
        }
        
        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-100);
            font-size: 0.875rem;
            color: var(--gray-800);
            vertical-align: middle;
        }
        
        .table tbody tr {
            transition: background var(--transition-fast);
        }
        
        .table tbody tr:hover {
            background: var(--gray-50);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* ============================================
           FORMS - UNIFIED DESIGN
        ============================================ */
        .form-group {
            margin-bottom: var(--space-lg);
        }
        
        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: var(--space-sm);
        }
        
        .form-label .required {
            color: var(--danger);
            margin-left: 2px;
        }
        
        .form-control,
        .form-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            color: var(--gray-900);
            background: var(--white);
            transition: all var(--transition-fast);
        }
        
        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--info);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-control::placeholder {
            color: var(--gray-400);
        }
        
        .form-text {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: var(--space-xs);
        }
        
        .input-group {
            display: flex;
        }
        
        .input-group-text {
            padding: 0.625rem 0.875rem;
            background: var(--gray-50);
            border: 1.5px solid var(--gray-300);
            border-right: none;
            border-radius: var(--radius-md) 0 0 var(--radius-md);
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        
        .input-group .form-control {
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
        }
        
        /* ============================================
           BADGES
        ============================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: var(--space-xs);
            padding: 0.25rem 0.625rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }
        
        .badge-success {
            background: var(--success-light);
            color: var(--success-dark);
        }
        
        .badge-danger {
            background: var(--danger-light);
            color: var(--danger-dark);
        }
        
        .badge-warning {
            background: var(--warning-light);
            color: var(--warning-dark);
        }
        
        .badge-info {
            background: var(--info-light);
            color: var(--info-dark);
        }
        
        .badge-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }
        
        /* ============================================
           MODALS - PROFESSIONAL DESIGN
        ============================================ */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1050;
            align-items: center;
            justify-content: center;
            padding: var(--space-lg);
        }
        
        .modal.show {
            display: flex;
        }
        
        .modal-dialog {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 540px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .modal-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }
        
        .btn-close {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--gray-100);
            border-radius: var(--radius-full);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            transition: all var(--transition-fast);
        }
        
        .btn-close:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }
        
        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: var(--space-sm);
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--gray-200);
        }
        
        /* ============================================
           ALERTS
        ============================================ */
        .alert {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            margin-bottom: var(--space-lg);
            border-left: 4px solid;
        }
        
        .alert i {
            font-size: 1.25rem;
        }
        
        .alert-success {
            background: var(--success-light);
            color: var(--success-dark);
            border-color: var(--success);
        }
        
        .alert-danger {
            background: var(--danger-light);
            color: var(--danger-dark);
            border-color: var(--danger);
        }
        
        .alert-warning {
            background: var(--warning-light);
            color: var(--warning-dark);
            border-color: var(--warning);
        }
        
        .alert-info {
            background: var(--info-light);
            color: var(--info-dark);
            border-color: var(--info);
        }
        
        /* ============================================
           DROPDOWN
        ============================================ */
        .dropdown-menu {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: var(--space-sm);
            margin-top: var(--space-xs);
            min-width: 200px;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            color: var(--gray-700);
            text-decoration: none;
            transition: all var(--transition-fast);
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        
        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--gray-900);
        }
        
        .dropdown-item i {
            font-size: 1rem;
        }
        
        .dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: var(--space-sm) 0;
        }
        
        /* ============================================
           EMPTY STATES
        ============================================ */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: var(--space-2xl) var(--space-lg);
            text-align: center;
            color: var(--gray-500);
        }
        
        .empty-state-icon {
            width: 80px;
            height: 80px;
            background: var(--gray-100);
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--space-lg);
        }
        
        .empty-state-icon i {
            font-size: 2rem;
            color: var(--gray-400);
        }
        
        .empty-state h5 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: var(--space-sm);
        }
        
        .empty-state p {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-bottom: var(--space-lg);
            max-width: 400px;
        }
        
        /* ============================================
           LOADING SPINNER
        ============================================ */
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--gray-200);
            border-top-color: var(--primary-black);
            border-radius: var(--radius-full);
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* ============================================
           SCROLLBAR
        ============================================ */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: var(--radius-sm);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
        
        /* ============================================
           UTILITIES
        ============================================ */
        .text-muted { color: var(--gray-500) !important; }
        .text-success { color: var(--success) !important; }
        .text-danger { color: var(--danger) !important; }
        .text-warning { color: var(--warning) !important; }
        .text-info { color: var(--info) !important; }
        
        .bg-success { background: var(--success) !important; }
        .bg-danger { background: var(--danger) !important; }
        .bg-warning { background: var(--warning) !important; }
        .bg-info { background: var(--info) !important; }
        
        .d-flex { display: flex; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: var(--space-sm); }
        .gap-3 { gap: var(--space-md); }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-2 { margin-bottom: var(--space-sm); }
        .mb-3 { margin-bottom: var(--space-md); }
        .mb-4 { margin-bottom: var(--space-lg); }
        .mt-2 { margin-top: var(--space-sm); }
        .mt-3 { margin-top: var(--space-md); }
        .ms-2 { margin-left: var(--space-sm); }
        .me-2 { margin-right: var(--space-sm); }
        
        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: var(--space-md);
            }
            
            .nav-item span {
                display: none;
            }
            
            .user-button .user-name {
                display: none;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: var(--space-md);
            }
            
            .page-header {
                flex-direction: column;
                gap: var(--space-md);
            }
            
            .page-actions {
                width: 100%;
                flex-wrap: wrap;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-badge">{{ auth()->user()->role }}</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person"></i>
                                Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Logout
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
    
    <!-- Main Content -->
    <main class="content-wrapper">
        @yield('content')
    </main>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.1/dist/axios.min.js"></script>
    <script>
        // Configure Axios with CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        }
        
        // Global notification system
        window.showNotification = function(message, type = 'info', duration = 4000) {
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };
            
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };
            
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 1rem;
                right: 1rem;
                z-index: 9999;
                background: ${colors[type] || colors.info};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                display: flex;
                align-items: center;
                gap: 0.75rem;
                animation: slideIn 0.3s ease;
                max-width: 400px;
                font-weight: 500;
                font-size: 0.875rem;
            `;
            
            notification.innerHTML = `
                <span style="font-size: 1.25rem;">${icons[type] || icons.info}</span>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; color: white; font-size: 1.25rem; cursor: pointer; padding: 0;">&times;</button>
            `;
            
            document.body.appendChild(notification);
            
            if (duration > 0) {
                setTimeout(() => notification.remove(), duration);
            }
        };
        
        // Animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
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
        
        // Show session messages
        @if(session('success'))
            showNotification(@json(session('success')), 'success');
        @endif
        @if(session('error'))
            showNotification(@json(session('error')), 'error');
        @endif
        @if($errors->any())
            showNotification(@json($errors->first()), 'error');
        @endif
    </script>
    @yield('scripts')
</body>
</html>
