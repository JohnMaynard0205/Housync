<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Logs - HouSync IoT</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/security.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- IMMEDIATE SIDEBAR STATE APPLICATION -->
    <script>
        // Apply sidebar state IMMEDIATELY when head loads - before body renders
        (function() {
            const sidebarState = localStorage.getItem('sidebarExpanded');
            if (sidebarState === 'true') {
                // Add CSS class immediately
                document.documentElement.classList.add('sidebar-expanded');
                console.log('SECURITY HEAD - Added sidebar-expanded class');
            }
        })();
    </script>
    
    <!-- IMMEDIATE CSS TO HANDLE SIDEBAR STATE -->
    <style>
        /* Immediate sidebar state CSS - applies before JavaScript */
        .sidebar-expanded .sidebar {
            width: 250px !important;
        }
        .sidebar-expanded .main-content {
            margin-left: 250px !important;
        }
        .sidebar-expanded .sidebar .nav-item span {
            display: block !important;
        }
        
        /* Ensure menu toggle button is always clickable */
        .menu-toggle {
            position: relative !important;
            z-index: 9999 !important;
            pointer-events: auto !important;
            cursor: pointer !important;
            background: transparent !important;
            border: none !important;
            padding: 8px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .menu-toggle:hover {
            background: rgba(0, 0, 0, 0.1) !important;
            border-radius: 4px !important;
        }
        
        .menu-toggle:active {
            background: rgba(0, 0, 0, 0.2) !important;
        }
        
        /* Ensure the header area doesn't block clicks */
        .header {
            position: relative !important;
            z-index: 999 !important;
        }
        
        .header-left {
            z-index: 9999 !important;
            position: relative !important;
        }
        
        /* Navigation structure and logout button styles */
        .sidebar-content {
            display: flex !important;
            flex-direction: column !important;
            height: 100vh !important;
            padding: 20px 0 0 0 !important;
        }
        
        .nav-items-container {
            flex: 1 !important;
            padding-bottom: 20px !important;
        }
        
        .nav-bottom {
            margin-top: auto !important;
            padding: 10px 0 20px 0 !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        
        .logout-item {
            color: #ef4444 !important;
            transition: all 0.3s ease !important;
        }
        
        .logout-item:hover {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #f87171 !important;
        }
        
        .logout-item i {
            color: #ef4444 !important;
        }
        
        .logout-item:hover i {
            color: #f87171 !important;
        }
        
        /* Ensure logout text shows when sidebar is expanded */
        .sidebar-expanded .logout-item span {
            display: block !important;
        }
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
            background: #fffae6 !important; /* Debug: yellow background */
            border: 3px solid #e53e3e !important; /* Debug: thick red border */
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
        <aside class="sidebar" id="mainSidebar">
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
                    <div class="nav-item active">
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
                    <h1 class="app-title">HouSync IoT</h1>
                </div>
                <div class="header-right">
                    <button class="header-btn">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="header-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </button>
                    <div class="user-profile">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Ann Lee" class="profile-avatar">
                        <span class="profile-name">Ann Lee</span>
                        <i class="fas fa-chevron-down"></i>
                        <!-- Dropdown Menu -->
                        <div class="profile-dropdown" style="display: none;">
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

            <!-- Security Content -->
            <div class="security-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-title-section">
                        <h2>Security & Access Control</h2>
                        <p>Real-time monitoring of RFID access points and security events</p>
                    </div>
                    <div class="page-actions">
                        <div class="live-indicator">
                            <span class="live-dot"></span>
                            <span>LIVE</span>
                        </div>
                        <button class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Export Logs
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add RFID Card
                        </button>
                    </div>
                </div>

                <!-- Security Status Cards -->
                <div class="security-status">
                    <div class="status-card system-status">
                        <div class="status-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="status-info">
                            <h3>System Status</h3>
                            <span class="status-value online">ONLINE</span>
                            <span class="status-detail">All devices connected</span>
                        </div>
                        <div class="status-indicator online"></div>
                    </div>
                    
                    <div class="status-card access-today">
                        <div class="status-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="status-info">
                            <h3>Access Events Today</h3>
                            <span class="status-value">127</span>
                            <span class="status-detail">+8% from yesterday</span>
                        </div>
                    </div>
                    
                    <div class="status-card security-alerts">
                        <div class="status-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="status-info">
                            <h3>Security Alerts</h3>
                            <span class="status-value warning">3</span>
                            <span class="status-detail">2 new alerts</span>
                        </div>
                    </div>
                    
                    <div class="status-card active-cards">
                        <div class="status-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="status-info">
                            <h3>Active RFID Cards</h3>
                            <span class="status-value">28</span>
                            <span class="status-detail">All tenants registered</span>
                        </div>
                    </div>
                </div>

                <!-- Main Security Dashboard -->
                <div class="security-main">
                    <!-- Left Panel - Access Logs -->
                    <div class="access-logs-section">
                        <div class="section-header">
                            <h3>Real-Time Access Logs</h3>
                            <div class="log-filters">
                                <button class="filter-btn active" data-filter="all">All</button>
                                <button class="filter-btn" data-filter="entry">Entry</button>
                                <button class="filter-btn" data-filter="exit">Exit</button>
                                <button class="filter-btn" data-filter="alerts">Alerts</button>
                            </div>
                        </div>

                        <div class="access-logs-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Tenant</th>
                                        <th>Action</th>
                                        <th>RFID Card</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="accessLogsTable">
                                    <tr data-type="entry" class="log-entry recent">
                                        <td class="time-col">
                                            <span class="time">14:35:42</span>
                                            <span class="date">Today</span>
                                        </td>
                                        <td class="tenant-col">
                                            <div class="tenant-info">
                                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Juan Karlos">
                                                <div>
                                                    <span class="name">Juan Karlos</span>
                                                    <span class="unit">Unit 01</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="action-col">
                                            <span class="action entry">
                                                <i class="fas fa-sign-in-alt"></i>
                                                TAP IN
                                            </span>
                                        </td>
                                        <td class="card-col">
                                            <span class="card-id">RF-001-JK</span>
                                        </td>
                                        <td class="status-col">
                                            <span class="status success">Granted</span>
                                        </td>
                                    </tr>
                                    
                                    <tr data-type="exit">
                                        <td class="time-col">
                                            <span class="time">14:28:15</span>
                                            <span class="date">Today</span>
                                        </td>
                                        <td class="tenant-col">
                                            <div class="tenant-info">
                                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Ana Reyes">
                                                <div>
                                                    <span class="name">Ana Reyes</span>
                                                    <span class="unit">Unit 02</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="action-col">
                                            <span class="action exit">
                                                <i class="fas fa-sign-out-alt"></i>
                                                TAP OUT
                                            </span>
                                        </td>
                                        <td class="card-col">
                                            <span class="card-id">RF-002-AR</span>
                                        </td>
                                        <td class="status-col">
                                            <span class="status success">Granted</span>
                                        </td>
                                    </tr>
                                    
                                    <tr data-type="entry">
                                        <td class="time-col">
                                            <span class="time">14:15:33</span>
                                            <span class="date">Today</span>
                                        </td>
                                        <td class="tenant-col">
                                            <div class="tenant-info">
                                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Carlos Mendoza">
                                                <div>
                                                    <span class="name">Carlos Mendoza</span>
                                                    <span class="unit">Unit 04</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="action-col">
                                            <span class="action entry">
                                                <i class="fas fa-sign-in-alt"></i>
                                                TAP IN
                                            </span>
                                        </td>
                                        <td class="card-col">
                                            <span class="card-id">RF-004-CM</span>
                                        </td>
                                        <td class="status-col">
                                            <span class="status success">Granted</span>
                                        </td>
                                    </tr>
                                    
                                    <tr data-type="alerts" class="alert-row">
                                        <td class="time-col">
                                            <span class="time">13:45:21</span>
                                            <span class="date">Today</span>
                                        </td>
                                        <td class="tenant-col">
                                            <div class="tenant-info">
                                                <i class="fas fa-exclamation-triangle alert-icon"></i>
                                                <div>
                                                    <span class="name">Unknown Card</span>
                                                    <span class="unit">Gate Access</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="action-col">
                                            <span class="action denied">
                                                <i class="fas fa-ban"></i>
                                                TAP DENIED
                                            </span>
                                        </td>
                                        <td class="card-col">
                                            <span class="card-id unknown">RF-999-UNK</span>
                                        </td>
                                        <td class="status-col">
                                            <span class="status denied">Denied</span>
                                        </td>
                                    </tr>
                                    
                                    <tr data-type="exit">
                                        <td class="time-col">
                                            <span class="time">13:30:18</span>
                                            <span class="date">Today</span>
                                        </td>
                                        <td class="tenant-col">
                                            <div class="tenant-info">
                                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face" alt="Maria Santos">
                                                <div>
                                                    <span class="name">Maria Santos</span>
                                                    <span class="unit">Unit 06</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="action-col">
                                            <span class="action exit">
                                                <i class="fas fa-sign-out-alt"></i>
                                                TAP OUT
                                            </span>
                                        </td>
                                        <td class="card-col">
                                            <span class="card-id">RF-006-MS</span>
                                        </td>
                                        <td class="status-col">
                                            <span class="status success">Granted</span>
                                        </td>
                                    </tr>
                                    
                                    <tr data-type="entry">
                                        <td class="time-col">
                                            <span class="time">12:45:07</span>
                                            <span class="date">Today</span>
                                        </td>
                                        <td class="tenant-col">
                                            <div class="tenant-info">
                                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face" alt="Roberto Cruz">
                                                <div>
                                                    <span class="name">Roberto Cruz</span>
                                                    <span class="unit">Unit 08</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="action-col">
                                            <span class="action entry">
                                                <i class="fas fa-sign-in-alt"></i>
                                                TAP IN
                                            </span>
                                        </td>
                                        <td class="card-col">
                                            <span class="card-id">RF-008-RC</span>
                                        </td>
                                        <td class="status-col">
                                            <span class="status success">Granted</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right Panel - Security Controls -->
                    <div class="security-sidebar">
                        <!-- Device Status -->
                        <div class="device-status">
                            <h4>IoT Device Status</h4>
                            <div class="device-list">
                                <div class="device-item">
                                    <div class="device-info">
                                        <i class="fas fa-door-closed"></i>
                                        <div>
                                            <span class="device-name">Main Gate Reader</span>
                                            <span class="device-id">DEV-GATE-01</span>
                                        </div>
                                    </div>
                                    <div class="device-status-indicator online">
                                        <span class="status-dot"></span>
                                        <span>Online/Locked</span>
                                    </div>
                                </div>
                                
                                <div class="device-item">
                                    <div class="device-info">
                                        <i class="fas fa-wifi"></i>
                                        <div>
                                            <span class="device-name">Network Bridge</span>
                                            <span class="device-id">NET-HUB-01</span>
                                        </div>
                                    </div>
                                    <div class="device-status-indicator warning">
                                        <span class="status-dot"></span>
                                        <span>Weak Signal</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Alerts -->
                        <div class="recent-alerts">
                            <h4>Security Alerts</h4>
                            <div class="alert-list">
                                <div class="alert-item high">
                                    <div class="alert-icon">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <div class="alert-details">
                                        <p><strong>Unauthorized Access</strong></p>
                                        <span>Unknown RFID card attempted access</span>
                                        <span class="alert-time">13:45 PM</span>
                                    </div>
                                </div>
                                
                                <div class="alert-item medium">
                                    <div class="alert-icon">
                                        <i class="fas fa-wifi"></i>
                                    </div>
                                    <div class="alert-details">
                                        <p><strong>Network Issue</strong></p>
                                        <span>Bridge signal strength low</span>
                                        <span class="alert-time">12:30 PM</span>
                                    </div>
                                </div>
                                
                                <div class="alert-item low">
                                    <div class="alert-icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="alert-details">
                                        <p><strong>System Maintenance</strong></p>
                                        <span>Scheduled backup completed</span>
                                        <span class="alert-time">11:00 AM</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions */
                        <div class="quick-actions">
                            <h4>Quick Actions</h4>
                            <div class="action-buttons">
                                <button class="quick-btn">
                                    <i class="fas fa-lock"></i>
                                    <span>Emergency Lock</span>
                                </button>
                                <button class="quick-btn">
                                    <i class="fas fa-plus"></i>
                                    <span>Add New Card</span>
                                </button>
                                <button class="quick-btn">
                                    <i class="fas fa-ban"></i>
                                    <span>Disable Card</span>
                                </button>
                                <button class="quick-btn">
                                    <i class="fas fa-history"></i>
                                    <span>View History</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // ENHANCED SIDEBAR PERSISTENCE FOR SECURITY PAGE WITH TOGGLE FIX
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('SECURITY - DOM Content Loaded');
            
            // Find menu toggle button with better error handling
            const menuToggle = document.querySelector('.menu-toggle');
            console.log('SECURITY - Menu toggle button found:', !!menuToggle);
            
            if (menuToggle) {
                console.log('SECURITY - Menu toggle button element:', menuToggle);
            }
            
            // Ensure sidebar state is applied after DOM loads
            const sidebarState = localStorage.getItem('sidebarExpanded');
            console.log('SECURITY DOM READY - Sidebar state:', sidebarState);
            
            if (sidebarState === 'true') {
                document.documentElement.classList.add('sidebar-expanded');
                console.log('SECURITY DOM READY - Added sidebar-expanded class');
            } else {
                document.documentElement.classList.remove('sidebar-expanded');
                console.log('SECURITY DOM READY - Removed sidebar-expanded class');
            }
            
            // Enhanced toggle functionality with multiple approaches
            function setupToggleButton() {
                const menuToggle = document.querySelector('.menu-toggle');
                
                if (menuToggle) {
                    // Remove any existing event listeners
                    menuToggle.removeEventListener('click', handleToggleClick);
                    
                    // Add the click event listener
                    menuToggle.addEventListener('click', handleToggleClick);
                    
                    // Also add event listener with different approach as backup
                    menuToggle.onclick = handleToggleClick;
                    
                    console.log('SECURITY - Toggle event listeners added successfully');
                } else {
                    console.error('SECURITY - Menu toggle button not found!');
                    // Retry finding the button after a short delay
                    setTimeout(setupToggleButton, 100);
                }
            }
            
            function handleToggleClick(event) {
                event.preventDefault();
                event.stopPropagation();
                
                console.log('SECURITY - Toggle button clicked!');
                
                const isCurrentlyExpanded = document.documentElement.classList.contains('sidebar-expanded');
                console.log('SECURITY - Currently expanded:', isCurrentlyExpanded);
                
                if (isCurrentlyExpanded) {
                    document.documentElement.classList.remove('sidebar-expanded');
                    localStorage.setItem('sidebarExpanded', 'false');
                    console.log('SECURITY TOGGLE - Sidebar collapsed');
                } else {
                    document.documentElement.classList.add('sidebar-expanded');
                    localStorage.setItem('sidebarExpanded', 'true');
                    console.log('SECURITY TOGGLE - Sidebar expanded');
                }
            }
            
            // Setup the toggle button
            setupToggleButton();
            
            // Also setup with a slight delay as backup
            setTimeout(setupToggleButton, 200);
            
            // Filter functionality for access logs
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.dataset.filter;
                    const rows = document.querySelectorAll('#accessLogsTable tr');
                    
                    rows.forEach(row => {
                        if (filter === 'all' || row.dataset.type === filter) {
                            row.style.display = 'table-row';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });

            // Simulate real-time updates
            function simulateRealTimeUpdate() {
                const table = document.getElementById('accessLogsTable');
                if (!table) return;
                
                const tenants = [
                    { name: 'Jane Doe', unit: 'Unit 12', img: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face', card: 'RF-012-JD' },
                    { name: 'Lisa Garcia', unit: 'Unit 05', img: 'https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?w=150&h=150&fit=crop&crop=face', card: 'RF-005-LG' }
                ];
                
                const actions = ['entry', 'exit'];
                const randomTenant = tenants[Math.floor(Math.random() * tenants.length)];
                const randomAction = actions[Math.floor(Math.random() * actions.length)];
                const currentTime = new Date();
                
                const newRow = document.createElement('tr');
                newRow.dataset.type = randomAction;
                newRow.className = 'log-entry recent';
                
                newRow.innerHTML = `
                    <td class="time-col">
                        <span class="time">${currentTime.toLocaleTimeString()}</span>
                        <span class="date">Now</span>
                    </td>
                    <td class="tenant-col">
                        <div class="tenant-info">
                            <img src="${randomTenant.img}" alt="${randomTenant.name}">
                            <div>
                                <span class="name">${randomTenant.name}</span>
                                <span class="unit">${randomTenant.unit}</span>
                            </div>
                        </div>
                    </td>
                    <td class="action-col">
                        <span class="action ${randomAction}">
                            <i class="fas fa-sign-${randomAction === 'entry' ? 'in' : 'out'}-alt"></i>
                            TAP ${randomAction === 'entry' ? 'IN' : 'OUT'}
                        </span>
                    </td>
                    <td class="card-col">
                        <span class="card-id">${randomTenant.card}</span>
                    </td>
                    <td class="status-col">
                        <span class="status success">Granted</span>
                    </td>
                `;
                
                table.insertBefore(newRow, table.firstChild);
                
                setTimeout(() => {
                    newRow.classList.remove('recent');
                }, 2000);
                
                const accessTodayElement = document.querySelector('.access-today .status-value');
                if (accessTodayElement) {
                    let currentCount = parseInt(accessTodayElement.textContent);
                    accessTodayElement.textContent = currentCount + 1;
                }
            }

            // Quick action buttons
            document.querySelectorAll('.quick-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const action = this.querySelector('span').textContent;
                    alert(`${action} functionality will be implemented with IoT backend.`);
                });
            });

            // Start real-time simulation
            setInterval(simulateRealTimeUpdate, 15000);

            // Live indicator animation
            setInterval(() => {
                const liveIndicator = document.querySelector('.live-dot');
                if (liveIndicator) {
                    liveIndicator.style.opacity = 
                        liveIndicator.style.opacity === '0.3' ? '1' : '0.3';
                }
            }, 1000);
        });
        
        // Alternative approach - add click listener when window loads
        window.addEventListener('load', function() {
            console.log('SECURITY - Window loaded, setting up backup toggle');
            const menuToggle = document.querySelector('.menu-toggle');
            if (menuToggle) {
                menuToggle.style.cursor = 'pointer';
                menuToggle.style.pointerEvents = 'auto';
                console.log('SECURITY - Backup toggle setup complete');
            }
        });
        
        // Profile dropdown logic (robust with fallback)
        function attachProfileDropdownListeners() {
            console.log('Dropdown JS: attachProfileDropdownListeners called');
            const userProfiles = document.querySelectorAll('.user-profile');
            userProfiles.forEach((userProfile, idx) => {
                const profileDropdown = userProfile.querySelector('.profile-dropdown');
                let dropdownOpen = false;
                console.log('Dropdown JS: Found userProfile', idx, userProfile, profileDropdown);
                if (userProfile && profileDropdown && !userProfile.classList.contains('dropdown-initialized')) {
                    userProfile.classList.add('dropdown-initialized');
                    userProfile.addEventListener('click', function(e) {
                        e.stopPropagation();
                        dropdownOpen = !dropdownOpen;
                        profileDropdown.classList.toggle('show', dropdownOpen);
                        if (dropdownOpen) profileDropdown.style.removeProperty('display');
                        console.log('Clicked userProfile:', userProfile);
                        console.log('Toggling dropdown:', profileDropdown, 'Open:', dropdownOpen);
                    });
                    document.addEventListener('click', function() {
                        if (dropdownOpen) {
                            profileDropdown.classList.remove('show');
                            dropdownOpen = false;
                        }
                    });
                } else {
                    console.log('Dropdown JS: userProfile or profileDropdown not found or already initialized.', userProfile, profileDropdown);
                }
            });
        }
        // Attach on DOMContentLoaded and with fallback timers
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', attachProfileDropdownListeners);
        } else {
            attachProfileDropdownListeners();
        }
        setTimeout(attachProfileDropdownListeners, 500);
        setTimeout(attachProfileDropdownListeners, 1000);
        
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
    </script>
    
    
</body>
</html> 