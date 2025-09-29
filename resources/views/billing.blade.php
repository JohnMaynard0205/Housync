<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Management - HouSync</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/billing.css') }}">
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
                    <div class="nav-item" onclick="window.location.href='{{ route('tenants') }}'">
                        <i class="fas fa-users"></i>
                        <span>Tenants</span>
                    </div>
                    <div class="nav-item active">
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

            <!-- Billing Content -->
            <div class="billing-content">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="page-title-section">
                        <h2>Billing Management</h2>
                        <p>Track payments, manage invoices, and monitor financial performance</p>
                    </div>
                    <div class="page-actions">
                        <div class="date-filter">
                            <i class="fas fa-calendar"></i>
                            <select>
                                <option>This Month</option>
                                <option>Last Month</option>
                                <option>Last 3 Months</option>
                                <option>This Year</option>
                            </select>
                        </div>
                        <button class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Export Report
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Create Invoice
                        </button>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="financial-summary">
                    <div class="summary-card revenue">
                        <div class="summary-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Total Revenue</h3>
                            <span class="summary-value">₱245,800</span>
                            <span class="summary-change positive">+12.5%</span>
                        </div>
                    </div>
                    <div class="summary-card collected">
                        <div class="summary-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Collected This Month</h3>
                            <span class="summary-value">₱189,500</span>
                            <span class="summary-change positive">+8.2%</span>
                        </div>
                    </div>
                    <div class="summary-card outstanding">
                        <div class="summary-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Outstanding</h3>
                            <span class="summary-value">₱56,300</span>
                            <span class="summary-change negative">+3 overdue</span>
                        </div>
                    </div>
                    <div class="summary-card pending">
                        <div class="summary-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="summary-info">
                            <h3>Pending Payments</h3>
                            <span class="summary-value">₱34,200</span>
                            <span class="summary-change neutral">5 invoices</span>
                        </div>
                    </div>
                </div>

                <!-- Main Billing Area -->
                <div class="billing-main">
                    <!-- Left Section - Payment Records -->
                    <div class="payments-section">
                        <div class="section-header">
                            <h3>Payment Records</h3>
                            <div class="filter-controls">
                                <button class="filter-btn active" data-status="all">All</button>
                                <button class="filter-btn" data-status="paid">Paid</button>
                                <button class="filter-btn" data-status="pending">Pending</button>
                                <button class="filter-btn" data-status="overdue">Overdue</button>
                            </div>
                        </div>

                        <div class="payments-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Tenant</th>
                                        <th>Unit</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-status="paid">
                                        <td><span class="invoice-number">#INV-2024-001</span></td>
                                        <td>Juan Karlos</td>
                                        <td>Unit 01</td>
                                        <td class="amount">₱8,500</td>
                                        <td>July 1, 2024</td>
                                        <td><span class="status paid">Paid</span></td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-eye"></i></button>
                                            <button class="action-btn"><i class="fas fa-download"></i></button>
                                        </td>
                                    </tr>
                                    <tr data-status="paid">
                                        <td><span class="invoice-number">#INV-2024-002</span></td>
                                        <td>Carlos Mendoza</td>
                                        <td>Unit 04</td>
                                        <td class="amount">₱11,500</td>
                                        <td>July 1, 2024</td>
                                        <td><span class="status paid">Paid</span></td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-eye"></i></button>
                                            <button class="action-btn"><i class="fas fa-download"></i></button>
                                        </td>
                                    </tr>
                                    <tr data-status="pending">
                                        <td><span class="invoice-number">#INV-2024-003</span></td>
                                        <td>Ana Reyes</td>
                                        <td>Unit 02</td>
                                        <td class="amount">₱6,000</td>
                                        <td>July 5, 2024</td>
                                        <td><span class="status pending">Pending</span></td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-bell"></i></button>
                                            <button class="action-btn"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr data-status="overdue">
                                        <td><span class="invoice-number">#INV-2024-004</span></td>
                                        <td>Maria Santos</td>
                                        <td>Unit 06</td>
                                        <td class="amount">₱7,500</td>
                                        <td>June 28, 2024</td>
                                        <td><span class="status overdue">Overdue</span></td>
                                        <td>
                                            <button class="action-btn danger"><i class="fas fa-exclamation-triangle"></i></button>
                                            <button class="action-btn"><i class="fas fa-phone"></i></button>
                                        </td>
                                    </tr>
                                    <tr data-status="paid">
                                        <td><span class="invoice-number">#INV-2024-005</span></td>
                                        <td>Roberto Cruz</td>
                                        <td>Unit 08</td>
                                        <td class="amount">₱9,000</td>
                                        <td>July 3, 2024</td>
                                        <td><span class="status paid">Paid</span></td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-eye"></i></button>
                                            <button class="action-btn"><i class="fas fa-download"></i></button>
                                        </td>
                                    </tr>
                                    <tr data-status="pending">
                                        <td><span class="invoice-number">#INV-2024-006</span></td>
                                        <td>Jane Doe</td>
                                        <td>Unit 12</td>
                                        <td class="amount">₱10,000</td>
                                        <td>July 7, 2024</td>
                                        <td><span class="status pending">Pending</span></td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-bell"></i></button>
                                            <button class="action-btn"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <tr data-status="overdue">
                                        <td><span class="invoice-number">#INV-2024-007</span></td>
                                        <td>Lisa Garcia</td>
                                        <td>Unit 05</td>
                                        <td class="amount">₱9,000</td>
                                        <td>June 25, 2024</td>
                                        <td><span class="status overdue">Overdue</span></td>
                                        <td>
                                            <button class="action-btn danger"><i class="fas fa-exclamation-triangle"></i></button>
                                            <button class="action-btn"><i class="fas fa-phone"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right Section - Quick Actions & Analytics -->
                    <div class="billing-sidebar">
                        <!-- Quick Actions -->
                        <div class="quick-actions">
                            <h4>Quick Actions</h4>
                            <div class="action-buttons">
                                <button class="quick-btn">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Generate Monthly Report</span>
                                </button>
                                <button class="quick-btn">
                                    <i class="fas fa-bell"></i>
                                    <span>Send Payment Reminders</span>
                                </button>
                                <button class="quick-btn">
                                    <i class="fas fa-calculator"></i>
                                    <span>Calculate Late Fees</span>
                                </button>
                                <button class="quick-btn">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Revenue Analytics</span>
                                </button>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="recent-activity">
                            <h4>Recent Payments</h4>
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-icon paid">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><strong>Juan Karlos</strong> paid ₱8,500</p>
                                        <span>2 hours ago</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon paid">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><strong>Roberto Cruz</strong> paid ₱9,000</p>
                                        <span>5 hours ago</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon reminder">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p>Reminder sent to <strong>Ana Reyes</strong></p>
                                        <span>1 day ago</span>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon overdue">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><strong>Maria Santos</strong> payment overdue</p>
                                        <span>3 days ago</span>
                                    </div>
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

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active filter button
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const status = this.dataset.status;
                const rows = document.querySelectorAll('.payments-table tbody tr');
                
                rows.forEach(row => {
                    if (status === 'all' || row.dataset.status === status) {
                        row.style.display = 'table-row';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Quick action buttons
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.querySelector('span').textContent;
                alert(`${action} functionality will be implemented.`);
            });
        });

        // Action buttons in table
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i').className;
                let action = 'Action';
                
                if (icon.includes('eye')) action = 'View Invoice';
                else if (icon.includes('download')) action = 'Download Invoice';
                else if (icon.includes('bell')) action = 'Send Reminder';
                else if (icon.includes('edit')) action = 'Edit Invoice';
                else if (icon.includes('exclamation-triangle')) action = 'Escalate Overdue';
                else if (icon.includes('phone')) action = 'Call Tenant';
                
                alert(`${action} functionality will be implemented.`);
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