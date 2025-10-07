<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Units Management - HouSync</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/units.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .user-profile {
            position: relative;
            cursor: pointer;
        }
        .profile-dropdown {
            position: absolute;
            top: 110%;
            right: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            border-radius: 8px;
            min-width: 180px;
            z-index: 9999;
            padding: 8px 0;
            display: none;
        }
        .profile-dropdown.show, .user-profile .profile-dropdown.show {
            display: block !important;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #374151;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s;
        }
        .dropdown-item:hover {
            background: #f3f4f6;
        }
        .dropdown-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 6px 0;
        }
        .dashboard-container, .main-content, .header-right {
            overflow: visible !important;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-content">
                <div class="nav-items-container">
                    <div class="nav-item" onclick="window.location.href='{{ route('dashboard') }}'">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </div>
                    <div class="nav-item active">
                        <i class="fas fa-building"></i>
                        <span>Units</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('tenants') }}'">
                        <i class="fas fa-users"></i>
                        <span>Tenants</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('billing') }}'">
                        <i class="fas fa-credit-card"></i>
                        <span>Billing</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('messages') }}'">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('security') }}'">
                        <i class="fas fa-shield-alt"></i>
                        <span>Security</span>
                    </div>
                </div>
                
                <!-- Logout Button at Bottom -->
                <div class="nav-bottom">
                    <div class="nav-item logout-item" onclick="handleLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="header-center">
                    <h1 class="app-title">HouSync</h1>
                </div>
                <div class="header-right">
                    <button class="header-btn">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="header-btn">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="user-profile" id="userProfile">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Ann Lee" class="profile-avatar">
                        <span class="profile-name">Ann Lee</span>
                        <i class="fas fa-chevron-down"></i>
                        <!-- Dropdown Menu -->
                        <div class="profile-dropdown" id="profileDropdown" style="display: none;">
                            <a href="#" class="dropdown-item"><i class="fas fa-user"></i> My Profile</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-cog"></i> Account Settings</a>
                            <a href="{{ route('dashboard') }}" class="dropdown-item"><i class="fas fa-th-large"></i> Dashboard</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-history"></i> My Activity</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-bell"></i> Notifications</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-question-circle"></i> Help & Support</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item" onclick="handleLogout(); return false;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Units Content -->
            <div class="units-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-title-section">
                        <h2>Units Management</h2>
                        <p>Manage all property units, tenants, and availability</p>
                    </div>
                    <div class="page-actions">
                        <button class="btn btn-outline" onclick="openFilterModal()">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                        <button class="btn btn-primary" onclick="openAddUnitModal()">
                            <i class="fas fa-plus"></i>
                            Add Unit
                        </button>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="units-summary">
                    <div class="summary-card">
                        <div class="summary-icon total">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Total Units</h3>
                            <span class="summary-value" id="totalUnits">{{ $totalUnits }}</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon occupied">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Occupied</h3>
                            <span class="summary-value" id="occupiedUnits">{{ $occupiedUnits }}</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon available">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Available</h3>
                            <span class="summary-value" id="availableUnits">{{ $availableUnits }}</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon maintenance">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Maintenance</h3>
                            <span class="summary-value" id="maintenanceUnits">{{ $maintenanceUnits }}</span>
                        </div>
                    </div>
                </div>

                <!-- Units Grid -->
                <div class="units-section">
                    <div class="section-header">
                        <h3>All Units</h3>
                        <div class="view-controls">
                            <button class="view-btn active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    <div class="units-grid" id="unitsGrid">
                        @foreach($units as $unit)
                        <div class="unit-card {{ $unit->status }}">
                            <div class="unit-header">
                                <div class="unit-number">{{ $unit->unit_number }}</div>
                                <div class="unit-status {{ $unit->status }}">{{ ucfirst($unit->status) }}</div>
                            </div>
                            <div class="unit-details">
                                <div class="unit-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span>Landlord: {{ $unit->apartment ? $unit->apartment->landlord->name : 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span>Tenants: {{ $unit->tenant_count }} {{ $unit->tenant_count == 1 ? 'person' : 'people' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-home"></i>
                                        <span>Type: {{ $unit->unit_type }}</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>Rent: {{ $unit->formatted_rent }}/month</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-file-invoice"></i>
                                        <span>{{ $unit->leasing_type_label }}</span>
                                    </div>
                                </div>
                                <div class="unit-actions">
                                    @if($unit->status === 'available')
                                        <button class="action-btn primary">Add Tenant</button>
                                    @else
                                        <button class="action-btn">View Details</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Filter Modal -->
    <div id="filterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Filter Units</h3>
                <span class="close" onclick="closeFilterModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="searchFilter">Search</label>
                            <input type="text" id="searchFilter" name="search" placeholder="Search by unit number, owner, or type">
                        </div>
                        <div class="form-group">
                            <label for="statusFilter">Status</label>
                            <select id="statusFilter" name="status">
                                <option value="">All Statuses</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="unitTypeFilter">Unit Type</label>
                            <select id="unitTypeFilter" name="unit_type">
                                <option value="">All Types</option>
                                @foreach($unitTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bedroomsFilter">Bedrooms</label>
                            <select id="bedroomsFilter" name="bedrooms">
                                <option value="">Any</option>
                                <option value="0">Studio</option>
                                <option value="1">1 Bedroom</option>
                                <option value="2">2 Bedrooms</option>
                                <option value="3">3+ Bedrooms</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="minRentFilter">Min Rent</label>
                            <input type="number" id="minRentFilter" name="min_rent" placeholder="₱0">
                        </div>
                        <div class="form-group">
                            <label for="maxRentFilter">Max Rent</label>
                            <input type="number" id="maxRentFilter" name="max_rent" placeholder="₱50,000">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="furnishedFilter" name="is_furnished" value="1">
                                Furnished Only
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="leasingTypeFilter">Leasing Type</label>
                            <select id="leasingTypeFilter" name="leasing_type">
                                <option value="">All Types</option>
                                <option value="separate">Separate Bills</option>
                                <option value="inclusive">All Inclusive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="clearFilters()">Clear Filters</button>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
            </div>
        </div>
    </div>

    <!-- Add Unit Modal -->
    <div id="addUnitModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Add New Unit</h3>
                <span class="close" onclick="closeAddUnitModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addUnitForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="unitNumber">Unit Number *</label>
                            <input type="text" id="unitNumber" name="unit_number" required placeholder="e.g., Unit 09">
                        </div>
                        <div class="form-group">
                            <label for="apartmentId">Apartment *</label>
                            <select id="apartmentId" name="apartment_id" required>
                                <option value="">Select Apartment</option>
                                <!-- Apartment options will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="unitType">Unit Type *</label>
                            <select id="unitType" name="unit_type" required>
                                <option value="">Select Type</option>
                                <option value="Studio">Studio</option>
                                <option value="1 Bedroom">1 Bedroom</option>
                                <option value="2 Bedroom">2 Bedroom</option>
                                <option value="3 Bedroom">3 Bedroom</option>
                                <option value="Penthouse">Penthouse</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rentAmount">Monthly Rent *</label>
                            <input type="number" id="rentAmount" name="rent_amount" required placeholder="8500" step="0.01">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bedrooms">Bedrooms *</label>
                            <input type="number" id="bedrooms" name="bedrooms" required min="0" max="10" value="1">
                        </div>
                        <div class="form-group">
                            <label for="bathrooms">Bathrooms *</label>
                            <input type="number" id="bathrooms" name="bathrooms" required min="1" max="10" value="1">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="floorArea">Floor Area (sq.m)</label>
                            <input type="number" id="floorArea" name="floor_area" placeholder="35.5" step="0.1">
                        </div>
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="available" selected>Available</option>
                                <option value="maintenance">Under Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="leasingType">Leasing Type *</label>
                            <select id="leasingType" name="leasing_type" required>
                                <option value="">Select Leasing Type</option>
                                <option value="separate">Separate Bills (Tenant pays rent + utilities separately)</option>
                                <option value="inclusive">All Inclusive (Rent includes all utilities and bills)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tenantCount">Current Tenant Count</label>
                            <input type="number" id="tenantCount" name="tenant_count" min="0" max="20" value="0">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="isFurnished" name="is_furnished" value="1">
                                Furnished
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief description of the unit"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Additional notes or comments"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeAddUnitModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUnit()">Save Unit</button>
            </div>
        </div>
    </div>

    <script>
        // Enhanced menu toggle functionality with persistence
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            // Restore sidebar state from localStorage
            const sidebarState = localStorage.getItem('sidebarExpanded');
            if (sidebarState === 'true') {
                sidebar.classList.add('collapsed');
            }
            
            // Toggle sidebar and save state
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                const isExpanded = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarExpanded', isExpanded);
            });
        });
        
        // Logout functionality
        function handleLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Create a form to submit the logout request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        }

        // View toggle functionality
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const view = this.dataset.view;
                const grid = document.getElementById('unitsGrid');
                
                if (view === 'list') {
                    grid.classList.add('list-view');
                } else {
                    grid.classList.remove('list-view');
                }
            });
        });

        // CSRF token setup for AJAX requests
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        // Filter Modal Functions
        function openFilterModal() {
            document.getElementById('filterModal').style.display = 'block';
        }

        function closeFilterModal() {
            document.getElementById('filterModal').style.display = 'none';
        }

        function clearFilters() {
            document.getElementById('filterForm').reset();
            applyFilters();
        }

        function applyFilters() {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            
            // Redirect to the units page with filter parameters
            window.location.href = '{{ route("units") }}?' + params.toString();
        }

        // Add Unit Modal Functions
        function openAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'block';
        }

        function closeAddUnitModal() {
            document.getElementById('addUnitModal').style.display = 'none';
            document.getElementById('addUnitForm').reset();
        }

        function saveUnit() {
            const form = document.getElementById('addUnitForm');
            const formData = new FormData(form);
            
            // Convert FormData to regular object
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Add CSRF token
            data._token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route("units.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Unit created successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to create unit'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the unit');
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const filterModal = document.getElementById('filterModal');
            const addUnitModal = document.getElementById('addUnitModal');
            
            if (event.target === filterModal) {
                closeFilterModal();
            }
            if (event.target === addUnitModal) {
                closeAddUnitModal();
            }
        });

        // Profile dropdown logic
        document.addEventListener('DOMContentLoaded', function() {
            const userProfile = document.getElementById('userProfile');
            const profileDropdown = document.getElementById('profileDropdown');
            let dropdownOpen = false;
            if (userProfile && profileDropdown && !userProfile.classList.contains('dropdown-initialized')) {
                userProfile.classList.add('dropdown-initialized');
                userProfile.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownOpen = !dropdownOpen;
                    profileDropdown.classList.toggle('show', dropdownOpen);
                    console.log('Profile dropdown toggled:', dropdownOpen);
                });
                document.addEventListener('click', function() {
                    if (dropdownOpen) {
                        profileDropdown.classList.remove('show');
                        dropdownOpen = false;
                    }
                });
            }
        });
    </script>
    
    
</body>
</html> 