<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - HouseSync Staff Portal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2196F3;
            --secondary-color: #FF9800;
            --success-color: #4CAF50;
            --warning-color: #FFC107;
            --danger-color: #F44336;
            --dark-color: #212121;
            --light-color: #F5F5F5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .dashboard-container { display: flex; min-height: 100vh; }
        .dashboard-container.collapsed .sidebar { width: 80px; }
        .dashboard-container.collapsed .main-content { margin-left: 80px; }

        /* Sidebar Styles */
        .sidebar { width: 280px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0; position: fixed; height: 100vh; overflow-y: auto; z-index: 1000; transition: width 0.2s cubic-bezier(.4,0,.2,1); }
        .dashboard-container.collapsed .sidebar-header h2,
        .dashboard-container.collapsed .sidebar-header p { display: none; }
        .dashboard-container.collapsed .nav-item { justify-content: center; }
        .dashboard-container.collapsed .nav-item i { margin-right: 0; }
        .dashboard-container.collapsed .nav-item .nav-text { display: none; }

        .sidebar-header { padding: 2rem 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative; }
        .collapse-btn { position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.15); color: #fff; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s ease; }
        .collapse-btn:hover { background: rgba(255,255,255,0.25); }

        .sidebar-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .sidebar-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--primary-color);
        }

        .nav-item.active {
            background-color: rgba(255,255,255,0.15);
            color: white;
            border-left-color: var(--primary-color);
        }

        .nav-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            background-color: #f8f9fa;
            transition: margin-left 0.2s cubic-bezier(.4,0,.2,1);
        }
        .dashboard-container.collapsed .main-content {
            margin-left: 80px;
        }

        /* Header */
        .page-header {
            background: white;
            padding: 1.5rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .user-role {
            color: var(--primary-color);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .card-title {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #f97316 !important;
            border-color: #f97316 !important;
            color: white !important;
        }

        .btn-primary:hover {
            background-color: #ea580c !important;
            border-color: #ea580c !important;
            color: white !important;
        }

        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.active {
            background-color: #f97316 !important;
            border-color: #f97316 !important;
            box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.25) !important;
        }

        .btn-primary:focus:hover,
        .btn-primary:active:hover {
            background-color: #ea580c !important;
            border-color: #ea580c !important;
        }

        /* Badges */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark-color);
            background-color: #f8f9fa;
        }

        .table td {
            vertical-align: middle;
        }

        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stats-card h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .stats-card p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1100;
            background: #667eea;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: #764ba2;
        }

        .mobile-menu-toggle i {
            font-size: 1.25rem;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        /* Prevent container overlap in collapsed sidebar mode */
        .dashboard-container.collapsed .main-content > .container,
        .dashboard-container.collapsed .main-content > .container-fluid {
            margin-left: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            transition: none;
        }
        .main-content > .container,
        .main-content > .container-fluid {
            margin-left: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            transition: none;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 5rem 1.5rem 1.5rem;
            }

            .mobile-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .page-header {
                padding: 1rem;
                margin: -5rem -1.5rem 1rem -1.5rem;
                padding-top: 4rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 5rem 1rem 1rem;
            }

            .page-header {
                padding: 1rem;
                margin: -5rem -1rem 1rem -1rem;
                padding-top: 4rem;
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .user-menu {
                width: 100%;
            }

            .mobile-menu-toggle {
                width: 40px;
                height: 40px;
            }

            .card {
                margin-bottom: 1rem;
            }

            .stats-card {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.25rem;
            }

            .stats-card h3 {
                font-size: 1.5rem;
            }
        }

        /* Utilities */
        .text-primary { color: var(--primary-color) !important; }
        .text-secondary { color: var(--secondary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-warning { color: var(--warning-color) !important; }
        .text-danger { color: var(--danger-color) !important; }

        .bg-soft-primary { background-color: rgba(33, 150, 243, 0.1) !important; }
        .bg-soft-success { background-color: rgba(76, 175, 80, 0.1) !important; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
        .bg-soft-info { background-color: rgba(0, 188, 212, 0.1) !important; }

        .avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .avatar-lg {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .avatar-title {
            background-color: var(--primary-color);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-container" id="dashboardContainer">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <button class="collapse-btn" id="collapseSidebarBtnStaff" title="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>Staff Portal</h2>
                <p>Maintenance & Services</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('staff.dashboard') }}" class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span class="nav-text">Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tools"></i> <span class="nav-text">Maintenance Requests</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-calendar"></i> <span class="nav-text">Work Schedule</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-message"></i> <span class="nav-text">Messages</span>
                </a>
                <a href="{{ route('staff.profile') }}" class="nav-item {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> <span class="nav-text">Profile</span>
                </a>
            </nav>
            <div class="sidebar-footer" style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">@yield('title')</h1>
                </div>
                <div class="user-menu">
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name }}</p>
                        <p class="user-role">Staff Member</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const container = document.getElementById('dashboardContainer') || document.querySelector('.dashboard-container');
            const collapseBtn = document.getElementById('collapseSidebarBtnStaff');
            
            function toggleMobileMenu() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                
                // Change icon
                const icon = mobileMenuToggle.querySelector('i');
                if (sidebar.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            function setCollapsedState(collapsed) {
                if (!container) return;
                if (collapsed) { container.classList.add('collapsed'); }
                else { container.classList.remove('collapsed'); }
                try { localStorage.setItem('staffSidebarCollapsed', collapsed ? '1' : '0'); } catch (e) {}
            }
            // init from storage
            try { if (localStorage.getItem('staffSidebarCollapsed') === '1') setCollapsedState(true); } catch (e) {}
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', toggleMobileMenu);
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleMobileMenu);
            }
            
            if (collapseBtn) {
                collapseBtn.addEventListener('click', function() {
                    const isCollapsed = container.classList.contains('collapsed');
                    setCollapsedState(!isCollapsed);
                });
            }
            
            // Close mobile menu when clicking on a nav item
            const navItems = sidebar.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 1024 && sidebar.classList.contains('show')) {
                        toggleMobileMenu();
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html> 