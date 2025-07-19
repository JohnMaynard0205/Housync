<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Landlords - Housesync</title>
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

        .badge {
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
            align-items: center;
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

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .data-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #f1f5f9;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
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

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
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

        .alert-warning {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #d97706;
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

        /* Modal for rejection */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #1e293b;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
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
                <a href="{{ route('super-admin.pending-landlords') }}" class="nav-item active">
                    <i class="fas fa-user-clock"></i> Pending Approvals
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="badge">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('super-admin.users') }}" class="nav-item">
                    <i class="fas fa-users"></i> User Management
                </a>
                <a href="{{ route('super-admin.apartments') }}" class="nav-item">
                    <i class="fas fa-building"></i> Properties
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-bar"></i> Analytics
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
                    <h1>Pending Landlord Approvals</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">Review and approve landlord registration requests</p>
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

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $pendingLandlords->count() }}</div>
                    <div class="stat-label">Pending Approvals</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ \App\Models\User::where('role', 'landlord')->where('status', 'approved')->count() }}</div>
                    <div class="stat-label">Approved Landlords</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ \App\Models\User::where('role', 'landlord')->where('status', 'rejected')->count() }}</div>
                    <div class="stat-label">Rejected Applications</div>
                </div>
            </div>

            <!-- Pending Landlords Section -->
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Landlord Registration Requests</h2>
                        <p class="section-subtitle">Review applications and approve or reject landlord accounts</p>
                    </div>
                </div>

                @if($pendingLandlords->count() > 0)
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Business Info</th>
                                    <th>Registered</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingLandlords as $landlord)
                                    <tr>
                                        <td>
                                            <div>
                                                <div style="font-weight: 600;">{{ $landlord->name }}</div>
                                                <div style="font-size: 0.75rem; color: #64748b;">ID: #{{ $landlord->id }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $landlord->email }}</td>
                                        <td>{{ $landlord->phone ?? 'N/A' }}</td>
                                        <td>
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $landlord->business_info }}">
                                                {{ $landlord->business_info ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $landlord->created_at->format('M d, Y') }}</div>
                                            <div style="font-size: 0.75rem; color: #64748b;">{{ $landlord->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td>
                                            <span class="status-badge status-{{ $landlord->status }}">
                                                {{ ucfirst($landlord->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <form method="POST" action="{{ route('super-admin.approve-landlord', $landlord->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this landlord?')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <button class="btn btn-danger btn-sm" onclick="showRejectModal({{ $landlord->id }}, '{{ $landlord->name }}')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($pendingLandlords->hasPages())
                        <div style="margin-top: 2rem;">
                            {{ $pendingLandlords->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <h3 class="empty-title">No Pending Approvals</h3>
                        <p class="empty-text">All landlord applications have been reviewed. New applications will appear here.</p>
                        <a href="{{ route('super-admin.users') }}" class="btn btn-primary">
                            <i class="fas fa-users"></i> View All Users
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Reject Landlord Application</h3>
                <button type="button" class="close" onclick="closeRejectModal()">&times;</button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Landlord Name</label>
                    <input type="text" id="landlordName" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Rejection Reason *</label>
                    <textarea name="reason" class="form-control" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(landlordId, landlordName) {
            document.getElementById('rejectModal').style.display = 'block';
            document.getElementById('rejectForm').action = '/super-admin/reject-landlord/' + landlordId;
            document.getElementById('landlordName').value = landlordName;
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejectForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('rejectModal');
            if (event.target == modal) {
                closeRejectModal();
            }
        }
    </script>
</body>
</html> 