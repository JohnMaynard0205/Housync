<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HouseSync') - Landlord Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #ea580c 0%, #dc2626 100%); color: white; display: flex; flex-direction: column; position: fixed; height: 100vh; left: 0; top: 0; z-index: 1000; }
        .sidebar-header { padding: 2rem 1.5rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
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
        .main-content { flex: 1; margin-left: 280px; padding: 2rem; }
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
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Landlord Portal</h2>
                <p>Property Manager</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('landlord.dashboard') }}" class="nav-item {{ request()->routeIs('landlord.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> My Dashboard
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item {{ request()->routeIs('landlord.apartments') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> My Properties
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item {{ request()->routeIs('landlord.units') ? 'active' : '' }}">
                    <i class="fas fa-door-open"></i> My Units
                </a>
                <a href="{{ route('landlord.tenant-assignments') }}" class="nav-item {{ request()->routeIs('landlord.tenant-assignments') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Tenant Assignments
                </a>
                <a href="{{ route('landlord.staff') }}" class="nav-item {{ request()->routeIs('landlord.staff*') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i> Staff
                </a>
                <a href="{{ route('landlord.security') }}" class="nav-item {{ request()->routeIs('landlord.security*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> Security
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tools"></i> Maintenance
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
    @stack('scripts')
    @yield('scripts')
</body>
</html> 