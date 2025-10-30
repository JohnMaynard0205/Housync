<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HouseSync') - Landlord Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .dashboard-container.collapsed .sidebar { width: 80px; }
        .dashboard-container.collapsed .main-content { margin-left: 80px; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #ea580c 0%, #dc2626 100%); color: white; display: flex; flex-direction: column; position: fixed; height: 100vh; left: 0; top: 0; z-index: 1000; transition: width 0.2s cubic-bezier(.4,0,.2,1); }
        .dashboard-container.collapsed .sidebar-header h2,
        .dashboard-container.collapsed .sidebar-header p { display: none; }
        .dashboard-container.collapsed .nav-item { justify-content: center; }
        .dashboard-container.collapsed .nav-item i { margin-right: 0; font-size: 1.1rem; }
        .dashboard-container.collapsed .nav-item .nav-text { display: none; }
        .sidebar-header { padding: 2rem 1.5rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); position: relative; }
        .collapse-btn { position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.15); color: #fff; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s ease; }
        .collapse-btn:hover { background: rgba(255,255,255,0.25); }
        .sidebar-header h2 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; }
        .sidebar-header p { font-size: 0.875rem; opacity: 0.8; }
        .sidebar-nav { flex: 1; padding: 1.5rem 0; }
        .nav-item { display: flex; align-items: center; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent; position: relative; }
        .nav-item:hover { background-color: rgba(255,255,255,0.1); color: white; border-left-color: #fb923c; }
        .nav-item.active { background-color: #f97316; color: white; border-left-color: #fb923c; }
        .nav-item i { width: 20px; margin-right: 0.75rem; font-size: 1rem; }
        .badge-count { background-color: #ef4444; color: white; border-radius: 9999px; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; margin-left: auto; }
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
        .logout-btn { display: flex; align-items: center; width: 100%; padding: 0.875rem; background: rgba(255,255,255,0.1); border: none; border-radius: 0.5rem; color: white; text-decoration: none; transition: all 0.2s; }
        .logout-btn:hover { background: rgba(255,255,255,0.2); color: white; }
        .logout-btn i { margin-right: 0.5rem; }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: margin-left 0.2s cubic-bezier(.4,0,.2,1);
        }
        .dashboard-container.collapsed .main-content {
            margin-left: 80px;
        }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .content-header h1 { font-size: 2rem; font-weight: 700; color: #1e293b; }
        .user-profile { display: flex; align-items: center; background: white; padding: 0.75rem 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; margin-right: 0.75rem; }
        .user-info h3 { font-size: 0.875rem; font-weight: 600; color: #1e293b; }
        .user-info p { font-size: 0.75rem; color: #64748b; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 4px solid #f97316; text-align: center; }
        .stat-value { font-size: 2.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
        .stat-label { color: #64748b; font-size: 0.875rem; font-weight: 500; }
        .page-section { background: white; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9; }
        .section-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .section-subtitle { color: #64748b; font-size: 1rem; margin-top: 0.25rem; }
        .properties-grid, .units-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; margin-top: 2rem; }
        .property-card, .unit-card { background: white; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; transition: all 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .property-card:hover, .unit-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); border-color: #f97316; }
        .property-header, .unit-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .property-title, .unit-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem; }
        .property-status, .unit-status { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .status-active, .status-available { background: #d1fae5; color: #059669; }
        .status-inactive, .status-maintenance { background: #fee2e2; color: #dc2626; }
        .status-occupied { background: #fef3c7; color: #d97706; }
        .property-info, .unit-info { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .info-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #64748b; }
        .info-item i { width: 16px; text-align: center; color: #f97316; }
        .property-stats, .unit-details { background: #f8fafc; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; display: flex; justify-content: space-between; }
        .stat-item, .detail-row { text-align: center; display: flex; flex-direction: column; }
        .stat-item-value, .detail-value { font-size: 1.25rem; font-weight: 600; color: #1e293b; }
        .stat-item-label, .detail-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .rent-amount { font-size: 1.25rem; font-weight: 700; color: #f97316; text-align: center; margin-bottom: 1rem; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { 
            background: #f97316 !important; 
            color: white !important; 
            border: 1px solid #f97316 !important;
        }
        .btn-primary:hover { 
            background: #ea580c !important; 
            color: white !important; 
            border-color: #ea580c !important;
        }
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.active {
            background: #f97316 !important;
            border-color: #f97316 !important;
            box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.25) !important;
        }
        .btn-primary:focus:hover,
        .btn-primary:active:hover {
            background: #ea580c !important;
            border-color: #ea580c !important;
        }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-secondary:hover { background: #4b5563; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.75rem; }
        .btn-group { display: flex; gap: 0.5rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; }
        .alert-success { background: #d1fae5; border: 1px solid #a7f3d0; color: #047857; }
        .alert-error { background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; }
        .empty-state { text-align: center; padding: 4rem 2rem; }
        .empty-icon { font-size: 4rem; color: #94a3b8; margin-bottom: 1rem; }
        .empty-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
        .empty-text { color: #64748b; margin-bottom: 2rem; }
        .pagination { display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem; }
        .pagination a, .pagination span { padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; text-decoration: none; color: #374151; font-size: 0.875rem; }
        .pagination a:hover { background: #f9fafb; border-color: #9ca3af; }
        .pagination .active span { background: #f97316; border-color: #f97316; color: white; }
        
        /* Global override for Bootstrap btn-primary to ensure orange color */
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
        .btn-primary.active,
        .btn-primary:not(:disabled):not(.disabled):active {
            background-color: #f97316 !important;
            border-color: #f97316 !important;
            box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.25) !important;
        }
        .btn-primary:focus:hover,
        .btn-primary:active:hover {
            background-color: #ea580c !important;
            border-color: #ea580c !important;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1100;
            background: #f97316;
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
            background: #ea580c;
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

        /* Responsive Styles */
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

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 1rem;
            }

            .properties-grid, .units-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 1rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 5rem 1rem 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .properties-grid, .units-grid {
                grid-template-columns: 1fr;
            }

            .page-section {
                padding: 1.5rem;
            }

            .section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .property-info, .unit-info {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
                width: 100%;
            }

            .btn-group .btn {
                width: 100%;
            }

            .user-profile {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .stat-value {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .property-title, .unit-title {
                font-size: 1.1rem;
            }

            .mobile-menu-toggle {
                width: 40px;
                height: 40px;
            }
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
                <button class="collapse-btn" id="collapseSidebarBtnLandlord" title="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>Landlord Portal</h2>
                <p>Property Manager</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('landlord.dashboard') }}" class="nav-item {{ request()->routeIs('landlord.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> <span class="nav-text">My Dashboard</span>
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item {{ request()->routeIs('landlord.apartments') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> <span class="nav-text">My Properties</span>
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item {{ request()->routeIs('landlord.units') ? 'active' : '' }}">
                    <i class="fas fa-door-open"></i> <span class="nav-text">My Units</span>
                </a>
                <a href="{{ route('landlord.tenant-assignments') }}" class="nav-item {{ request()->routeIs('landlord.tenant-assignments') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> <span class="nav-text">Tenant Assignments</span>
                </a>
                <a href="{{ route('landlord.tenant-history') }}" class="nav-item {{ request()->routeIs('landlord.tenant-history') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> <span class="nav-text">Tenant History</span>
                </a>
                <a href="{{ route('landlord.staff') }}" class="nav-item {{ request()->routeIs('landlord.staff*') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i> <span class="nav-text">Staff</span>
                </a>
                <a href="{{ route('landlord.security') }}" class="nav-item {{ request()->routeIs('landlord.security*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> <span class="nav-text">Security</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i> <span class="nav-text">Payments</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tools"></i> <span class="nav-text">Maintenance</span>
                </a>

            </nav>
            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const container = document.getElementById('dashboardContainer') || document.querySelector('.dashboard-container');
            const collapseBtn = document.getElementById('collapseSidebarBtnLandlord');
            
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
                try { localStorage.setItem('landlordSidebarCollapsed', collapsed ? '1' : '0'); } catch (e) {}
            }
            // initialize from storage
            try { if (localStorage.getItem('landlordSidebarCollapsed') === '1') setCollapsedState(true); } catch (e) {}
            
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
    @yield('scripts')
</body>
</html> 