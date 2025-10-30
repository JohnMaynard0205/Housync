<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Portal')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { min-height: 100%; background-color: #f8fafc; }
        body { font-family: 'Inter', sans-serif; color: #1e293b; }
        .dashboard-container { display: flex; min-height: 100vh; }
        aside.sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            width: 260px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            transition: width .2s cubic-bezier(.4,0,.2,1);
        }
        .dashboard-container.collapsed aside.sidebar { width: 72px; }
        .sidebar-header { padding: 1.25rem 1.5rem; font-size: 1.2rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.05); letter-spacing: .5px; display: flex; align-items: center; gap: .75rem; }
        .sidebar-header .portal-title-label { transition: opacity .2s; }
        .dashboard-container.collapsed .portal-title-label { opacity: 0; width: 0; overflow: hidden; }
        .sidebar-hamburger { background: transparent; color: #fff; border: none; font-size: 1.4rem; margin-right: .75rem; outline: none; cursor: pointer; }
        nav.sidebar-nav { flex: 1 1 auto; padding: 1rem 0; display: flex; flex-direction: column; gap: .25rem; }
        nav.sidebar-nav a.nav-link { color: #fff; display: flex; align-items: center; gap: 1rem; border-radius: 8px 0 0 8px; padding: 0.8rem 1.25rem; font-size: 1.08rem; border-left: 4px solid transparent; transition: background .2s, border .2s, color .2s; font-weight: 500; text-decoration: none; }
        nav.sidebar-nav a.nav-link.active, nav.sidebar-nav a.nav-link:hover { background: rgba(255,255,255,0.08); border-left: 4px solid #fff; color: #fff; text-decoration: none; }
        nav.sidebar-nav .nav-icon { min-width: 22px; text-align: center; font-size: 1.2em; }
        nav.sidebar-nav .nav-label { transition: opacity .2s, width .2s; }
        .dashboard-container.collapsed nav.sidebar-nav .nav-label { opacity: 0; width: 0; overflow: hidden; }
        .sidebar-footer { padding: 1.25rem 1.5rem .8rem; border-top: 1px solid rgba(255,255,255,0.05); }
        .main-content { flex: 1 1 0%; background: #f8fafc; min-width: 0; padding: 2rem; transition: none; }
        @media (max-width: 900px) { aside.sidebar { position: fixed; left: 0; height: 100vh; z-index: 1040; } .main-content { padding: 1.5rem .5rem .5rem 1rem; } }
        @media (max-width: 600px) { .main-content { padding: .7rem .2rem; } aside.sidebar { width: 90px; } .dashboard-container.collapsed aside.sidebar { width: 56px; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="dashboard-container" id="dashboardContainer">
    <aside class="sidebar">
        <div class="sidebar-header">
            <button class="sidebar-hamburger" id="sidebarCollapseBtn" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
            <span class="portal-title-label">Staff Portal</span>
        </div>
        <nav class="sidebar-nav">
            <a class="nav-link{{ request()->routeIs('staff.dashboard') ? ' active' : '' }}" href="{{ route('staff.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-home"></i></span> <span class="nav-label">Dashboard</span>
            </a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-tools"></i></span> <span class="nav-label">Maintenance Requests</span></a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-calendar"></i></span> <span class="nav-label">Work Schedule</span></a>
            <a class="nav-link" href="#"><span class="nav-icon"><i class="fas fa-message"></i></span> <span class="nav-label">Messages</span></a>
            <a class="nav-link{{ request()->routeIs('staff.profile') ? ' active' : '' }}" href="{{ route('staff.profile') }}">
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
            try { localStorage.setItem('staffSidebarCollapsed', cont.classList.contains('collapsed') ? '1' : '0'); } catch (e) {}
        }
    };
    // Respect stored state
    (function() {
        var cont = document.getElementById('dashboardContainer');
        try { if (localStorage.getItem('staffSidebarCollapsed') === '1') cont.classList.add('collapsed'); } catch (e) {}
    })();
</script>
@stack('scripts')
@yield('scripts')
</body>
</html> 