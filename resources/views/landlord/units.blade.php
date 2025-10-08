@extends('layouts.landlord-app')

@section('title', 'My Units')

@push('styles')
<style>
.units-list {
    background: white;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
}

.units-list .list-header {
    display: flex;
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    font-size: 0.875rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.units-list .list-row {
    display: flex;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    align-items: center;
    transition: background 0.15s ease;
}

.units-list .list-row:hover {
    background: #f8fafc;
}

.units-list .list-row:last-child {
    border-bottom: none;
}

.units-list .list-column {
    flex: 1;
    display: flex;
    align-items: center;
    padding: 0 0.5rem;
    font-size: 0.875rem;
}

.units-list .text-center {
    justify-content: center;
}

.units-list .text-right {
    justify-content: flex-end;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 0.375rem;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    transition: all 0.15s ease;
    cursor: pointer;
}

.btn-icon:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
</style>
@endpush

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Units - Housesync</title>
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #f97316;
            text-align: center;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Page Content */
        .page-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 1rem;
            margin-top: 0.25rem;
        }

        /* Search and Filters */
        .filters-section {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        /* Units Grid */
        .units-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .unit-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .unit-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #f97316;
        }

        .unit-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .unit-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .unit-property {
            font-size: 0.875rem;
            color: #64748b;
        }

        .unit-status {
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
            background: #fef3c7;
            color: #d97706;
        }

        .status-maintenance {
            background: #fee2e2;
            color: #dc2626;
        }

        .unit-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .info-item i {
            width: 16px;
            text-align: center;
            color: #f97316;
        }

        .unit-details {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #64748b;
        }

        .detail-value {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
        }

        .rent-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f97316;
            text-align: center;
            margin-bottom: 1rem;
        }

        /* Action Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #f97316;
            color: white;
        }

        .btn-primary:hover {
            background: #ea580c;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: #64748b;
            margin-bottom: 2rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .pagination nav {
            display: flex;
            gap: 0.25rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #475569;
            font-size: 0.875rem;
            background: white;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
        }

        .pagination a:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .pagination .active span {
            background: #f97316;
            border-color: #f97316;
            color: white;
        }

        /* Fix Tailwind SVG arrow sizing */
        .pagination svg {
            width: 1rem !important;
            height: 1rem !important;
            display: inline-block;
        }

        /* Disabled pagination elements */
        .pagination .disabled span,
        .pagination [aria-disabled="true"] {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8fafc;
        }

        /* Pagination ellipsis */
        .pagination .dots {
            padding: 0.5rem;
            color: #94a3b8;
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
                <a href="{{ route('landlord.dashboard') }}" class="nav-item">
                    <i class="fas fa-home"></i> My Dashboard
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item">
                    <i class="fas fa-building"></i> My Properties
                    @if(isset($apartments) && $apartments->count() > 0)
                        <span class="badge-count">{{ $apartments->count() }}</span>
                    @endif
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item active">
                    <i class="fas fa-door-open"></i> My Units
                    @if(isset($units) && $units->count() > 0)
                        <span class="badge-count">{{ $units->count() }}</span>
                    @endif
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i> Tenants
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
                <div>
                    <h1>My Units</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">Manage all your rental units</p>
                </div>
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

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_units'] }}</div>
                    <div class="stat-label">Total Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['available_units'] }}</div>
                    <div class="stat-label">Available Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['occupied_units'] }}</div>
                    <div class="stat-label">Occupied Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($stats['monthly_revenue'], 0) }}</div>
                    <div class="stat-label">Monthly Revenue</div>
                </div>
            </div>

            <!-- Units Section -->
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">All Units</h2>
                        <p class="section-subtitle">View and manage your rental units across all properties</p>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div class="sort-dropdown">
                            <label style="margin-right: 0.5rem; font-size: 0.875rem; color: #64748b;">Sort by:</label>
                            <select id="unitSort" onchange="window.location.href='?sort=' + this.value" style="padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                                <option value="property_unit" {{ request('sort', 'property_unit') == 'property_unit' ? 'selected' : '' }}>Property → Unit Number</option>
                                <option value="property" {{ request('sort') == 'property' ? 'selected' : '' }}>Property Name</option>
                                <option value="unit_number" {{ request('sort') == 'unit_number' ? 'selected' : '' }}>Unit Number Only</option>
                                <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status (Available First)</option>
                                <option value="rent" {{ request('sort') == 'rent' ? 'selected' : '' }}>Rent (Highest First)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#generateUnitsModal">
                            <i class="fas fa-layer-group"></i> Generate Units
                        </button>
                        <a href="{{ route('landlord.create-unit') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Unit
                        </a>
                    </div>
                </div>

                @if($units->count() > 0)
                    <div class="units-list">
                        <div class="list-header">
                            <div class="list-column" style="flex: 1.5;">Unit Number</div>
                            <div class="list-column" style="flex: 1.5;">Property</div>
                            <div class="list-column">Type</div>
                            <div class="list-column text-center">Beds / Baths</div>
                            <div class="list-column text-center">Floor</div>
                            <div class="list-column text-center">Status</div>
                            <div class="list-column text-right">Rent/Month</div>
                            <div class="list-column text-center">Actions</div>
                        </div>
                        
                        @foreach($units as $unit)
                            <div class="list-row">
                                <div class="list-column" style="flex: 1.5;">
                                    <div style="font-weight: 600; color: #1e293b;">{{ $unit->unit_number }}</div>
                                </div>
                                <div class="list-column" style="flex: 1.5;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-building" style="color: #94a3b8; font-size: 0.75rem;"></i>
                                        <span>{{ $unit->apartment->name ?? 'Unknown' }}</span>
                                    </div>
                                </div>
                                <div class="list-column">
                                    <span style="color: #64748b;">{{ str_replace('_', ' ', ucfirst($unit->unit_type ?? 'N/A')) }}</span>
                                </div>
                                <div class="list-column text-center">
                                    <div style="display: flex; gap: 0.75rem; align-items: center; justify-content: center;">
                                        <span title="Bedrooms"><i class="fas fa-bed" style="color: #94a3b8; margin-right: 0.25rem;"></i>{{ $unit->bedrooms ?? 0 }}</span>
                                        <span title="Bathrooms"><i class="fas fa-bath" style="color: #94a3b8; margin-right: 0.25rem;"></i>{{ $unit->bathrooms ?? 1 }}</span>
                                    </div>
                                </div>
                                <div class="list-column text-center">
                                    <span style="color: #64748b;">{{ $unit->floor_number ?? 'N/A' }}</span>
                                </div>
                                <div class="list-column text-center">
                                    @php
                                        $statusColors = [
                                            'available' => ['bg' => '#dcfce7', 'text' => '#16a34a'],
                                            'occupied' => ['bg' => '#fee2e2', 'text' => '#dc2626'],
                                            'maintenance' => ['bg' => '#fef3c7', 'text' => '#d97706'],
                                        ];
                                        $color = $statusColors[$unit->status] ?? ['bg' => '#e2e8f0', 'text' => '#64748b'];
                                    @endphp
                                    <span style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                        {{ ucfirst($unit->status) }}
                                    </span>
                                </div>
                                <div class="list-column text-right">
                                    <span style="color: #0f172a; font-weight: 600;">₱{{ number_format($unit->rent_amount ?? 0, 0) }}</span>
                                </div>
                                <div class="list-column text-center">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <button onclick="editUnit({{ $unit->id }})" class="btn-icon" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="viewUnitDetails({{ $unit->id }})" class="btn-icon" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($units->hasPages())
                        <div class="pagination" style="margin-top: 1.5rem;">
                            {{ $units->appends(['sort' => request('sort')])->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <h3 class="empty-title">No Units Found</h3>
                        <p class="empty-text">
                            @if(request()->hasAny(['search', 'status', 'apartment']))
                                No units match your search criteria. Try adjusting your filters.
                            @else
                                You haven't added any units yet. Start by adding units to your properties.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'status', 'apartment']))
                            <a href="{{ route('landlord.units') }}" class="btn btn-primary">
                                <i class="fas fa-refresh"></i> Clear Filters
                            </a>
                        @else
                            <a href="{{ route('landlord.apartments') }}" class="btn btn-primary">
                                <i class="fas fa-building"></i> Go to Properties
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Generate Units Modal Logic
        document.addEventListener('DOMContentLoaded', function() {
            const propertySelect = document.getElementById('gen_property_id');
            const numFloorsInput = document.getElementById('gen_num_floors');
            const numUnitsInput = document.getElementById('gen_num_units');
            const unitsPerFloorInput = document.getElementById('gen_units_per_floor');
            const unitTypeSelect = document.getElementById('gen_unit_type');
            const bedroomsInput = document.getElementById('gen_bedrooms');
            const numberingPattern = document.getElementById('gen_numbering_pattern');
            const floorConfig = document.getElementById('gen_floor_config');

            // When property is selected, populate floor count
            if (propertySelect) {
                propertySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const floors = selectedOption.getAttribute('data-floors') || 1;
                    numFloorsInput.value = floors;
                    calculateUnitsPerFloor();
                });
            }

            // Auto-populate bedrooms based on unit type
            if (unitTypeSelect) {
                unitTypeSelect.addEventListener('change', function() {
                    const bedroomMap = {
                        'Studio': 0,
                        'One Bedroom': 1,
                        'Two Bedroom': 2,
                        'Three Bedroom': 3,
                        'Penthouse': 3
                    };
                    bedroomsInput.value = bedroomMap[this.value] || 0;
                });
            }

            // Toggle floor configuration visibility
            if (numberingPattern) {
                numberingPattern.addEventListener('change', function() {
                    if (this.value === 'floor_based') {
                        floorConfig.style.display = 'flex';
                    } else {
                        floorConfig.style.display = 'none';
                    }
                });
            }

            // Auto-calculate units per floor
            function calculateUnitsPerFloor() {
                if (numUnitsInput && numFloorsInput) {
                    const totalUnits = parseInt(numUnitsInput.value) || 0;
                    const floors = parseInt(numFloorsInput.value) || 1;
                    
                    if (totalUnits > 0 && floors > 0 && !unitsPerFloorInput.value) {
                        const perFloor = Math.ceil(totalUnits / floors);
                        unitsPerFloorInput.placeholder = `Auto: ${perFloor} units/floor`;
                    }
                }
            }

            if (numUnitsInput) {
                numUnitsInput.addEventListener('input', calculateUnitsPerFloor);
            }

            // Handle form submission
            const generateForm = document.getElementById('generateUnitsForm');
            if (generateForm) {
                generateForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('generateUnitsModal'));
                            modal.hide();
                            
                            // Show success message and reload
                            alert(data.message || 'Units generated successfully!');
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error generating units. Please try again.');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });

        function editUnit(unitId) {
            // Show the edit modal
            const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
            const modalTitle = document.getElementById('editUnitModalLabel');
            const modalContent = document.getElementById('editUnitContent');
            const saveBtn = document.getElementById('saveUnitBtn');
            const form = document.getElementById('editUnitForm');
            
            // Reset modal content
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading unit details...</p>
                </div>
            `;
            saveBtn.style.display = 'none';
            
            // Show modal
            modal.show();
            
            // Fetch unit data
            fetch(`/landlord/units/${unitId}/details`)
                .then(response => response.json())
                .then(data => {
                    modalTitle.textContent = `Edit Unit ${data.unit_number}`;
                    form.action = `/landlord/units/${unitId}`;
                    
                    // Generate form content
                    modalContent.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_unit_number" class="form-label">Unit Number *</label>
                                    <input type="text" class="form-control" id="edit_unit_number" name="unit_number" value="${data.unit_number}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_unit_type" class="form-label">Unit Type *</label>
                                    <select class="form-control" id="edit_unit_type" name="unit_type" required>
                                        <option value="studio" ${data.unit_type === 'studio' ? 'selected' : ''}>Studio</option>
                                        <option value="one_bedroom" ${data.unit_type === 'one_bedroom' ? 'selected' : ''}>One Bedroom</option>
                                        <option value="two_bedroom" ${data.unit_type === 'two_bedroom' ? 'selected' : ''}>Two Bedroom</option>
                                        <option value="three_bedroom" ${data.unit_type === 'three_bedroom' ? 'selected' : ''}>Three Bedroom</option>
                                        <option value="penthouse" ${data.unit_type === 'penthouse' ? 'selected' : ''}>Penthouse</option>
                                    </select>
                                    <small class="form-text text-muted">Bedrooms will auto-update based on selection</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_rent_amount" class="form-label">Monthly Rent (₱) *</label>
                                    <input type="number" class="form-control" id="edit_rent_amount" name="rent_amount" value="${data.rent_amount}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status *</label>
                                    <select class="form-control" id="edit_status" name="status" required>
                                        <option value="available" ${data.status === 'available' ? 'selected' : ''}>Available</option>
                                        <option value="occupied" ${data.status === 'occupied' ? 'selected' : ''}>Occupied</option>
                                        <option value="maintenance" ${data.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_leasing_type" class="form-label">Leasing Type *</label>
                                    <select class="form-control" id="edit_leasing_type" name="leasing_type" required>
                                        <option value="separate" ${data.leasing_type === 'separate' ? 'selected' : ''}>Separate (Utilities not included)</option>
                                        <option value="inclusive" ${data.leasing_type === 'inclusive' ? 'selected' : ''}>Inclusive (Utilities included)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_bedrooms" class="form-label">Bedrooms *</label>
                                    <input type="number" class="form-control" id="edit_bedrooms" name="bedrooms" value="${data.bedrooms}" min="0" required readonly>
                                    <small class="form-text text-muted">Auto-filled based on unit type</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_bathrooms" class="form-label">Bathrooms *</label>
                                    <input type="number" class="form-control" id="edit_bathrooms" name="bathrooms" value="${data.bathrooms}" min="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="edit_is_furnished" name="is_furnished" value="1" ${data.is_furnished ? 'checked' : ''}>
                                <label class="form-check-label" for="edit_is_furnished">
                                    Furnished Unit
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Amenities</label>
                            <div class="row">
                                ${generateAmenitiesCheckboxes(data.amenities || [])}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3">${data.description || ''}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="2">${data.notes || ''}</textarea>
                        </div>
                    `;
                    
                    // Show save button
                    saveBtn.style.display = 'inline-block';
                    
                    // Add event listener for unit type change to auto-populate bedrooms
                    const editUnitTypeSelect = document.getElementById('edit_unit_type');
                    const editBedroomsInput = document.getElementById('edit_bedrooms');
                    
                    if (editUnitTypeSelect && editBedroomsInput) {
                        editUnitTypeSelect.addEventListener('change', function() {
                            const unitType = this.value;
                            let bedroomCount = 0;
                            
                            switch(unitType) {
                                case 'studio':
                                    bedroomCount = 0;
                                    break;
                                case 'one_bedroom':
                                    bedroomCount = 1;
                                    break;
                                case 'two_bedroom':
                                    bedroomCount = 2;
                                    break;
                                case 'three_bedroom':
                                    bedroomCount = 3;
                                    break;
                                case 'penthouse':
                                    bedroomCount = 3; // Default for penthouse
                                    break;
                                default:
                                    bedroomCount = 0;
                            }
                            
                            editBedroomsInput.value = bedroomCount;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading unit details:', error);
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Error loading unit details. Please try again.
                        </div>
                    `;
                });
        }
        
        function generateAmenitiesCheckboxes(amenities) {
            const allAmenities = [
                { value: 'aircon', label: 'Air Conditioning' },
                { value: 'heating', label: 'Heating' },
                { value: 'balcony', label: 'Balcony' },
                { value: 'parking', label: 'Parking' },
                { value: 'gym', label: 'Gym Access' },
                { value: 'pool', label: 'Pool Access' },
                { value: 'wifi', label: 'WiFi' },
                { value: 'laundry', label: 'Laundry' }
            ];
            
            return allAmenities.map(amenity => {
                const checked = amenities.includes(amenity.value) ? 'checked' : '';
                return `
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_amenity_${amenity.value}" name="amenities[]" value="${amenity.value}" ${checked}>
                            <label class="form-check-label" for="edit_amenity_${amenity.value}">
                                ${amenity.label}
                            </label>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Handle form submission
        document.addEventListener('DOMContentLoaded', function() {
            const editForm = document.getElementById('editUnitForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitBtn = document.getElementById('saveUnitBtn');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('editUnitModal')).hide();
                            
                            // Show success message and reload page
                            alert('Unit updated successfully!');
                            location.reload();
                        } else {
                            // Show error message
                            alert(data.message || 'An error occurred while updating the unit.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the unit.');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });

        function viewUnitDetails(unitId) {
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('unitDetailsModal'));
            const modalTitle = document.getElementById('unitDetailsModalLabel');
            const modalContent = document.getElementById('unitDetailsContent');
            const editBtn = document.getElementById('editUnitBtn');
            
            // Reset modal content
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading unit details...</p>
                </div>
            `;
            editBtn.style.display = 'none';
            
            modal.show();
            
            // Fetch unit details
            fetch(`/landlord/units/${unitId}/details`)
                .then(response => response.json())
                .then(data => {
                    modalTitle.textContent = `Unit ${data.unit_number} - Details`;
                    
                    // Create the details HTML
                    modalContent.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Unit Information</h6>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Unit Number</label>
                                    <p class="mb-1">${data.unit_number}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Property</label>
                                    <p class="mb-1">${data.apartment_name}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Unit Type</label>
                                    <p class="mb-1">${data.unit_type ? data.unit_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not specified'}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Monthly Rent</label>
                                    <p class="mb-1 text-success fw-bold">₱${Number(data.rent_amount).toLocaleString()}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Status</label>
                                    <p class="mb-1">
                                        <span class="badge bg-${data.status === 'occupied' ? 'success' : data.status === 'available' ? 'warning' : 'danger'}">
                                            ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Unit Specifications</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2 mb-3">
                                            <h4 class="text-primary mb-0">${data.bedrooms || 0}</h4>
                                            <small class="text-muted">Bedrooms</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2 mb-3">
                                            <h4 class="text-info mb-0">${data.bathrooms || 0}</h4>
                                            <small class="text-muted">Bathrooms</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Max Occupants</label>
                                    <p class="mb-1">${data.max_occupants || 'Not specified'}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Floor Number</label>
                                    <p class="mb-1">${data.floor_number || 'Not specified'}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Furnished</label>
                                    <p class="mb-1">
                                        <span class="badge bg-${data.is_furnished ? 'success' : 'secondary'}">
                                            ${data.is_furnished ? 'Yes' : 'No'}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        ${data.current_tenant ? `
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Current Tenant</h6>
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Name:</strong> ${data.current_tenant.name}<br>
                                            <strong>Email:</strong> ${data.current_tenant.email}<br>
                                            ${data.current_tenant.phone ? `<strong>Phone:</strong> ${data.current_tenant.phone}<br>` : ''}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Lease Start:</strong> ${data.current_tenant.lease_start}<br>
                                            <strong>Lease End:</strong> ${data.current_tenant.lease_end}<br>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="/landlord/tenant-assignments/${data.current_tenant.assignment_id}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Assignment Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${data.amenities && data.amenities.length > 0 ? `
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Amenities</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    ${data.amenities.map(amenity => `<span class="badge bg-soft-primary text-primary"><i class="fas fa-check me-1"></i>${amenity}</span>`).join('')}
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${data.description ? `
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Description</h6>
                                <p class="text-muted">${data.description}</p>
                            </div>
                        </div>
                        ` : ''}
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Quick Actions</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    ${data.status === 'available' ? `
                                        <a href="/landlord/tenant-assignments?unit_id=${data.id}" class="btn btn-success btn-sm">
                                            <i class="fas fa-user-plus"></i> Assign Tenant
                                        </a>
                                    ` : ''}
                                    <a href="/landlord/tenant-assignments?unit_id=${data.id}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-history"></i> Assignment History
                                    </a>
                                    <a href="/landlord/units/${data.apartment_id}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-building"></i> View Property
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Show edit button and set up click handler
                    editBtn.style.display = 'inline-block';
                    editBtn.onclick = function() {
                        editUnit(data.id);
                    };
                })
                .catch(error => {
                    console.error('Error fetching unit details:', error);
                    modalContent.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-danger">Error Loading Details</h5>
                            <p class="text-muted">Failed to load unit details. Please try again.</p>
                            <button class="btn btn-primary" onclick="viewUnitDetails(${unitId})">Retry</button>
                        </div>
                    `;
                });
        }

        // assignTenant and vacateUnit removed; tenant actions handled in Tenant Assignments tab

        function deleteUnit(unitId, unitNumber) {
            if (confirm(`Are you sure you want to delete Unit ${unitNumber}? This action cannot be undone.\n\nNote: You cannot delete units with active tenant assignments.`)) {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/landlord/units/${unitId}`;
                
                // CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                // DELETE method
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

    </script>

    <!-- Unit Details Modal -->
    <div class="modal fade" id="unitDetailsModal" tabindex="-1" aria-labelledby="unitDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitDetailsModalLabel">Unit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="unitDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading unit details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editUnitBtn" style="display: none;">
                        <i class="fas fa-edit"></i> Edit Unit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Units Modal -->
    <div class="modal fade" id="generateUnitsModal" tabindex="-1" aria-labelledby="generateUnitsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateUnitsModalLabel">
                        <i class="fas fa-layer-group"></i> Generate Units
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="generateUnitsForm" method="POST" action="{{ route('landlord.bulk-generate-units') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Automatically create multiple units for a property with your configured settings.
                        </div>

                        <!-- Select Property -->
                        <div class="mb-3">
                            <label for="gen_property_id" class="form-label">Select Property <span class="text-danger">*</span></label>
                            <select class="form-control" id="gen_property_id" name="apartment_id" required>
                                <option value="">-- Select Property --</option>
                                @foreach($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" data-floors="{{ $apartment->floors ?? 1 }}">
                                        {{ $apartment->name }} ({{ $apartment->units->count() }} units)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Number of Units -->
                        <div class="mb-3">
                            <label for="gen_num_units" class="form-label">Number of Units to Generate <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="gen_num_units" name="num_units" min="1" max="500" required>
                            <small class="text-muted">Maximum 500 units per generation</small>
                        </div>

                        <!-- Unit Configuration -->
                        <h6 class="mt-4 mb-3">Unit Configuration</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gen_unit_type" class="form-label">Default Unit Type</label>
                                <select class="form-control" id="gen_unit_type" name="default_unit_type">
                                    <option value="Studio">Studio</option>
                                    <option value="One Bedroom">One Bedroom</option>
                                    <option value="Two Bedroom" selected>Two Bedroom</option>
                                    <option value="Three Bedroom">Three Bedroom</option>
                                    <option value="Penthouse">Penthouse</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gen_rent" class="form-label">Default Rent Amount (₱)</label>
                                <input type="number" class="form-control" id="gen_rent" name="default_rent" step="100" min="0" placeholder="15000">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gen_bedrooms" class="form-label">Default Bedrooms</label>
                                <input type="number" class="form-control" id="gen_bedrooms" name="default_bedrooms" min="0" value="2" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gen_bathrooms" class="form-label">Default Bathrooms</label>
                                <input type="number" class="form-control" id="gen_bathrooms" name="default_bathrooms" min="0" step="0.5" value="1">
                            </div>
                        </div>

                        <!-- Numbering Pattern -->
                        <div class="mb-3">
                            <label for="gen_numbering_pattern" class="form-label">Unit Numbering Pattern</label>
                            <select class="form-control" id="gen_numbering_pattern" name="numbering_pattern">
                                <option value="floor_based" selected>Floor-based (101, 102, 201, 202...)</option>
                                <option value="sequential">Sequential (1, 2, 3, 4...)</option>
                                <option value="letter_number">Letter-Number (A1, A2, B1, B2...)</option>
                            </select>
                        </div>

                        <!-- Floor Configuration -->
                        <div id="gen_floor_config" class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gen_num_floors" class="form-label">Number of Floors</label>
                                <input type="number" class="form-control" id="gen_num_floors" name="num_floors" min="1" value="1" readonly>
                                <small class="text-muted">From property details</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gen_units_per_floor" class="form-label">Units per Floor</label>
                                <input type="number" class="form-control" id="gen_units_per_floor" name="units_per_floor" min="1" placeholder="Auto-calculated">
                                <small class="text-muted">Leave blank to auto-distribute</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-layer-group"></i> Generate Units
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Unit Modal -->
    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUnitModalLabel">Edit Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUnitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body" id="editUnitContent">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading unit details...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveUnitBtn" style="display: none;">
                            <i class="fas fa-save"></i> Update Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html> 