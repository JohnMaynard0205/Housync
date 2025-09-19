<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - Housesync</title>
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
            border-left: 4px solid #3b82f6;
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
            grid-template-columns: 2fr 1fr 1fr;
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
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Property Cards Grid */
        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .property-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .property-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: #3b82f6;
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
            margin-bottom: 0.25rem;
        }

        .property-landlord {
            font-size: 0.875rem;
            color: #64748b;
        }

        .property-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #d1fae5;
            color: #059669;
        }

        .property-info {
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
            color: #3b82f6;
        }

        .property-stats {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        .stat-item-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
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
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            text-decoration: none;
            color: #374151;
            font-size: 0.875rem;
        }

        .pagination a:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .pagination .active span {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
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
                <a href="{{ route('super-admin.dashboard') }}" class="nav-item">
                    <i class="fas fa-home"></i> My Dashboard
                </a>
                <a href="{{ route('super-admin.pending-landlords') }}" class="nav-item">
                    <i class="fas fa-user-clock"></i> Pending Approvals
                </a>
                <a href="{{ route('super-admin.users') }}" class="nav-item">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="{{ route('super-admin.apartments') }}" class="nav-item active">
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
                <div>
                    <h1>Property Management</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">View and manage all properties in the system</p>
                </div>
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

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ \App\Models\Apartment::count() }}</div>
                    <div class="stat-label">Total Properties</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ \App\Models\Unit::count() }}</div>
                    <div class="stat-label">Total Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ \App\Models\Unit::where('status', 'occupied')->count() }}</div>
                    <div class="stat-label">Occupied Units</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ \App\Models\User::where('role', 'landlord')->where('status', 'approved')->count() }}</div>
                    <div class="stat-label">Active Landlords</div>
                </div>
            </div>

            <!-- Properties Section -->
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">All Properties</h2>
                        <p class="section-subtitle">View properties from all landlords in the system</p>
                    </div>
                </div>

                <!-- Search and Filters -->
                <form method="GET" action="{{ route('super-admin.apartments') }}">
                    <div class="filters-section">
                        <div class="form-group">
                            <label class="form-label">Search Properties</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by property name, address, or landlord..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Landlord</label>
                            <select name="landlord" class="form-control">
                                <option value="">All Landlords</option>
                                @foreach(\App\Models\User::where('role', 'landlord')->where('status', 'approved')->get() as $landlord)
                                    <option value="{{ $landlord->id }}" {{ request('landlord') == $landlord->id ? 'selected' : '' }}>
                                        {{ $landlord->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>

                @php
                    $apartments = \App\Models\Apartment::with(['landlord', 'units'])
                        ->when(request('search'), function($query) {
                            $search = request('search');
                            $query->where(function($q) use ($search) {
                                $q->where('name', 'like', '%' . $search . '%')
                                  ->orWhere('address', 'like', '%' . $search . '%')
                                  ->orWhereHas('landlord', function($subQuery) use ($search) {
                                      $subQuery->where('name', 'like', '%' . $search . '%');
                                  });
                            });
                        })
                        ->when(request('landlord'), function($query) {
                            $query->where('landlord_id', request('landlord'));
                        })
                        ->latest()
                        ->paginate(12);
                @endphp

                @if($apartments->count() > 0)
                    <div class="properties-grid">
                        @foreach($apartments as $apartment)
                            <div class="property-card">
                                <div class="property-header">
                                    <div>
                                        <h3 class="property-title">{{ $apartment->name }}</h3>
                                        <p class="property-landlord">
                                            <i class="fas fa-user"></i> {{ $apartment->landlord->name ?? 'Unknown Landlord' }}
                                        </p>
                                    </div>
                                    <span class="property-status status-active">Active</span>
                                </div>

                                <div class="property-info">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span title="{{ $apartment->address }}">{{ Str::limit($apartment->address ?? 'No address', 30) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-door-open"></i>
                                        <span>{{ $apartment->units->count() }} Units</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $apartment->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $apartment->units->where('status', 'occupied')->count() }} Occupied</span>
                                    </div>
                                </div>

                                <div class="property-stats">
                                    <div class="stat-item">
                                        <div class="stat-item-value">{{ $apartment->units->count() }}</div>
                                        <div class="stat-item-label">Total Units</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-item-value">{{ $apartment->units->where('status', 'occupied')->count() }}</div>
                                        <div class="stat-item-label">Occupied</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-item-value">
                                            @if($apartment->units->count() > 0)
                                                {{ round(($apartment->units->where('status', 'occupied')->count() / $apartment->units->count()) * 100) }}%
                                            @else
                                                0%
                                            @endif
                                        </div>
                                        <div class="stat-item-label">Occupancy</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-item-value">₱{{ number_format($apartment->units->where('status', 'occupied')->sum('rent_amount') ?? 0, 0) }}</div>
                                        <div class="stat-item-label">Revenue</div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <a href="#" class="btn btn-primary btn-sm" onclick="viewProperty({{ $apartment->id }})">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <a href="#" class="btn btn-secondary btn-sm" onclick="viewUnits({{ $apartment->id }})">
                                        <i class="fas fa-door-open"></i> View Units
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($apartments->hasPages())
                        <div class="pagination">
                            {{ $apartments->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="empty-title">No Properties Found</h3>
                        <p class="empty-text">
                            @if(request()->hasAny(['search', 'landlord']))
                                No properties match your search criteria. Try adjusting your filters.
                            @else
                                No properties have been added to the system yet.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'landlord']))
                            <a href="{{ route('super-admin.apartments') }}" class="btn btn-primary">
                                <i class="fas fa-refresh"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function viewProperty(apartmentId) {
            // You can implement property details modal or redirect to detail page
            alert('Property details for ID: ' + apartmentId + '\nThis would show detailed property information.');
        }

        function viewUnits(apartmentId) {
            // You can implement units view modal or redirect to units page
            alert('Units view for property ID: ' + apartmentId + '\nThis would show all units in this property.');
        }
    </script>
</body>
</html> 