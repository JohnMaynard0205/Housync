<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlords Dashboard - Firebase Data</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .stat-card .label {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .pending { color: #f39c12; }
        .approved { color: #27ae60; }
        .rejected { color: #e74c3c; }
        .total { color: #3498db; }

        .landlords-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }

        .landlord-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .landlord-card:hover {
            transform: translateY(-5px);
        }

        .landlord-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .landlord-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin-right: 15px;
        }

        .landlord-info h3 {
            color: #2c3e50;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .landlord-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: auto;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .landlord-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
        }

        .detail-item i {
            color: #667eea;
            margin-right: 10px;
            width: 20px;
        }

        .detail-item span {
            color: #2c3e50;
            font-size: 0.9rem;
        }

        .business-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .business-info h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .business-info p {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #7f8c8d;
        }

        .no-data i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 25px;
            margin-bottom: 20px;
            transition: background 0.3s ease;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .back-link i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <div class="header">
            <h1><i class="fas fa-building"></i> Landlords Dashboard</h1>
            <p>Firebase Real-time Data</p>
        </div>

        @if($landlordsData)
            @php
                $totalLandlords = count($landlordsData);
                $pendingCount = collect($landlordsData)->where('status', 'pending')->count();
                $approvedCount = collect($landlordsData)->where('status', 'approved')->count();
                $rejectedCount = collect($landlordsData)->where('status', 'rejected')->count();
            @endphp

            <div class="stats">
                <div class="stat-card">
                    <div class="icon total"><i class="fas fa-users"></i></div>
                    <div class="number">{{ $totalLandlords }}</div>
                    <div class="label">Total Landlords</div>
                </div>
                <div class="stat-card">
                    <div class="icon pending"><i class="fas fa-clock"></i></div>
                    <div class="number">{{ $pendingCount }}</div>
                    <div class="label">Pending Approval</div>
                </div>
                <div class="stat-card">
                    <div class="icon approved"><i class="fas fa-check-circle"></i></div>
                    <div class="number">{{ $approvedCount }}</div>
                    <div class="label">Approved</div>
                </div>
                <div class="stat-card">
                    <div class="icon rejected"><i class="fas fa-times-circle"></i></div>
                    <div class="number">{{ $rejectedCount }}</div>
                    <div class="label">Rejected</div>
                </div>
            </div>

            <div class="landlords-grid">
                @foreach($landlordsData as $landlord)
                    <div class="landlord-card">
                        <div class="landlord-header">
                            <div class="landlord-avatar">
                                {{ strtoupper(substr($landlord['name'], 0, 1)) }}
                            </div>
                            <div class="landlord-info">
                                <h3>{{ $landlord['name'] }}</h3>
                                <p>{{ $landlord['email'] }}</p>
                            </div>
                            <div class="status-badge status-{{ $landlord['status'] }}">
                                {{ $landlord['status'] }}
                            </div>
                        </div>

                        <div class="landlord-details">
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>{{ $landlord['phone'] ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $landlord['address'] ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ date('M j, Y', strtotime($landlord['registered_at'])) }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-user-tag"></i>
                                <span>ID: {{ $landlord['id'] }}</span>
                            </div>
                        </div>

                        @if($landlord['business_info'])
                            <div class="business-info">
                                <h4><i class="fas fa-briefcase"></i> Business Information</h4>
                                <p>{{ $landlord['business_info'] }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="landlord-card">
                <div class="no-data">
                    <i class="fas fa-users-slash"></i>
                    <h3>No Landlords Found</h3>
                    <p>No landlord registrations found in Firebase database.</p>
                </div>
            </div>
        @endif
    </div>
    
    
</body>
</html> 