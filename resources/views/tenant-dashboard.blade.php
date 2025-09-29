<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard - HouSync</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tenant-specific styles -->
    <style>
        /* Tenant dashboard color scheme - Green theme */
        .tenant-nav-item.active {
            background: #10b981;
            color: white;
        }
        
        .tenant-nav-item:hover {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .tenant-stat-card {
            border-left: 4px solid #10b981;
        }
        
        .tenant-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .tenant-btn-primary {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .tenant-btn-primary:hover {
            background: #059669;
        }
        
        .payment-status.paid {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .payment-status.due {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .payment-status.overdue {
            background: #fee2e2;
            color: #dc2626;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        /* Fix anchor tag styling for navigation */
        a.nav-item {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }
        
        a.nav-item:hover {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        /* Ensure profile button is clickable */
        .nav-item.tenant-nav-item {
            cursor: pointer !important;
            pointer-events: auto !important;
            position: relative !important;
            z-index: 999 !important;
        }
        
        .nav-item.tenant-nav-item * {
            pointer-events: none !important;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('partials.tenant-sidebar')

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header tenant-header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="header-center">
                    <h1 class="app-title">Tenant Portal</h1>
                </div>
                <div class="header-right">
                    <button class="header-btn">
                        <i class="fas fa-bell"></i>
                    </button>
                    <div class="user-profile">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Juan Karlos" class="profile-avatar">
                        <span class="profile-name">Juan Karlos</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Section -->
                <div class="tenant-welcome" style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                    <h2 style="color: #1f2937; margin: 0 0 10px 0;">Welcome back, Juan!</h2>
                    <p style="color: #6b7280; margin: 0;">Here's what's happening with your rental</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card tenant-stat-card">
                        <div class="stat-info">
                            <h3>Next Rent Due</h3>
                            <div class="stat-value">
                                <span class="value">Aug 1, 2024</span>
                            </div>
                            <span class="payment-status due">Due in 5 days</span>
                        </div>
                    </div>

                    <div class="stat-card tenant-stat-card">
                        <div class="stat-info">
                            <h3>Monthly Rent</h3>
                            <div class="stat-value">
                                <span class="value">₱8,500</span>
                            </div>
                            <span style="color: #6b7280; font-size: 12px;">Unit 01</span>
                        </div>
                    </div>

                    <div class="stat-card tenant-stat-card">
                        <div class="stat-info">
                            <h3>Lease Status</h3>
                            <div class="stat-value">
                                <span class="value">Active</span>
                            </div>
                            <span class="payment-status paid">Until Dec 2024</span>
                        </div>
                    </div>

                    <div class="stat-card tenant-stat-card">
                        <div class="stat-info">
                            <h3>Maintenance Requests</h3>
                            <div class="stat-value">
                                <span class="value">1 Open</span>
                            </div>
                            <span style="color: #6b7280; font-size: 12px;">Last updated today</span>
                        </div>
                    </div>
                </div>

                <!-- Main Dashboard Area -->
                <div class="dashboard-main">
                    <!-- Left Section - Recent Activity -->
                    <div class="data-section">
                        <div class="section-header">
                            <h2>Recent Activity</h2>
                            <div class="action-buttons">
                                <button class="tenant-btn-primary">
                                    <i class="fas fa-plus"></i>
                                    New Request
                                </button>
                            </div>
                        </div>

                        <div class="data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Activity</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>July 1, 2024</td>
                                        <td>Rent Payment</td>
                                        <td><span class="payment-status paid">Paid</span></td>
                                        <td>₱8,500</td>
                                    </tr>
                                    <tr>
                                        <td>July 15, 2024</td>
                                        <td>Maintenance Request</td>
                                        <td><span class="payment-status due">In Progress</span></td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>June 1, 2024</td>
                                        <td>Rent Payment</td>
                                        <td><span class="payment-status paid">Paid</span></td>
                                        <td>₱8,500</td>
                                    </tr>
                                    <tr>
                                        <td>May 1, 2024</td>
                                        <td>Rent Payment</td>
                                        <td><span class="payment-status paid">Paid</span></td>
                                        <td>₱8,500</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right Section - Quick Actions & Info -->
                    <div class="activity-section">
                        <h3>Quick Actions</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
                            <button class="tenant-btn-primary" style="width: 100%;">
                                <i class="fas fa-credit-card"></i>
                                Pay Rent
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%;">
                                <i class="fas fa-tools"></i>
                                Request Maintenance
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%;">
                                <i class="fas fa-envelope"></i>
                                Contact Landlord
                            </button>
                            <button class="tenant-btn-primary" style="width: 100%;">
                                <i class="fas fa-download"></i>
                                Download Receipt
                            </button>
                        </div>

                        <h4 style="color: #1f2937; margin: 0 0 15px 0;">Unit Information</h4>
                        <div style="background: #f9fafb; padding: 20px; border-radius: 8px;">
                            <div style="margin-bottom: 10px;">
                                <strong>Unit Number:</strong> 01
                            </div>
                            <div style="margin-bottom: 10px;">
                                <strong>Type:</strong> 1 Bedroom
                            </div>
                            <div style="margin-bottom: 10px;">
                                <strong>Floor:</strong> Ground Floor
                            </div>
                            <div style="margin-bottom: 10px;">
                                <strong>Landlord:</strong> HouSync Management
                            </div>
                            <div>
                                <strong>Emergency Contact:</strong> +63 917 123 4567
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


        // Quick action button functionality
        document.querySelectorAll('.tenant-btn-primary').forEach(btn => {
            btn.addEventListener('click', function() {
                const text = this.textContent.trim();
                alert(`${text} functionality will be implemented.`);
            });
        });
    </script>
    
    
</body>
</html> 