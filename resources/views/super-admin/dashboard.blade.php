<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Housesync</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Sidebar Styles - Blue Theme */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
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
            border-left-color: #60a5fa;
        }

        .nav-item.active {
            background-color: #3b82f6;
            color: white;
            border-left-color: #60a5fa;
        }

        .nav-item i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .badge {
            background-color: #ef4444;
            color: white;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: auto;
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
            background: linear-gradient(135deg, #3b82f6, #1e40af);
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

        /* Welcome Section */
        .welcome-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
        }

        .welcome-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            color: #64748b;
            font-size: 1rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .stat-sublabel {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        /* Activity Section */
        .activity-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #2563eb;
            color: white;
        }

        .btn-primary i {
            margin-right: 0.5rem;
        }

        /* Activity Table */
        .activity-table {
            width: 100%;
            border-collapse: collapse;
        }

        .activity-table th {
            text-align: left;
            padding: 1rem 0.5rem;
            border-bottom: 2px solid #f1f5f9;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .activity-table td {
            padding: 1rem 0.5rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-active {
            background: #dbeafe;
            color: #2563eb;
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .action-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            text-decoration: none;
            color: #1e293b;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
            transform: translateY(-1px);
        }

        .action-btn:last-child {
            margin-bottom: 0;
        }

        .action-btn i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Super Admin Portal</h2>
                <p>System Administrator</p>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('super-admin.dashboard') }}" class="nav-item active">
                    <i class="fas fa-home"></i> My Dashboard
                </a>
                <a href="{{ route('super-admin.pending-landlords') }}" class="nav-item">
                    <i class="fas fa-user-clock"></i> Pending Approvals
                    @if($stats['pending_landlords'] > 0)
                        <span class="badge">{{ $stats['pending_landlords'] }}</span>
                    @endif
                </a>
                <a href="{{ route('super-admin.users') }}" class="nav-item">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="{{ route('super-admin.apartments') }}" class="nav-item">
                    <i class="fas fa-building"></i> Properties
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i> System Settings
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
            <!-- Header -->
            <div class="content-header">
                <h1>Super Admin Portal</h1>
                <div class="user-profile">
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="user-info">
                        <h3>{{ auth()->user()->name }}</h3>
                        <p>System Administrator</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Welcome Section -->
            <div class="welcome-section">
                <h2>Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
                <p>Here's what's happening in your system today</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-sublabel">Across all roles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['pending_landlords'] }}</div>
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-sublabel">Require your attention</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['approved_landlords'] }}</div>
                    <div class="stat-label">Active Landlords</div>
                    <div class="stat-sublabel">Approved & verified</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_apartments'] }}</div>
                    <div class="stat-label">Total Properties</div>
                    <div class="stat-sublabel">In the system</div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Recent Activity -->
                <div class="activity-section">
                    <div class="section-header">
                        <h3 class="section-title">Recent Activity</h3>
                        <a href="{{ route('super-admin.users') }}" class="btn-primary">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                    
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers->take(5) as $user)
                            <tr>
                                <td>{{ $user->created_at->format('M j, Y') }}</td>
                                <td>{{ $user->name }}</td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $user->role) }}</td>
                                <td>
                                    @if($user->role === 'landlord')
                                        @if($user->status === 'pending')
                                            <span class="status-badge status-pending">Pending</span>
                                        @elseif($user->status === 'approved')
                                            <span class="status-badge status-approved">Approved</span>
                                        @else
                                            <span class="status-badge status-active">{{ ucfirst($user->status) }}</span>
                                        @endif
                                    @else
                                        <span class="status-badge status-active">Active</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3 class="section-title" style="margin-bottom: 1.5rem;">Quick Actions</h3>
                    
                    <a href="{{ route('super-admin.create-user') }}" class="action-btn">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                    
                    <a href="{{ route('super-admin.pending-landlords') }}" class="action-btn">
                        <i class="fas fa-user-check"></i> Review Approvals
                    </a>
                    
                    <a href="{{ route('super-admin.apartments') }}" class="action-btn">
                        <i class="fas fa-building"></i> Manage Properties
                    </a>
                    
                    <a href="{{ route('super-admin.users') }}" class="action-btn">
                        <i class="fas fa-users-cog"></i> User Management
                    </a>

                    @if($stats['pending_landlords'] > 0)
                    <div style="margin-top: 1.5rem; padding: 1rem; background: #fef3c7; border-radius: 0.5rem; border-left: 4px solid #f59e0b;">
                        <h4 style="color: #d97706; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fas fa-exclamation-triangle"></i> Attention Required
                        </h4>
                        <p style="color: #92400e; font-size: 0.75rem; margin: 0;">
                            You have {{ $stats['pending_landlords'] }} landlord{{ $stats['pending_landlords'] > 1 ? 's' : '' }} waiting for approval.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html> 