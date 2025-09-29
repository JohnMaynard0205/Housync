<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HouSync</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border-radius: 8px;
            min-width: 180px;
            z-index: 1000;
            padding: 8px 0;
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-content">
                <div class="nav-items-container">
                    <div class="nav-item active">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('units') }}'">
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
                        <span>Security Logs</span>
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

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon occupied">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Occupied Units</h3>
                            <div class="stat-value">
                                <span class="indicator positive">+</span>
                                <span class="value">23/30</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon available">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Available Units</h3>
                            <div class="stat-value">
                                <span class="indicator positive">+</span>
                                <span class="value">7</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon tenants">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Active Tenants</h3>
                            <div class="stat-value">
                                <span class="indicator positive">+</span>
                                <span class="value">28</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon payments">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Payments Received</h3>
                            <div class="stat-value">
                                <span class="indicator positive">+</span>
                                <span class="value">₱5200</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Dashboard Area -->
                <div class="dashboard-main">
                    <!-- Left Section - Data Table -->
                    <div class="data-section">
                        <div class="section-header">
                            <h2>Month of July</h2>
                            <div class="action-buttons">
                                <button class="btn btn-outline">Export to excel</button>
                            </div>
                        </div>

                        <div class="data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Unit Number</th>
                                        <th>Rent</th>
                                        <th>Water Rate</th>
                                        <th>Electricity Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Paid</td>
                                        <td>₱600</td>
                                        <td>₱1547.93</td>
                                        <td><span class="status occupied">Occupied</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Paid</td>
                                        <td>₱350.6</td>
                                        <td>₱824</td>
                                        <td><span class="status occupied">Occupied</span></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td class="overdue">Overdue</td>
                                        <td>₱679.12</td>
                                        <td>₱947</td>
                                        <td><span class="status occupied">Occupied</span></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td class="overdue">Overdue</td>
                                        <td>₱679.12</td>
                                        <td>₱947</td>
                                        <td><span class="status occupied">Occupied</span></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td class="overdue">Overdue</td>
                                        <td>₱679.12</td>
                                        <td>₱947</td>
                                        <td><span class="status occupied">Occupied</span></td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td class="overdue">Overdue</td>
                                        <td>₱679.12</td>
                                        <td>₱947</td>
                                        <td><span class="status occupied">Occupied</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right Section - Recent Activity -->
                    <div class="activity-section">
                        <h3>Recent Tenant Activity</h3>
                        
                        <div class="activity-table">
                            <div class="activity-header">
                                <div class="col">Tenant Name</div>
                                <div class="col">Activity</div>
                                <div class="col">Status</div>
                            </div>
                            <div class="activity-row">
                                <div class="col">Juan Karlos</div>
                                <div class="col">Paid Rent</div>
                                <div class="col"><span class="status confirmed">Confirmed</span></div>
                            </div>
                            <div class="activity-row">
                                <div class="col">Ana Reyes</div>
                                <div class="col">Uploaded Proof</div>
                                <div class="col"><span class="status pending">Pending Review</span></div>
                            </div>
                            <div class="activity-row">
                                <div class="col">Jane Doe</div>
                                <div class="col">Maintenance Report</div>
                                <div class="col"><span class="status pending">Pending Review</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Enhanced JavaScript for menu toggle with persistence
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

        // Profile dropdown logic
        document.addEventListener('DOMContentLoaded', function() {
            const userProfile = document.getElementById('userProfile');
            const profileDropdown = document.getElementById('profileDropdown');
            let dropdownOpen = false;
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownOpen = !dropdownOpen;
                profileDropdown.style.display = dropdownOpen ? 'block' : 'none';
            });
            document.addEventListener('click', function() {
                if (dropdownOpen) {
                    profileDropdown.style.display = 'none';
                    dropdownOpen = false;
                }
            });
        });
    </script>
    
    
</body>
</html> 