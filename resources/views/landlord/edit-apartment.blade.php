<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property - Housesync</title>
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
            border-left-color: #fb923c;
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

        .badge-count {
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

        /* Form Styles */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            border-left: 4px solid #f97316;
        }

        .form-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: #f97316;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: ' *';
            color: #ef4444;
        }

        .form-control {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .form-control.error {
            border-color: #ef4444;
        }

        .form-control.error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        select.form-control {
            cursor: pointer;
        }

        .form-help {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .form-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.25rem;
        }

        /* Amenities Grid */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .amenity-item:hover {
            border-color: #f97316;
            background: #fef7ed;
        }

        .amenity-item input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: #f97316;
        }

        .amenity-item label {
            font-size: 0.875rem;
            color: #1e293b;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .amenity-item i {
            color: #f97316;
            width: 16px;
        }

        /* Action Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
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
            background: #f97316;
            color: white;
        }

        .btn-primary:hover {
            background: #ea580c;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #f1f5f9;
        }

        .form-actions-left {
            display: flex;
            gap: 1rem;
        }

        .form-actions-right {
            display: flex;
            gap: 1rem;
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

        /* Property Preview */
        .property-preview {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #10b981;
        }

        .preview-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .preview-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
        }

        .preview-icon {
            color: #10b981;
        }

        .preview-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .preview-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .preview-item i {
            color: #f97316;
            width: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .amenities-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .form-actions-left,
            .form-actions-right {
                width: 100%;
                justify-content: center;
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
                <a href="{{ route('landlord.apartments') }}" class="nav-item active">
                    <i class="fas fa-building"></i> My Properties
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item">
                    <i class="fas fa-door-open"></i> My Units
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i> Tenants
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
                <div>
                    <h1>Edit Property</h1>
                    <p style="color: #64748b; margin-top: 0.5rem;">Update property information</p>
                </div>
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

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> Please fix the following errors:
                    <ul style="margin-left: 1rem; margin-top: 0.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Property Preview -->
            <div class="property-preview">
                <div class="preview-header">
                    <i class="fas fa-eye preview-icon"></i>
                    <h3 class="preview-title">Editing: {{ $apartment->name }}</h3>
                </div>
                <div class="preview-info">
                    <div class="preview-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $apartment->address }}</span>
                    </div>
                    <div class="preview-item">
                        <i class="fas fa-door-open"></i>
                        <span>{{ $apartment->units->count() }} Units</span>
                    </div>
                    <div class="preview-item">
                        <i class="fas fa-calendar"></i>
                        <span>Created {{ $apartment->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="preview-item">
                        <i class="fas fa-users"></i>
                        <span>{{ $apartment->units->where('status', 'occupied')->count() }} Occupied</span>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="page-section">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Property Information</h2>
                        <p class="section-subtitle">Update the details for your property</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('landlord.update-apartment', $apartment->id) }}" class="form-container">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label required">Property Name</label>
                                <input type="text" name="name" class="form-control @error('name') error @enderror" 
                                       value="{{ old('name', $apartment->name) }}" placeholder="e.g., Sunshine Apartments" required>
                                @error('name')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Property Type</label>
                                <select name="property_type" class="form-control @error('property_type') error @enderror" required>
                                    <option value="">Select property type</option>
                                    <option value="apartment" {{ old('property_type', $apartment->property_type) == 'apartment' ? 'selected' : '' }}>Apartment Building</option>
                                    <option value="condominium" {{ old('property_type', $apartment->property_type) == 'condominium' ? 'selected' : '' }}>Condominium</option>
                                    <option value="townhouse" {{ old('property_type', $apartment->property_type) == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                                    <option value="house" {{ old('property_type', $apartment->property_type) == 'house' ? 'selected' : '' }}>Single Family House</option>
                                    <option value="duplex" {{ old('property_type', $apartment->property_type) == 'duplex' ? 'selected' : '' }}>Duplex</option>
                                </select>
                                @error('property_type')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Total Units</label>
                                <input type="number" name="total_units" id="total_units" class="form-control @error('total_units') error @enderror" 
                                       value="{{ old('total_units', $apartment->total_units) }}" min="{{ $apartment->units()->count() }}" placeholder="e.g., 24" required>
                                @error('total_units')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Current actual units: {{ $apartment->units()->count() }}
                                    @if($apartment->units()->count() < $apartment->total_units)
                                        ({{ $apartment->total_units - $apartment->units()->count() }} units need to be created)
                                    @endif
                                </small>
                            </div>

                            <input type="hidden" id="current_unit_count" value="{{ $apartment->units()->count() }}">

                            @if($apartment->units()->count() < $apartment->total_units)
                            <div class="form-group full-width">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Discrepancy Detected:</strong> This property has {{ $apartment->total_units }} units listed but only {{ $apartment->units()->count() }} units actually created.
                                    <br>Would you like to auto-generate the missing {{ $apartment->total_units - $apartment->units()->count() }} units?
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="auto_create_missing" name="auto_create_missing" value="1">
                                        <label class="custom-control-label" for="auto_create_missing">
                                            Yes, auto-create missing units
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="form-group full-width" id="increase_units_notice" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    You're increasing the total units. Would you like to auto-generate the additional units?
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="auto_generate_additional" name="auto_generate_additional" value="1" checked>
                                        <label class="custom-control-label" for="auto_generate_additional">
                                            Yes, auto-generate <span id="additional_count">0</span> additional units
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Year Built</label>
                                <input type="number" name="year_built" class="form-control @error('year_built') error @enderror" 
                                       value="{{ old('year_built', $apartment->year_built) }}" min="1900" max="{{ date('Y') }}" placeholder="e.g., 2020">
                                @error('year_built')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-map-marker-alt"></i>
                            Location Information
                        </h3>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label class="form-label required">Street Address</label>
                                <input type="text" name="address" class="form-control @error('address') error @enderror" 
                                       value="{{ old('address', $apartment->address) }}" placeholder="e.g., 123 Main Street" required>
                                @error('address')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control @error('city') error @enderror" 
                                       value="{{ old('city', $apartment->city) }}" placeholder="e.g., Manila">
                                @error('city')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">State/Province</label>
                                <input type="text" name="state" class="form-control @error('state') error @enderror" 
                                       value="{{ old('state', $apartment->state) }}" placeholder="e.g., Metro Manila">
                                @error('state')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postal_code" class="form-control @error('postal_code') error @enderror" 
                                       value="{{ old('postal_code', $apartment->postal_code) }}" placeholder="e.g., 1234">
                                @error('postal_code')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-cogs"></i>
                            Property Details
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Number of Floors</label>
                                <input type="number" name="floors" class="form-control @error('floors') error @enderror" 
                                       value="{{ old('floors', $apartment->floors) }}" min="1" placeholder="e.g., 5">
                                @error('floors')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Parking Spaces</label>
                                <input type="number" name="parking_spaces" class="form-control @error('parking_spaces') error @enderror" 
                                       value="{{ old('parking_spaces', $apartment->parking_spaces) }}" min="0" placeholder="e.g., 20">
                                @error('parking_spaces')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control @error('contact_person') error @enderror" 
                                       value="{{ old('contact_person', $apartment->contact_person) }}" placeholder="e.g., John Doe">
                                @error('contact_person')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Contact Phone</label>
                                <input type="tel" name="contact_phone" class="form-control @error('contact_phone') error @enderror" 
                                       value="{{ old('contact_phone', $apartment->contact_phone) }}" placeholder="e.g., +63 912 345 6789">
                                @error('contact_phone')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control @error('contact_email') error @enderror" 
                                       value="{{ old('contact_email', $apartment->contact_email) }}" placeholder="e.g., contact@example.com">
                                @error('contact_email')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') error @enderror" 
                                      placeholder="Describe your property, its features, and what makes it special...">{{ old('description', $apartment->description) }}</textarea>
                            @error('description')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-star"></i>
                            Property Amenities
                        </h3>
                        <p class="form-help">Select the amenities available in your property</p>
                        
                        @php
                            $currentAmenities = is_array($apartment->amenities) ? $apartment->amenities : [];
                            $oldAmenities = old('amenities', []);
                            $selectedAmenities = !empty($oldAmenities) ? $oldAmenities : $currentAmenities;
                        @endphp
                        
                        <div class="amenities-grid">
                            <div class="amenity-item">
                                <input type="checkbox" id="pool" name="amenities[]" value="pool" {{ in_array('pool', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="pool">
                                    <i class="fas fa-swimming-pool"></i>
                                    Swimming Pool
                                </label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="gym" name="amenities[]" value="gym" {{ in_array('gym', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="gym">
                                    <i class="fas fa-dumbbell"></i>
                                    Gym/Fitness Center
                                </label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="parking" name="amenities[]" value="parking" {{ in_array('parking', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="parking">
                                    <i class="fas fa-parking"></i>
                                    Parking
                                </label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="security" name="amenities[]" value="security" {{ in_array('security', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="security">
                                    <i class="fas fa-shield-alt"></i>
                                    24/7 Security
                                </label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="elevator" name="amenities[]" value="elevator" {{ in_array('elevator', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="elevator">
                                    <i class="fas fa-arrow-up"></i>
                                    Elevator
                                </label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="laundry" name="amenities[]" value="laundry" {{ in_array('laundry', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="laundry">
                                    <i class="fas fa-tshirt"></i>
                                    Laundry Room
                                </label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="wifi" name="amenities[]" value="wifi" {{ in_array('wifi', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="wifi">
                                    <i class="fas fa-wifi"></i>
                                    Free WiFi
                                </label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="garden" name="amenities[]" value="garden" {{ in_array('garden', $selectedAmenities) ? 'checked' : '' }}>
                                <label for="garden">
                                    <i class="fas fa-seedling"></i>
                                    Garden/Green Space
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <div class="form-actions-left">
                            <a href="{{ route('landlord.apartments') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Delete Property
                            </button>
                        </div>
                        <div class="form-actions-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Update Property
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Delete Form -->
                <form id="delete-form" method="POST" action="{{ route('landlord.delete-apartment', $apartment->id) }}" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');

            // Real-time validation
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('error')) {
                        validateField(this);
                    }
                });
            });

            function validateField(field) {
                const value = field.value.trim();
                const isRequired = field.hasAttribute('required');
                
                if (isRequired && !value) {
                    showError(field, 'This field is required');
                } else if (field.type === 'email' && value && !isValidEmail(value)) {
                    showError(field, 'Please enter a valid email address');
                } else if (field.type === 'tel' && value && !isValidPhone(value)) {
                    showError(field, 'Please enter a valid phone number');
                } else {
                    clearError(field);
                }
            }

            function showError(field, message) {
                field.classList.add('error');
                let errorDiv = field.parentNode.querySelector('.form-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'form-error';
                    field.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = message;
            }

            function clearError(field) {
                field.classList.remove('error');
                const errorDiv = field.parentNode.querySelector('.form-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function isValidPhone(phone) {
                const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
                return phoneRegex.test(phone);
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                inputs.forEach(input => {
                    validateField(input);
                    if (input.classList.contains('error')) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fix the errors before submitting.');
                }
            });

            // Detect when total units is increased
            const totalUnitsInput = document.getElementById('total_units');
            const currentUnitCount = parseInt(document.getElementById('current_unit_count').value);
            const increaseNotice = document.getElementById('increase_units_notice');
            const additionalCountSpan = document.getElementById('additional_count');

            if (totalUnitsInput && increaseNotice) {
                totalUnitsInput.addEventListener('input', function() {
                    const newTotal = parseInt(this.value) || 0;
                    
                    if (newTotal > currentUnitCount) {
                        const additional = newTotal - currentUnitCount;
                        additionalCountSpan.textContent = additional;
                        increaseNotice.style.display = 'block';
                    } else {
                        increaseNotice.style.display = 'none';
                    }
                });
            }
        });

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this property? This action cannot be undone and will also delete all associated units.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</body>
</html> 