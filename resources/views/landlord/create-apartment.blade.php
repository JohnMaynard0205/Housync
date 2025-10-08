@extends('layouts.landlord-app')

@section('title', 'Add New Property')

@push('styles')
<style>
    /* Progress Indicator */
    .progress-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 3rem;
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        position: relative;
    }

    .progress-step i {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .progress-step.active i {
        background: #f97316;
        color: white;
    }

    .progress-step span {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
    }

    .progress-step.active span {
        color: #f97316;
        font-weight: 600;
    }

    .progress-connector {
        width: 100px;
        height: 2px;
        background: #e2e8f0;
        margin: 0 0.5rem;
    }

    /* Form Styles */
    .form-container {
        max-width: 100%;
    }

    .form-section {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .form-section-title i {
        color: #f97316;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-weight: 500;
        color: #1e293b;
        font-size: 0.875rem;
    }

    .form-label.required::after {
        content: " *";
        color: #ef4444;
    }

    .form-control {
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }

    .form-control.error {
        border-color: #ef4444;
    }

    .form-error {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .form-help, .form-text.text-muted {
        color: #64748b;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    /* Custom Checkbox */
    .custom-control {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .custom-control-input {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .custom-control-label {
        cursor: pointer;
        user-select: none;
    }

    /* Amenities Grid */
    .amenities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .amenity-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .amenity-item:hover {
        border-color: #f97316;
        background: #fff7ed;
    }

    .amenity-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .amenity-item input[type="checkbox"]:checked + label {
        color: #f97316;
        font-weight: 600;
    }

    .amenity-item label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.875rem;
        margin: 0;
    }

    .amenity-item i {
        color: #f97316;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 2rem;
        border-top: 2px solid #e2e8f0;
        margin-top: 2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .progress-connector {
            width: 50px;
        }

        .amenities-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="content-header">
        <div>
            <h1>Add New Property</h1>
            <p style="color: #64748b; margin-top: 0.5rem;">Create a new property in your portfolio</p>
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

    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="progress-step active">
            <i class="fas fa-building"></i>
            <span>Property Details</span>
        </div>
        <div class="progress-connector"></div>
        <div class="progress-step">
            <i class="fas fa-door-open"></i>
            <span>Add Units</span>
        </div>
        <div class="progress-connector"></div>
        <div class="progress-step">
            <i class="fas fa-check"></i>
            <span>Complete</span>
        </div>
    </div>

    <!-- Form Section -->
    <div class="page-section">
        <div class="section-header">
            <div>
                <h2 class="section-title">Property Information</h2>
                <p class="section-subtitle">Fill in the details for your new property</p>
            </div>
        </div>

        <form method="POST" action="{{ route('landlord.store-apartment') }}" class="form-container" enctype="multipart/form-data">
            @csrf
            
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
                               value="{{ old('name') }}" placeholder="e.g., Sunshine Apartments" required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Property Type</label>
                        <select name="property_type" class="form-control @error('property_type') error @enderror" required>
                            <option value="">Select property type</option>
                            <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment Building</option>
                            <option value="condominium" {{ old('property_type') == 'condominium' ? 'selected' : '' }}>Condominium</option>
                            <option value="townhouse" {{ old('property_type') == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                            <option value="house" {{ old('property_type') == 'house' ? 'selected' : '' }}>Single Family House</option>
                            <option value="duplex" {{ old('property_type') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                        </select>
                        @error('property_type')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Total Units</label>
                        <input type="number" name="total_units" id="total_units" class="form-control @error('total_units') error @enderror" 
                               value="{{ old('total_units') }}" min="1" placeholder="e.g., 24" required>
                        @error('total_units')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Enter the number of units in this property</small>
                    </div>

                    <div class="form-group full-width">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="auto_generate_units" name="auto_generate_units" 
                                   value="1" {{ old('auto_generate_units', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="auto_generate_units">
                                <strong>Auto-generate units</strong> (Automatically create unit records based on total units count)
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            ✨ Recommended for properties with many units! This will create all unit records automatically.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Auto-Generation Settings (shown when auto-generate is checked) -->
            <div class="form-section" id="auto_gen_settings" style="display: none;">
                <h3 class="form-section-title">
                    <i class="fas fa-magic"></i>
                    Auto-Generation Settings
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Default Unit Type</label>
                        <select name="default_unit_type" class="form-control">
                            <option value="Studio" {{ old('default_unit_type') == 'Studio' ? 'selected' : '' }}>Studio</option>
                            <option value="1-Bedroom" {{ old('default_unit_type') == '1-Bedroom' ? 'selected' : '' }}>1-Bedroom</option>
                            <option value="2-Bedroom" {{ old('default_unit_type', '2-Bedroom') == '2-Bedroom' ? 'selected' : '' }}>2-Bedroom</option>
                            <option value="3-Bedroom" {{ old('default_unit_type') == '3-Bedroom' ? 'selected' : '' }}>3-Bedroom</option>
                            <option value="Apartment" {{ old('default_unit_type') == 'Apartment' ? 'selected' : '' }}>Apartment</option>
                        </select>
                        <small class="form-text text-muted">Default type for all generated units</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Default Rent Amount (₱)</label>
                        <input type="number" name="default_rent" class="form-control" 
                               value="{{ old('default_rent', 15000) }}" min="0" step="100" placeholder="15000">
                        <small class="form-text text-muted">You can edit individual unit prices later</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Units Per Floor</label>
                        <input type="number" name="units_per_floor" id="units_per_floor" class="form-control" 
                               value="{{ old('units_per_floor') }}" min="1" placeholder="Auto-calculated">
                        <small class="form-text text-muted">Will use number of floors from Property Details below</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Default Bedrooms</label>
                        <input type="number" name="default_bedrooms" class="form-control" 
                               value="{{ old('default_bedrooms', 2) }}" min="0" max="10">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Default Bathrooms</label>
                        <input type="number" name="default_bathrooms" class="form-control" 
                               value="{{ old('default_bathrooms', 1) }}" min="1" max="10">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Numbering Pattern</label>
                        <select name="numbering_pattern" class="form-control">
                            <option value="floor_based" {{ old('numbering_pattern', 'floor_based') == 'floor_based' ? 'selected' : '' }}>Floor-Based (101, 102, 201, 202...)</option>
                            <option value="sequential" {{ old('numbering_pattern') == 'sequential' ? 'selected' : '' }}>Sequential (Unit 1, Unit 2, Unit 3...)</option>
                            <option value="letter_number" {{ old('numbering_pattern') == 'letter_number' ? 'selected' : '' }}>Letter-Number (A1, A2, B1, B2...)</option>
                        </select>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> All generated units will be created with status "Available". You can edit individual units later to customize details.
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-calendar"></i>
                    Building Information
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Year Built</label>
                        <input type="number" name="year_built" class="form-control @error('year_built') error @enderror" 
                               value="{{ old('year_built') }}" min="1900" max="{{ date('Y') }}" placeholder="e.g., 2020">
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
                               value="{{ old('address') }}" placeholder="e.g., 123 Main Street" required>
                        @error('address')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city') error @enderror" 
                               value="{{ old('city') }}" placeholder="e.g., Manila">
                        @error('city')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">State/Province</label>
                        <input type="text" name="state" class="form-control @error('state') error @enderror" 
                               value="{{ old('state') }}" placeholder="e.g., Metro Manila">
                        @error('state')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control @error('postal_code') error @enderror" 
                               value="{{ old('postal_code') }}" placeholder="e.g., 1234">
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
                        <input type="number" name="floors" id="floors" class="form-control @error('floors') error @enderror" 
                               value="{{ old('floors') }}" min="1" placeholder="e.g., 5">
                        @error('floors')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Parking Spaces</label>
                        <input type="number" name="parking_spaces" class="form-control @error('parking_spaces') error @enderror" 
                               value="{{ old('parking_spaces') }}" min="0" placeholder="e.g., 20">
                        @error('parking_spaces')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control @error('contact_person') error @enderror" 
                               value="{{ old('contact_person') }}" placeholder="e.g., John Doe">
                        @error('contact_person')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Phone</label>
                        <input type="tel" name="contact_phone" class="form-control @error('contact_phone') error @enderror" 
                               value="{{ old('contact_phone') }}" placeholder="e.g., +63 912 345 6789">
                        @error('contact_phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control @error('contact_email') error @enderror" 
                               value="{{ old('contact_email') }}" placeholder="e.g., contact@example.com">
                        @error('contact_email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') error @enderror" 
                              placeholder="Describe your property, its features, and what makes it special...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Property Photos -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-image"></i>
                    Photos
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Cover Image</label>
                        <input type="file" name="cover_image" accept="image/*" class="form-control">
                        <p class="form-help">JPEG/PNG up to 3MB</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gallery (up to 8)</label>
                        <input type="file" name="gallery[]" accept="image/*" multiple class="form-control">
                        <p class="form-help">Add more photos to attract tenants</p>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-star"></i>
                    Property Amenities
                </h3>
                <p class="form-help">Select the amenities available in your property</p>
                
                <div class="amenities-grid">
                    <div class="amenity-item">
                        <input type="checkbox" id="pool" name="amenities[]" value="pool" {{ in_array('pool', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="pool">
                            <i class="fas fa-swimming-pool"></i>
                            Swimming Pool
                        </label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" id="gym" name="amenities[]" value="gym" {{ in_array('gym', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="gym">
                            <i class="fas fa-dumbbell"></i>
                            Gym/Fitness Center
                        </label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" id="parking" name="amenities[]" value="parking" {{ in_array('parking', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="parking">
                            <i class="fas fa-parking"></i>
                            Parking
                        </label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" id="security" name="amenities[]" value="security" {{ in_array('security', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="security">
                            <i class="fas fa-shield-alt"></i>
                            24/7 Security
                        </label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" id="elevator" name="amenities[]" value="elevator" {{ in_array('elevator', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="elevator">
                            <i class="fas fa-arrow-up"></i>
                            Elevator
                        </label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" id="laundry" name="amenities[]" value="laundry" {{ in_array('laundry', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="laundry">
                            <i class="fas fa-tshirt"></i>
                            Laundry Room
                        </label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" id="wifi" name="amenities[]" value="wifi" {{ in_array('wifi', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="wifi">
                            <i class="fas fa-wifi"></i>
                            Free WiFi
                        </label>
                    </div>
                    <div class="amenity-item">
                        <input type="checkbox" id="garden" name="amenities[]" value="garden" {{ in_array('garden', old('amenities', [])) ? 'checked' : '' }}>
                        <label for="garden">
                            <i class="fas fa-seedling"></i>
                            Garden/Green Space
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('landlord.apartments') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Create Property
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
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

        // Auto-generation toggle
        const autoGenCheckbox = document.getElementById('auto_generate_units');
        const autoGenSettings = document.getElementById('auto_gen_settings');
        const totalUnitsInput = document.getElementById('total_units');
        const floorsInput = document.getElementById('floors');
        const unitsPerFloorInput = document.getElementById('units_per_floor');

        // Show/hide auto-generation settings
        if (autoGenCheckbox) {
            autoGenCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    autoGenSettings.style.display = 'block';
                } else {
                    autoGenSettings.style.display = 'none';
                }
            });

            // Initial state
            if (autoGenCheckbox.checked) {
                autoGenSettings.style.display = 'block';
            }
        }

        // Auto-calculate units per floor
        function calculateUnitsPerFloor() {
            const totalUnits = parseInt(totalUnitsInput.value) || 0;
            const numFloors = parseInt(floorsInput.value) || 1;
            
            if (totalUnits > 0 && numFloors > 0 && !unitsPerFloorInput.value) {
                const perFloor = Math.ceil(totalUnits / numFloors);
                unitsPerFloorInput.placeholder = `Auto: ${perFloor} units/floor`;
            }
        }

        if (totalUnitsInput && floorsInput) {
            totalUnitsInput.addEventListener('input', calculateUnitsPerFloor);
            floorsInput.addEventListener('input', calculateUnitsPerFloor);
            calculateUnitsPerFloor();
        }
    });
</script>
@endpush
