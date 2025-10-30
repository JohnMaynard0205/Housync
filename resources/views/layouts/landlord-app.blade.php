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
        html, body {
            min-height: 100%;
            background-color: #f8fafc;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        .dashboard-container { display: flex; min-height: 100vh; }
        aside.sidebar {
            background: linear-gradient(180deg, #ea580c 0%, #dc2626 100%);
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
        .sidebar-footer { display:none; }
        .main-content {
            flex: 1 1 0%;
            background: #f8fafc;
            min-width: 0;
            padding: 2rem;
            transition: none;
        }
        /* Profile dropdown */
        .topbar{display:flex;justify-content:flex-end;align-items:center;margin-bottom:1rem}
        .profile-btn{display:flex;align-items:center;gap:.6rem;background:#fff;border:1px solid #e2e8f0;border-radius:9999px;padding:.35rem .6rem;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.04)}
        .profile-avatar{width:32px;height:32px;border-radius:50%;background:#ea580c;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600}
        .dropdown{position:relative}
        .dropdown-menu{position:absolute;right:0;top:calc(100% + 8px);background:#fff;border:1px solid #e2e8f0;border-radius:10px;min-width:220px;box-shadow:0 8px 24px rgba(0,0,0,.08);display:none;z-index:1050;padding:.4rem}
        .dropdown-menu.show{display:block}
        .dropdown-item{display:flex;align-items:center;gap:.6rem;padding:.6rem .8rem;border-radius:8px;color:#1e293b;text-decoration:none}
        .dropdown-item:hover{background:#f8fafc;text-decoration:none}
        /* --- GLOBAL DASHBOARD STYLES --- */
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
            background: linear-gradient(135deg, #f97316, #ea580c);
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
            border-left: 4px solid #f97316;
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
            border-left: 4px solid #f97316;
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
            border-left: 4px solid #a855f7;
        }
        .main-content .occupancy-percentage { font-size: 2rem; font-weight: 700; color: #a855f7; }
        .main-content .occupancy-label { font-size: 0.875rem; color: #64748b; }
        .main-content .badge-count { background: #ef4444; color: white; border-radius: 9999px; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; margin-left: 8px; }
        .main-content .activity-section, .main-content .quick-actions, .main-content .property-summary { background: white; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 1.5rem; }
        .main-content .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
        .main-content .section-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; }
        .main-content .btn-primary { background: #f97316; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.90rem; font-weight: 500; transition: all 0.2s; }
        .main-content .btn-primary:hover { background: #ea580c; color: #fff; }
        .main-content .status-badge.status-available { background: #d1fae5; color: #059669; }
        .main-content .status-badge.status-occupied { background: #dbeafe; color: #2563eb; }
        .main-content .status-badge.status-maintenance { background: #fef3c7; color: #d97706; }
        /* Responsive Tweaks */
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
            <span class="portal-title-label">Landlord Portal</span>
        </div>
        <nav class="sidebar-nav">
            <a class="nav-link{{ request()->routeIs('landlord.dashboard') ? ' active' : '' }}" href="{{ route('landlord.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-home"></i></span> <span class="nav-label">Dashboard</span>
            </a>
            <a class="nav-link{{ request()->routeIs('landlord.apartments') ? ' active' : '' }}" href="{{ route('landlord.apartments') }}">
                <span class="nav-icon"><i class="fas fa-building"></i></span> <span class="nav-label">My Properties</span>
            </a>
            <a class="nav-link{{ request()->routeIs('landlord.units') ? ' active' : '' }}" href="{{ route('landlord.units') }}">
                <span class="nav-icon"><i class="fas fa-door-open"></i></span> <span class="nav-label">My Units</span>
            </a>
            <a class="nav-link{{ request()->routeIs('landlord.tenant-assignments') ? ' active' : '' }}" href="{{ route('landlord.tenant-assignments') }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span> <span class="nav-label">Tenant Assignments</span>
            </a>
            <a class="nav-link{{ request()->routeIs('landlord.tenant-history') ? ' active' : '' }}" href="{{ route('landlord.tenant-history') }}">
                <span class="nav-icon"><i class="fas fa-history"></i></span> <span class="nav-label">Tenant History</span>
            </a>
            <a class="nav-link{{ request()->routeIs('landlord.staff*') ? ' active' : '' }}" href="{{ route('landlord.staff') }}">
                <span class="nav-icon"><i class="fas fa-tools"></i></span> <span class="nav-label">Staff</span>
            </a>
            <a class="nav-link{{ request()->routeIs('landlord.security*') ? ' active' : '' }}" href="{{ route('landlord.security') }}">
                <span class="nav-icon"><i class="fas fa-shield-alt"></i></span> <span class="nav-label">Security</span>
            </a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-credit-card"></i></span> <span class="nav-label">Payments</span></a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-tools"></i></span> <span class="nav-label">Maintenance</span></a>
        </nav>
        <div class="sidebar-footer mt-auto"></div>
    </aside>
    <main class="main-content">
        <div class="topbar">
            <div class="dropdown" id="llProfileDropdown">
                <div class="profile-btn" id="llProfileBtn">
                    <div class="profile-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down" style="font-size:.85rem;color:#64748b"></i>
                </div>
                <div class="dropdown-menu" id="llDropdownMenu">
                    <a href="#" class="dropdown-item"><i class="fas fa-user-cog"></i> Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item" style="width:100%;background:none;border:none;text-align:left"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </form>
                </div>
            </div>
        </div>
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarCollapseBtn').onclick = function() {
        var cont = document.getElementById('dashboardContainer');
        if (cont) {
            cont.classList.toggle('collapsed');
            try { localStorage.setItem('landlordSidebarCollapsed', cont.classList.contains('collapsed') ? '1' : '0'); } catch (e) {}
        }
    };
    // Respect stored state
    (function() {
        var cont = document.getElementById('dashboardContainer');
        try { if (localStorage.getItem('landlordSidebarCollapsed') === '1') cont.classList.add('collapsed'); } catch (e) {}
    })();
    (function(){
        var btn=document.getElementById('llProfileBtn');
        var menu=document.getElementById('llDropdownMenu');
        if(btn&&menu){
            btn.addEventListener('click',function(e){ e.stopPropagation(); menu.classList.toggle('show'); });
            document.addEventListener('click',function(){ menu.classList.remove('show'); });
        }
    })();
</script>
@stack('scripts')
@yield('scripts')
</body>
</html> 