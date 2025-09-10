<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Property - Housesync</title>
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
            text-decoration: none;
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

        /* Page Section */
        .page-section {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .section-subtitle {
            color: #64748b;
            margin: 0.5rem 0 0 0;
        }

        /* Property Cards */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .property-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .property-card:hover {
            border-color: #f97316;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
            transform: translateY(-2px);
        }

        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .property-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .property-id {
            background: #f1f5f9;
            color: #64748b;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .property-info {
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .info-item i {
            width: 16px;
            margin-right: 0.5rem;
            color: #f97316;
        }

        .property-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            text-align: center;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 0.5rem;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            display: block;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .select-btn {
            width: 100%;
            padding: 0.75rem;
            background: #f97316;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .select-btn:hover {
            background: #ea580c;
            color: white;
            text-decoration: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: #64748b;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #f97316;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn:hover {
            background: #ea580c;
            color: white;
            text-decoration: none;
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

            .properties-grid {
                grid-template-columns: 1fr;
            }
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
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item active">
                    <i class="fas fa-door-open"></i> My Units
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
                <h1>Add New Unit</h1>
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

            <!-- Page Section -->
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Select Property</h2>
                        <p class="section-subtitle">Choose which property you want to add a unit to</p>
                    </div>
                </div>

                @if($apartments->count() > 0)
                    <div class="properties-grid">
                        @foreach($apartments as $apartment)
                            <div class="property-card" onclick="selectProperty({{ $apartment->id }})">
                                <div class="property-header">
                                    <h3 class="property-title">{{ $apartment->name }}</h3>
                                    <span class="property-id">#{{ $apartment->id }}</span>
                                </div>

                                <div class="property-info">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $apartment->address }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $apartment->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-building"></i>
                                        <span>{{ $apartment->units->count() }} Units</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $apartment->getOccupiedUnitsCount() }} Occupied</span>
                                    </div>
                                </div>

                                <div class="property-stats">
                                    <div class="stat-item">
                                        <span class="stat-value">{{ $apartment->units->count() }}</span>
                                        <span class="stat-label">Total Units</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">{{ $apartment->getAvailableUnitsCount() }}</span>
                                        <span class="stat-label">Available</span>
                                    </div>
                                </div>

                                <a href="{{ route('landlord.create-unit-for-apartment', $apartment->id) }}" class="select-btn">
                                    <i class="fas fa-plus"></i> Add Unit to This Property
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="empty-title">No Properties Found</h3>
                        <p class="empty-text">You need to create a property first before you can add units.</p>
                        <a href="{{ route('landlord.create-apartment') }}" class="btn">
                            <i class="fas fa-plus"></i> Create Your First Property
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function selectProperty(apartmentId) {
            window.location.href = `/landlord/apartments/${apartmentId}/units/create`;
        }
    </script>
</body>
</html> 