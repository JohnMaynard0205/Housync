<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tenant Portal')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            min-height: 100%;
            background-color: #f8fafc;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        aside.sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            width: 260px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: width .2s cubic-bezier(.4,0,.2,1);
        }
        .dashboard-container.collapsed aside.sidebar {
            width: 72px;
        }
        .sidebar-header {
            padding: 1.25rem 1.5rem;
            font-size: 1.2rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            letter-spacing: .5px;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .sidebar-header .portal-title-label {
            transition: opacity .2s;
        }
        .dashboard-container.collapsed .portal-title-label {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .sidebar-hamburger {
            background: transparent;
            color: #fff;
            border: none;
            font-size: 1.4rem;
            margin-right: .75rem;
            outline: none;
            cursor: pointer;
        }
        nav.sidebar-nav {
            flex: 1 1 auto;
            padding: 1rem 0;
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }
        nav.sidebar-nav a.nav-link {
            color: #fff;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-radius: 8px 0 0 8px;
            padding: 0.8rem 1.25rem;
            font-size: 1.08rem;
            border-left: 4px solid transparent;
            transition: background .2s, border .2s, color .2s;
            font-weight: 500;
            text-decoration: none;
        }
        nav.sidebar-nav a.nav-link.active, nav.sidebar-nav a.nav-link:hover {
            background: rgba(255,255,255,0.08);
            border-left: 4px solid #fff;
            color: #fff;
            text-decoration: none;
        }
        nav.sidebar-nav .nav-icon {
            min-width: 22px;
            text-align: center;
            font-size: 1.2em;
        }
        nav.sidebar-nav .nav-label {
            transition: opacity .2s, width .2s;
        }
        .dashboard-container.collapsed nav.sidebar-nav .nav-label {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .sidebar-footer {
            padding: 1.25rem 1.5rem .8rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .main-content {
            flex: 1 1 0%;
            background: #f8fafc;
            min-width: 0;
            padding: 2rem;
            transition: none;
        }
        /* --- GLOBAL DASHBOARD STYLES (copied/adjusted from landlord-app) --- */
        .main-content .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .main-content .content-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }
        .main-content .user-profile {
            display: flex;
            align-items: center;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .main-content .user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, #818cf8, #a21caf);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 600; font-size: 125%; margin-right: 0.9rem;
        }
        .main-content .user-info h3 {
            font-size: 0.93rem;
            font-weight: 600;
            color: #1e293b;
        }
        .main-content .user-info p { font-size: 0.75rem; color: #64748b; }
        .main-content .welcome-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            border-left: 4px solid #818cf8;
        }
        .main-content .welcome-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: .6rem;
        }
        .main-content .welcome-section p { color: #64748b; font-size: 1rem; }
        .main-content .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .main-content .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border-left: 4px solid #818cf8;
        }
        .main-content .stat-card .stat-value { font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 0.2rem; }
        .main-content .stat-card .stat-label { color: #64748b; font-size: 0.92rem; margin-bottom: 0.5rem; }
        .main-content .stat-card .stat-sublabel { font-size: 0.78rem; color: #94a3b8; }
        .main-content .stat-card.revenue-card { border-left-color: #10b981; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); }
        .main-content .revenue-value { color: #059669; }
        .main-content .property-summary {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .main-content .occupancy-rate {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem; background: #f5f7fa; border-radius: 0.5rem;
            border-left: 4px solid #818cf8;
        }
        .main-content .occupancy-percentage { font-size: 2rem; font-weight: 700; color: #818cf8; }
        .main-content .occupancy-label { font-size: 0.875rem; color: #64748b; }
        .main-content .badge-count { background: #ef4444; color: white; border-radius: 9999px; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; margin-left: 8px; }
        .main-content .activity-section, .main-content .quick-actions, .main-content .property-summary { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 1.5rem; }
        .main-content .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
        .main-content .section-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; }
        .main-content .btn-primary { background: #6366f1; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.90rem; font-weight: 500; transition: all 0.2s; }
        .main-content .btn-primary:hover { background: #818cf8; color: #fff; }
        .main-content .status-badge.status-available { background: #d1fae5; color: #059669; }
        .main-content .status-badge.status-occupied { background: #dbeafe; color: #2563eb; }
        .main-content .status-badge.status-maintenance { background: #fef3c7; color: #d97706; }
        @media (max-width: 1200px) { .main-content .content-grid { grid-template-columns: 1fr; } }
        @media (max-width: 900px) { aside.sidebar { position: fixed; left: 0; height: 100vh; z-index: 1040; } .main-content { padding: 1.5rem .5rem .5rem 1rem; } }
        @media (max-width: 600px) { .main-content { padding: .6rem .2rem; } .main-content .stats-grid { grid-template-columns: 1fr; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="dashboard-container" id="dashboardContainer">
    <aside class="sidebar">
        <div class="sidebar-header">
            <button class="sidebar-hamburger" id="sidebarCollapseBtn" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
            <span class="portal-title-label">Tenant Portal</span>
        </div>
        <nav class="sidebar-nav">
            <a class="nav-link{{ request()->routeIs('tenant.dashboard') ? ' active' : '' }}" href="{{ route('tenant.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-home"></i></span> <span class="nav-label">Dashboard</span>
            </a>
            <a class="nav-link{{ request()->routeIs('tenant.upload-documents') ? ' active' : '' }}" href="{{ route('tenant.upload-documents') }}">
                <span class="nav-icon"><i class="fas fa-upload"></i></span> <span class="nav-label">Upload Documents</span>
            </a>
            <a class="nav-link{{ request()->routeIs('explore') ? ' active' : '' }}" href="{{ route('explore') }}">
                <span class="nav-icon"><i class="fas fa-search"></i></span> <span class="nav-label">Browse Properties</span>
            </a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-credit-card"></i></span> <span class="nav-label">Payments</span></a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-tools"></i></span> <span class="nav-label">Maintenance</span></a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-message"></i></span> <span class="nav-label">Messages</span></a>
            <a class="nav-link{{ request()->routeIs('tenant.lease') ? ' active' : '' }}" href="{{ route('tenant.lease') }}">
                <span class="nav-icon"><i class="fas fa-file-contract"></i></span> <span class="nav-label">Lease</span>
            </a>
            <a class="nav-link{{ request()->routeIs('tenant.profile') ? ' active' : '' }}" href="{{ route('tenant.profile') }}">
                <span class="nav-icon"><i class="fas fa-user"></i></span> <span class="nav-label">Profile</span>
            </a>
        </nav>
        <div class="sidebar-footer mt-auto">
            <a href="{{ route('logout') }}" class="btn btn-danger w-100" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-1"></i> <span class="nav-label">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </aside>
    <main class="main-content">@yield('content')</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarCollapseBtn').onclick = function() {
        var cont = document.getElementById('dashboardContainer');
        if (cont) {
            cont.classList.toggle('collapsed');
            try { localStorage.setItem('tenantSidebarCollapsed', cont.classList.contains('collapsed') ? '1' : '0'); } catch (e) {}
        }
    };
    // Respect stored state
    (function() {
        var cont = document.getElementById('dashboardContainer');
        try { if (localStorage.getItem('tenantSidebarCollapsed') === '1') cont.classList.add('collapsed'); } catch (e) {}
    })();
</script>
@stack('scripts')
@yield('scripts')
</body>
</html> 