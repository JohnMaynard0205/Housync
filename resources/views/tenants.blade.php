<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants Management - HouSync</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tenants.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <div class="nav-item" onclick="window.location.href='{{ route('units') }}'">
                        <i class="fas fa-building"></i>
                        <span>Units</span>
                    </div>
                    <div class="nav-item active">
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

            <!-- Tenants Content -->
            <div class="tenants-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-title-section">
                        <h2>Tenants Management</h2>
                        <p>Manage tenant profiles, payments, and lease agreements</p>
                    </div>
                    <div class="page-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search tenants...">
                        </div>
                        <button class="btn btn-outline">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Add Tenant
                        </button>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="tenants-summary">
                    <div class="summary-card">
                        <div class="summary-icon total">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Total Tenants</h3>
                            <span class="summary-value">28</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon active">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Active Leases</h3>
                            <span class="summary-value">25</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon paid">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Paid This Month</h3>
                            <span class="summary-value">22</span>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-icon overdue">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Overdue Payments</h3>
                            <span class="summary-value">3</span>
                        </div>
                    </div>
                </div>

                <!-- Tenants Section -->
                <div class="tenants-section">
                    <div class="section-header">
                        <h3>All Tenants</h3>
                        <div class="view-controls">
                            <button class="view-btn active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    <div class="tenants-grid" id="tenantsGrid">
                        <!-- Tenant Card 1 -->
                        <div class="tenant-card">
                            <div class="tenant-header">
                                <div class="tenant-avatar">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Juan Karlos">
                                </div>
                                <div class="tenant-basic-info">
                                    <h4>Juan Karlos</h4>
                                    <span class="tenant-unit">Unit 01</span>
                                </div>
                                <div class="payment-status paid">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="tenant-details">
                                <div class="tenant-info">
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>+63 912 345 6789</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>juan.karlos@email.com</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>₱8,500/month</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Lease: Jan 2024 - Dec 2024</span>
                                    </div>
                                </div>
                                <div class="tenant-actions">
                                    <button class="action-btn">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                    <button class="action-btn primary">
                                        <i class="fas fa-comment"></i>
                                        Message
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Card 2 -->
                        <div class="tenant-card">
                            <div class="tenant-header">
                                <div class="tenant-avatar">
                                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Ana Reyes">
                                </div>
                                <div class="tenant-basic-info">
                                    <h4>Ana Reyes</h4>
                                    <span class="tenant-unit">Unit 02</span>
                                </div>
                                <div class="payment-status pending">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="tenant-details">
                                <div class="tenant-info">
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>+63 918 765 4321</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>ana.reyes@email.com</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>₱6,000/month</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Lease: Mar 2024 - Feb 2025</span>
                                    </div>
                                </div>
                                <div class="tenant-actions">
                                    <button class="action-btn">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                    <button class="action-btn warning">
                                        <i class="fas fa-bell"></i>
                                        Send Reminder
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Card 3 -->
                        <div class="tenant-card">
                            <div class="tenant-header">
                                <div class="tenant-avatar">
                                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Carlos Mendoza">
                                </div>
                                <div class="tenant-basic-info">
                                    <h4>Carlos Mendoza</h4>
                                    <span class="tenant-unit">Unit 04</span>
                                </div>
                                <div class="payment-status paid">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="tenant-details">
                                <div class="tenant-info">
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>+63 925 111 2233</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>carlos.mendoza@email.com</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>₱11,500/month</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Lease: Feb 2024 - Jan 2025</span>
                                    </div>
                                </div>
                                <div class="tenant-actions">
                                    <button class="action-btn">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                    <button class="action-btn primary">
                                        <i class="fas fa-comment"></i>
                                        Message
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Card 4 -->
                        <div class="tenant-card">
                            <div class="tenant-header">
                                <div class="tenant-avatar">
                                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face" alt="Maria Santos">
                                </div>
                                <div class="tenant-basic-info">
                                    <h4>Maria Santos</h4>
                                    <span class="tenant-unit">Unit 06</span>
                                </div>
                                <div class="payment-status overdue">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                            </div>
                            <div class="tenant-details">
                                <div class="tenant-info">
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>+63 917 888 9999</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>maria.santos@email.com</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>₱7,500/month</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Lease: Dec 2023 - Nov 2024</span>
                                    </div>
                                </div>
                                <div class="tenant-actions">
                                    <button class="action-btn">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                    <button class="action-btn danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Follow Up
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Card 5 -->
                        <div class="tenant-card">
                            <div class="tenant-header">
                                <div class="tenant-avatar">
                                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face" alt="Roberto Cruz">
                                </div>
                                <div class="tenant-basic-info">
                                    <h4>Roberto Cruz</h4>
                                    <span class="tenant-unit">Unit 08</span>
                                </div>
                                <div class="payment-status paid">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="tenant-details">
                                <div class="tenant-info">
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>+63 929 444 5555</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>roberto.cruz@email.com</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>₱9,000/month</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Lease: May 2024 - Apr 2025</span>
                                    </div>
                                </div>
                                <div class="tenant-actions">
                                    <button class="action-btn">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                    <button class="action-btn primary">
                                        <i class="fas fa-comment"></i>
                                        Message
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Card 6 -->
                        <div class="tenant-card">
                            <div class="tenant-header">
                                <div class="tenant-avatar">
                                    <img src="https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=150&h=150&fit=crop&crop=face" alt="Jane Doe">
                                </div>
                                <div class="tenant-basic-info">
                                    <h4>Jane Doe</h4>
                                    <span class="tenant-unit">Unit 12</span>
                                </div>
                                <div class="payment-status paid">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="tenant-details">
                                <div class="tenant-info">
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span>+63 932 777 8888</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span>jane.doe@email.com</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-peso-sign"></i>
                                        <span>₱10,000/month</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Lease: Jun 2024 - May 2025</span>
                                    </div>
                                </div>
                                <div class="tenant-actions">
                                    <button class="action-btn">
                                        <i class="fas fa-eye"></i>
                                        View Profile
                                    </button>
                                    <button class="action-btn primary">
                                        <i class="fas fa-comment"></i>
                                        Message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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
                window.location.href = '{{ route("login") }}';
            }
        }

        // View toggle functionality
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const view = this.dataset.view;
                const grid = document.getElementById('tenantsGrid');
                
                if (view === 'list') {
                    grid.classList.add('list-view');
                } else {
                    grid.classList.remove('list-view');
                }
            });
        });

        // Search functionality
        const searchInput = document.querySelector('.search-box input');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tenantCards = document.querySelectorAll('.tenant-card');
            
            tenantCards.forEach(card => {
                const tenantName = card.querySelector('.tenant-basic-info h4').textContent.toLowerCase();
                const tenantUnit = card.querySelector('.tenant-unit').textContent.toLowerCase();
                
                if (tenantName.includes(searchTerm) || tenantUnit.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

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