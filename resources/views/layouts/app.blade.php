<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HouseSync') - Tenant Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles - Green Theme for Tenants */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            position: relative;
        }

        .nav-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #34d399;
            text-decoration: none;
        }

        .nav-item.active {
            background-color: #059669;
            color: white;
            border-left-color: #34d399;
        }

        .nav-item i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.875rem;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 0.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
        }

        .logout-btn i {
            margin-right: 0.5rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .content-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .user-profile {
            display: flex;
            align-items: center;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        .user-info h3 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
        }

        .user-info p {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Page Title Box */
        .page-title-box {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: #10b981;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #64748b;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        /* Buttons */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: #f97316 !important;
            color: white !important;
            border: 1px solid #f97316 !important;
        }

        .btn-primary:hover {
            background-color: #ea580c !important;
            color: white !important;
            border-color: #ea580c !important;
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

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
            color: white;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-outline-primary {
            border: 1px solid #10b981;
            color: #10b981;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background-color: #10b981;
            color: white;
        }

        .btn-outline-secondary {
            border: 1px solid #6b7280;
            color: #6b7280;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: #6b7280;
            color: white;
        }

        /* Badges */
        .badge {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
        }

        .bg-success {
            background-color: #10b981 !important;
        }

        .bg-warning {
            background-color: #f59e0b !important;
        }

        .bg-danger {
            background-color: #ef4444 !important;
        }

        .bg-info {
            background-color: #3b82f6 !important;
        }

        .bg-secondary {
            background-color: #6b7280 !important;
        }

        /* Alerts */
        .alert {
            border-radius: 0.5rem;
            border: none;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #374151;
        }

        .table td {
            vertical-align: middle;
        }

        /* Responsive */
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
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Tenant Portal</h2>
                <p>Welcome, {{ auth()->user()->name }}</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('tenant.dashboard') }}" class="nav-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="{{ route('tenant.upload-documents') }}" class="nav-item {{ request()->routeIs('tenant.upload-documents') ? 'active' : '' }}">
                    <i class="fas fa-upload"></i> Upload Documents
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tools"></i> Maintenance
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-message"></i> Messages
                </a>
                <a href="{{ route('tenant.lease') }}" class="nav-item">
                    <i class="fas fa-file-contract"></i> Lease
                </a>
                <a href="{{ route('tenant.profile') }}" class="nav-item">
                    <i class="fas fa-user"></i> Profile
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
</body>
</html> 