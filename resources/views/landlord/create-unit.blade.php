<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Unit - Housesync</title>
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
            text-decoration: none;
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

        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.875rem;
            background-color: rgba(255,255,255,0.1);
            border: none;
            border-radius: 0.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background-color: rgba(255,255,255,0.2);
            text-decoration: none;
            color: white;
        }

        .logout-btn i {
            margin-right: 0.75rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 1rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .breadcrumb a {
            color: #ea580c;
            text-decoration: none;
        }

        .breadcrumb i {
            margin: 0 0.5rem;
        }

        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
        }

        .form-control.error {
            border-color: #ef4444;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: 1.25rem;
            height: 1.25rem;
            accent-color: #ea580c;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: 500;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .amenity-item input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: #ea580c;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #ea580c;
            color: white;
        }

        .btn-primary:hover {
            background-color: #dc2626;
            text-decoration: none;
            color: white;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
            text-decoration: none;
            color: white;
        }

        .btn-outline {
            background-color: transparent;
            color: #ea580c;
            border: 1px solid #ea580c;
        }

        .btn-outline:hover {
            background-color: #ea580c;
            color: white;
            text-decoration: none;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        /* Property Info */
        .property-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .property-info h4 {
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 1.125rem;
        }

        .property-info p {
            color: #64748b;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Success/Error Messages */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Housesync</h2>
                <p>Property Management</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('landlord.dashboard') }}" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="{{ route('landlord.apartments') }}" class="nav-item">
                    <i class="fas fa-building"></i>
                    Properties
                </a>
                <a href="{{ route('landlord.units') }}" class="nav-item active">
                    <i class="fas fa-home"></i>
                    Units
                </a>
                <a href="{{ route('landlord.tenant-assignments') }}" class="nav-item">
                    <i class="fas fa-users"></i>
                    Tenant Assignments
                </a>
                <a href="{{ route('landlord.staff') }}" class="nav-item">
                    <i class="fas fa-user-tie"></i>
                    Staff Management
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="{{ route('landlord.dashboard') }}">Dashboard</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('landlord.units') }}">Units</a>
                <i class="fas fa-chevron-right"></i>
                <span>Add New Unit</span>
            </div>

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Add New Unit</h1>
                <p class="page-subtitle">Create a new rental unit for your property</p>
            </div>

            <!-- Form Container -->
            <div class="form-container">
                @if(isset($apartment))
                <!-- Property Info -->
                <div class="property-info">
                    <h4>Property Information</h4>
                    <p><strong>Property:</strong> {{ $apartment->name }}</p>
                    <p><strong>Address:</strong> {{ $apartment->address }}</p>
                </div>
                @endif

                <form method="POST" action="{{ isset($apartment) ? route('landlord.store-unit', $apartment->id) : route('landlord.create-unit') }}">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="section-title">Basic Information</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="unit_number" class="form-label">Unit Number *</label>
                                <input type="text" id="unit_number" name="unit_number" class="form-control @error('unit_number') error @enderror" 
                                       value="{{ old('unit_number') }}" placeholder="e.g., A101, 1A, etc." required>
                                @error('unit_number')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="unit_type" class="form-label">Unit Type *</label>
                                <select id="unit_type" name="unit_type" class="form-control @error('unit_type') error @enderror" required>
                                    <option value="">Select Unit Type</option>
                                    <option value="studio" {{ old('unit_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                                    <option value="one_bedroom" {{ old('unit_type') == 'one_bedroom' ? 'selected' : '' }}>One Bedroom</option>
                                    <option value="two_bedroom" {{ old('unit_type') == 'two_bedroom' ? 'selected' : '' }}>Two Bedroom</option>
                                    <option value="three_bedroom" {{ old('unit_type') == 'three_bedroom' ? 'selected' : '' }}>Three Bedroom</option>
                                    <option value="penthouse" {{ old('unit_type') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                                    <option value="duplex" {{ old('unit_type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                                </select>
                                @error('unit_type')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="rent_amount" class="form-label">Monthly Rent (₱) *</label>
                                <input type="number" id="rent_amount" name="rent_amount" class="form-control @error('rent_amount') error @enderror" 
                                       value="{{ old('rent_amount') }}" placeholder="0.00" min="0" step="0.01" required>
                                @error('rent_amount')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="status" class="form-label">Status *</label>
                                <select id="status" name="status" class="form-control @error('status') error @enderror" required>
                                    <option value="">Select Status</option>
                                    <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="leasing_type" class="form-label">Leasing Type *</label>
                                <select id="leasing_type" name="leasing_type" class="form-control @error('leasing_type') error @enderror" required>
                                    <option value="">Select Leasing Type</option>
                                    <option value="separate" {{ old('leasing_type') == 'separate' ? 'selected' : '' }}>Separate (Utilities not included)</option>
                                    <option value="inclusive" {{ old('leasing_type') == 'inclusive' ? 'selected' : '' }}>Inclusive (Utilities included)</option>
                                </select>
                                @error('leasing_type')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="floor_area" class="form-label">Floor Area (sq ft)</label>
                                <input type="number" id="floor_area" name="floor_area" class="form-control @error('floor_area') error @enderror" 
                                       value="{{ old('floor_area') }}" placeholder="0" min="0" step="0.01">
                                @error('floor_area')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Room Configuration -->
                    <div class="form-section">
                        <h3 class="section-title">Room Configuration</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bedrooms" class="form-label">Number of Bedrooms *</label>
                                <input type="number" id="bedrooms" name="bedrooms" class="form-control @error('bedrooms') error @enderror" 
                                       value="{{ old('bedrooms', 0) }}" placeholder="0" min="0" required>
                                @error('bedrooms')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="bathrooms" class="form-label">Number of Bathrooms *</label>
                                <input type="number" id="bathrooms" name="bathrooms" class="form-control @error('bathrooms') error @enderror" 
                                       value="{{ old('bathrooms', 1) }}" placeholder="1" min="1" required>
                                @error('bathrooms')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_furnished" name="is_furnished" value="1" {{ old('is_furnished') ? 'checked' : '' }}>
                                <label for="is_furnished">Furnished Unit</label>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="form-section">
                        <h3 class="section-title">Amenities</h3>
                        <p style="color: #64748b; margin-bottom: 1rem;">Select the amenities available in this unit:</p>
                        
                        <div class="amenities-grid">
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_aircon" name="amenities[]" value="aircon" {{ in_array('aircon', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_aircon">Air Conditioning</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_heating" name="amenities[]" value="heating" {{ in_array('heating', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_heating">Heating</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_balcony" name="amenities[]" value="balcony" {{ in_array('balcony', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_balcony">Balcony</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_parking" name="amenities[]" value="parking" {{ in_array('parking', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_parking">Parking</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_gym" name="amenities[]" value="gym" {{ in_array('gym', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_gym">Gym Access</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_pool" name="amenities[]" value="pool" {{ in_array('pool', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_pool">Pool Access</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_wifi" name="amenities[]" value="wifi" {{ in_array('wifi', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_wifi">WiFi</label>
                            </div>
                            <div class="amenity-item">
                                <input type="checkbox" id="amenity_laundry" name="amenities[]" value="laundry" {{ in_array('laundry', old('amenities', [])) ? 'checked' : '' }}>
                                <label for="amenity_laundry">Laundry</label>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section">
                        <h3 class="section-title">Additional Information</h3>
                        
                        <div class="form-group full-width">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control @error('description') error @enderror" 
                                      rows="4" placeholder="Describe the unit, its features, and any special characteristics...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea id="notes" name="notes" class="form-control @error('notes') error @enderror" 
                                      rows="3" placeholder="Any additional notes or special instructions...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="{{ route('landlord.units') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Create Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const rentInput = document.getElementById('rent_amount');
            const unitNumberInput = document.getElementById('unit_number');

            // Format rent amount
            rentInput.addEventListener('input', function() {
                let value = this.value.replace(/[^\d.]/g, '');
                if (value) {
                    value = parseFloat(value).toFixed(2);
                    this.value = value;
                }
            });

            // Auto-generate unit number if empty
            unitNumberInput.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    const unitType = document.getElementById('unit_type').value;
                    if (unitType) {
                        const timestamp = Date.now().toString().slice(-4);
                        this.value = `${unitType.charAt(0).toUpperCase()}${timestamp}`;
                    }
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('error');
                        isValid = false;
                    } else {
                        field.classList.remove('error');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        });
    </script>
</body>
</html> 