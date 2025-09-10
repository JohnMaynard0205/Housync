@extends('layouts.landlord-app')

@section('title', 'Landlord Dashboard')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard - Housesync</title>
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

        /* Sidebar Styles - Orange Theme */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ea580c 0%, #dc2626 100%);
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
            border-left-color: #fb923c;
        }

        .nav-item.active {
            background-color: #f97316;
            color: white;
            border-left-color: #fb923c;
        }

        .nav-item i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .badge-count {
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
            background: linear-gradient(135deg, #f97316, #ea580c);
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
            border-left: 4px solid #f97316;
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

        /* Stats Grid */
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
            border-left: 4px solid #f97316;
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

        /* Revenue highlight */
        .revenue-card {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }

        .revenue-value {
            color: #059669;
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
            background: #f97316;
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
            background: #ea580c;
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

        .status-available {
            background: #d1fae5;
            color: #059669;
        }

        .status-occupied {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-maintenance {
            background: #fef3c7;
            color: #d97706;
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
            background: #f97316;
            border-color: #f97316;
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

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        /* Property Summary Card */
        .property-summary {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .occupancy-rate {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #fef7ff;
            border-radius: 0.5rem;
            border-left: 4px solid #a855f7;
        }

        .occupancy-percentage {
            font-size: 2rem;
            font-weight: 700;
            color: #a855f7;
        }

        .occupancy-label {
            font-size: 0.875rem;
            color: #64748b;
        }
    </style>
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
                <a href="{{ route('landlord.dashboard') }}" class="nav-item active">
                    <i class="fas fa-home"></i> My Dashboard
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item">
                    <i class="fas fa-building"></i> My Properties
                    @if(isset($stats['total_apartments']))
                        <span class="badge-count">{{ $stats['total_apartments'] }}</span>
                    @endif
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item">
                    <i class="fas fa-door-open"></i> My Units
                    @if(isset($stats['total_units']))
                        <span class="badge-count">{{ $stats['total_units'] }}</span>
                    @endif
                </a>
                <a href="{{ route('landlord.tenant-assignments') }}" class="nav-item">
                    <i class="fas fa-users"></i> Tenant Assignments
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
            <!-- Header -->
            <div class="content-header">
                <h1>Landlord Portal</h1>
                <div class="user-profile">
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="user-info">
                        <h3>{{ auth()->user()->name }}</h3>
                        <p>Property Manager</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Welcome Section -->
            <div class="welcome-section">
                <h2>Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
                <p>Here's an overview of your property portfolio</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_apartments'] ?? 0 }}</div>
                    <div class="stat-label">Total Properties</div>
                    <div class="stat-sublabel">In your portfolio</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_units'] ?? 0 }}</div>
                    <div class="stat-label">Total Units</div>
                    <div class="stat-sublabel">Available for rent</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['occupied_units'] ?? 0 }}</div>
                    <div class="stat-label">Occupied Units</div>
                    <div class="stat-sublabel">Currently rented</div>
                </div>
                <div class="stat-card revenue-card">
                    <div class="stat-value revenue-value">₱{{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                    <div class="stat-label">Monthly Revenue</div>
                    <div class="stat-sublabel">From occupied units</div>
                </div>
            </div>

            <!-- Occupancy Rate Summary -->
            @if(($stats['total_units'] ?? 0) > 0)
            <div class="property-summary">
                <div class="occupancy-rate">
                    <div>
                        <div class="occupancy-percentage">{{ round((($stats['occupied_units'] ?? 0) / $stats['total_units']) * 100) }}%</div>
                        <div class="occupancy-label">Occupancy Rate</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.875rem; color: #64748b;">
                            {{ $stats['occupied_units'] ?? 0 }} of {{ $stats['total_units'] }} units occupied
                        </div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">
                            {{ $stats['available_units'] ?? 0 }} units available
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Recent Units -->
                <div class="activity-section">
                    <div class="section-header">
                        <h3 class="section-title">Recent Units</h3>
                        <a href="{{ route('landlord.units') }}" class="btn-primary">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                    
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Property</th>
                                <th>Status</th>
                                <th>Rent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($recentUnits) && count($recentUnits) > 0)
                                @foreach($recentUnits->take(5) as $unit)
                                <tr>
                                    <td>{{ $unit->unit_number }}</td>
                                    <td>{{ $unit->apartment->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($unit->status === 'available')
                                            <span class="status-badge status-available">Available</span>
                                        @elseif($unit->status === 'occupied')
                                            <span class="status-badge status-occupied">Occupied</span>
                                        @else
                                            <span class="status-badge status-maintenance">Maintenance</span>
                                        @endif
                                    </td>
                                    <td>₱{{ number_format($unit->rent_amount ?? 0, 0) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #64748b; padding: 2rem;">
                                        No units found. <a href="{{ route('landlord.create-apartment') }}" style="color: #f97316;">Add your first property</a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3 class="section-title" style="margin-bottom: 1.5rem;">Quick Actions</h3>
                    
                    <a href="{{ route('landlord.create-apartment') }}" class="action-btn">
                        <i class="fas fa-plus-circle"></i> Add New Property
                    </a>
                    
                    <a href="{{ route('landlord.apartments') }}" class="action-btn">
                        <i class="fas fa-building"></i> Manage Properties
                    </a>
                    
                    <a href="{{ route('landlord.units') }}" class="action-btn">
                        <i class="fas fa-door-open"></i> Manage Units
                    </a>
                    

                    
                    <a href="#" class="action-btn">
                        <i class="fas fa-users"></i> Tenant Directory
                    </a>

                    <!-- Property Performance -->
                    @if(($stats['total_units'] ?? 0) > 0)
                    <div style="margin-top: 1.5rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem;">Portfolio Summary</h4>
                        
                        <div style="padding: 1rem; background: #f0f9ff; border-radius: 0.5rem; border-left: 4px solid #0ea5e9; margin-bottom: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; color: #0369a1;">Available Units</span>
                                <span style="font-weight: 600; color: #0369a1;">{{ $stats['available_units'] ?? 0 }}</span>
                            </div>
                        </div>

                        <div style="padding: 1rem; background: #f0fdf4; border-radius: 0.5rem; border-left: 4px solid #10b981; margin-bottom: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; color: #047857;">Occupied Units</span>
                                <span style="font-weight: 600; color: #047857;">{{ $stats['occupied_units'] ?? 0 }}</span>
                            </div>
                        </div>

                        @if(($stats['total_units'] - $stats['occupied_units'] - $stats['available_units']) > 0)
                        <div style="padding: 1rem; background: #fffbeb; border-radius: 0.5rem; border-left: 4px solid #f59e0b;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; color: #d97706;">Maintenance</span>
                                <span style="font-weight: 600; color: #d97706;">{{ $stats['total_units'] - $stats['occupied_units'] - $stats['available_units'] }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div style="margin-top: 1.5rem; padding: 1rem; background: #fef3c7; border-radius: 0.5rem; border-left: 4px solid #f59e0b;">
                        <h4 style="color: #d97706; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">
                            <i class="fas fa-info-circle"></i> Get Started
                        </h4>
                        <p style="color: #92400e; font-size: 0.75rem; margin: 0;">
                            Add your first property to start managing your rental portfolio.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html> 