@extends('layouts.landlord-app')

@section('title', 'My Units')

@push('styles')
<style>
</style>
@endpush

@section('content')
<div class="content-header mb-4">
    <div>
        <h1>My Units</h1>
        <p style="color: #64748b; margin-top: 0.5rem;">Manage all your rental units</p>
    </div>
    <div class="user-profile">
        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div class="user-info">
            <h3>{{ auth()->user()->name }}</h3>
            <p>Property Manager</p>
        </div>
    </div>
</div>
@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif
<!-- Stats Cards -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total_units'] }}</div>
        <div class="stat-label">Total Units</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['available_units'] }}</div>
        <div class="stat-label">Available Units</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['occupied_units'] }}</div>
        <div class="stat-label">Occupied Units</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">₱{{ number_format($stats['monthly_revenue'], 0) }}</div>
        <div class="stat-label">Monthly Revenue</div>
    </div>
</div>
<!-- Units Section -->
<div class="page-section">
    <div class="section-header">
        <div>
            <h2 class="section-title">All Units</h2>
            <p class="section-subtitle">View and manage your rental units across all properties</p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <div class="sort-dropdown">
                <label style="margin-right: 0.5rem; font-size: 0.875rem; color: #64748b;">Sort by:</label>
                <select id="unitSort" onchange="window.location.href='?sort=' + this.value" style="padding: 0.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                    <option value="property_unit" {{ request('sort', 'property_unit') == 'property_unit' ? 'selected' : '' }}>Property → Floor → Unit</option>
                    <option value="floor" {{ request('sort') == 'floor' ? 'selected' : '' }}>Floor → Unit Number</option>
                    <option value="property" {{ request('sort') == 'property' ? 'selected' : '' }}>Property Name</option>
                    <option value="unit_number" {{ request('sort') == 'unit_number' ? 'selected' : '' }}>Unit Number Only</option>
                    <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status (Available First)</option>
                    <option value="rent" {{ request('sort') == 'rent' ? 'selected' : '' }}>Rent (Highest First)</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                </select>
            </div>
            <a href="{{ route('landlord.create-unit') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Unit
            </a>
        </div>
    </div>
    @if($units->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 12%;">Unit Number</th>
                        <th style="width: 18%;">Property</th>
                        <th style="width: 12%;">Type</th>
                        <th style="width: 10%;" class="text-center">Beds / Baths</th>
                        <th style="width: 8%;" class="text-center">Floor</th>
                        <th style="width: 10%;" class="text-center">Status</th>
                        <th style="width: 12%;" class="text-end">Rent/Month</th>
                        <th style="width: 8%;" class="text-center">Max Occupants</th>
                        <th style="width: 10%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($units as $unit)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="unit-number-badge">{{ $unit->unit_number }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-muted me-2"></i>
                                <span class="property-name">{{ $unit->apartment->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="unit-type">{{ str_replace('_', ' ', ucfirst($unit->unit_type ?? 'N/A')) }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <span class="bed-bath-info" title="Bedrooms">
                                    <i class="fas fa-bed text-muted me-1"></i>{{ $unit->bedrooms ?? 0 }}
                                </span>
                                <span class="bed-bath-info" title="Bathrooms">
                                    <i class="fas fa-bath text-muted me-1"></i>{{ $unit->bathrooms ?? 1 }}
                                </span>
                            </div>
                        </td>
                        <td class="text-center"><span class="floor-number">{{ $unit->floor_number ?? 'N/A' }}</span></td>
                        <td class="text-center">
                            @php
                                $statusConfig = [
                                    'available' => ['class' => 'badge bg-success', 'text' => 'Available'],
                                    'occupied' => ['class' => 'badge bg-danger', 'text' => 'Occupied'],
                                    'maintenance' => ['class' => 'badge bg-warning', 'text' => 'Maintenance'],
                                ];
                                $config = $statusConfig[$unit->status] ?? ['class' => 'badge bg-secondary', 'text' => ucfirst($unit->status)];
                            @endphp
                            <span class="{{ $config['class'] }}">{{ $config['text'] }}</span>
                        </td>
                        <td class="text-end"><span class="rent-amount">₱{{ number_format($unit->rent_amount ?? 0, 0) }}</span></td>
                        <td class="text-center"><span class="max-occupants">{{ $unit->max_occupants ?? '-' }}</span></td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button onclick="editUnit({{ $unit->id }})" class="btn btn-sm btn-outline-primary" title="Edit Unit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="viewUnitDetails({{ $unit->id }})" class="btn btn-sm btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        @if($units->hasPages())
            <div class="pagination mt-4">
                {{ $units->appends(['sort' => request('sort')])->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-door-open"></i></div>
            <h3 class="empty-title">No Units Found</h3>
            <p class="empty-text">
                @if(request()->hasAny(['search', 'status', 'apartment']))
                    No units match your search criteria. Try adjusting your filters.
                @else
                    You haven't added any units yet. Start by adding units to your properties.
                @endif
            </p>
            @if(request()->hasAny(['search', 'status', 'apartment']))
                <a href="{{ route('landlord.units') }}" class="btn btn-primary"><i class="fas fa-refresh"></i> Clear Filters</a>
            @else
                <a href="{{ route('landlord.apartments') }}" class="btn btn-primary"><i class="fas fa-building"></i> Go to Properties</a>
            @endif
        </div>
    @endif
</div>
<!-- Modals and JS remain below as before -->
@endsection

    <script>

        function editUnit(unitId) {
            // Show the edit modal
            const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
            const modalTitle = document.getElementById('editUnitModalLabel');
            const modalContent = document.getElementById('editUnitContent');
            const saveBtn = document.getElementById('saveUnitBtn');
            const form = document.getElementById('editUnitForm');
            
            // Reset modal content
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading unit details...</p>
                </div>
            `;
            saveBtn.style.display = 'none';
            
            // Show modal
            modal.show();
            
            // Fetch unit data
            fetch(`/landlord/units/${unitId}/details`)
                .then(response => response.json())
                .then(data => {
                    modalTitle.textContent = `Edit Unit ${data.unit_number}`;
                    form.action = `/landlord/units/${unitId}`;
                    
                    // Generate form content
                    modalContent.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_unit_number" class="form-label">Unit Number *</label>
                                    <input type="text" class="form-control" id="edit_unit_number" name="unit_number" value="${data.unit_number}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_unit_type" class="form-label">Unit Type *</label>
                                    <select class="form-control" id="edit_unit_type" name="unit_type" required>
                                        <option value="studio" ${data.unit_type === 'studio' ? 'selected' : ''}>Studio</option>
                                        <option value="one_bedroom" ${data.unit_type === 'one_bedroom' ? 'selected' : ''}>One Bedroom</option>
                                        <option value="two_bedroom" ${data.unit_type === 'two_bedroom' ? 'selected' : ''}>Two Bedroom</option>
                                        <option value="three_bedroom" ${data.unit_type === 'three_bedroom' ? 'selected' : ''}>Three Bedroom</option>
                                        <option value="penthouse" ${data.unit_type === 'penthouse' ? 'selected' : ''}>Penthouse</option>
                                    </select>
                                    <small class="form-text text-muted">Bedrooms will auto-update based on selection</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_rent_amount" class="form-label">Monthly Rent (₱) *</label>
                                    <input type="number" class="form-control" id="edit_rent_amount" name="rent_amount" value="${data.rent_amount}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status *</label>
                                    <select class="form-control" id="edit_status" name="status" required>
                                        <option value="available" ${data.status === 'available' ? 'selected' : ''}>Available</option>
                                        <option value="occupied" ${data.status === 'occupied' ? 'selected' : ''}>Occupied</option>
                                        <option value="maintenance" ${data.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_leasing_type" class="form-label">Leasing Type *</label>
                                    <select class="form-control" id="edit_leasing_type" name="leasing_type" required>
                                        <option value="separate" ${data.leasing_type === 'separate' ? 'selected' : ''}>Separate (Utilities not included)</option>
                                        <option value="inclusive" ${data.leasing_type === 'inclusive' ? 'selected' : ''}>Inclusive (Utilities included)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_bedrooms" class="form-label">Bedrooms *</label>
                                    <input type="number" class="form-control" id="edit_bedrooms" name="bedrooms" value="${data.bedrooms}" min="0" required readonly>
                                    <small class="form-text text-muted">Auto-filled based on unit type</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_bathrooms" class="form-label">Bathrooms *</label>
                                    <input type="number" class="form-control" id="edit_bathrooms" name="bathrooms" value="${data.bathrooms}" min="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="edit_is_furnished" name="is_furnished" value="1" ${data.is_furnished ? 'checked' : ''}>
                                <label class="form-check-label" for="edit_is_furnished">
                                    Furnished Unit
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Amenities</label>
                            <div class="row">
                                ${generateAmenitiesCheckboxes(data.amenities || [])}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3">${data.description || ''}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="2">${data.notes || ''}</textarea>
                        </div>
                    `;
                    
                    // Show save button
                    saveBtn.style.display = 'inline-block';
                    
                    // Add event listener for unit type change to auto-populate bedrooms
                    const editUnitTypeSelect = document.getElementById('edit_unit_type');
                    const editBedroomsInput = document.getElementById('edit_bedrooms');
                    
                    if (editUnitTypeSelect && editBedroomsInput) {
                        editUnitTypeSelect.addEventListener('change', function() {
                            const unitType = this.value;
                            let bedroomCount = 0;
                            
                            switch(unitType) {
                                case 'studio':
                                    bedroomCount = 0;
                                    break;
                                case 'one_bedroom':
                                    bedroomCount = 1;
                                    break;
                                case 'two_bedroom':
                                    bedroomCount = 2;
                                    break;
                                case 'three_bedroom':
                                    bedroomCount = 3;
                                    break;
                                case 'penthouse':
                                    bedroomCount = 3; // Default for penthouse
                                    break;
                                default:
                                    bedroomCount = 0;
                            }
                            
                            editBedroomsInput.value = bedroomCount;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading unit details:', error);
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Error loading unit details. Please try again.
                        </div>
                    `;
                });
        }
        
        function generateAmenitiesCheckboxes(amenities) {
            const allAmenities = [
                { value: 'aircon', label: 'Air Conditioning' },
                { value: 'heating', label: 'Heating' },
                { value: 'balcony', label: 'Balcony' },
                { value: 'parking', label: 'Parking' },
                { value: 'gym', label: 'Gym Access' },
                { value: 'pool', label: 'Pool Access' },
                { value: 'wifi', label: 'WiFi' },
                { value: 'laundry', label: 'Laundry' }
            ];
            
            return allAmenities.map(amenity => {
                const checked = amenities.includes(amenity.value) ? 'checked' : '';
                return `
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit_amenity_${amenity.value}" name="amenities[]" value="${amenity.value}" ${checked}>
                            <label class="form-check-label" for="edit_amenity_${amenity.value}">
                                ${amenity.label}
                            </label>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Handle form submission
        document.addEventListener('DOMContentLoaded', function() {
            const editForm = document.getElementById('editUnitForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitBtn = document.getElementById('saveUnitBtn');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('editUnitModal')).hide();
                            
                            // Show success message and reload page
                            alert('Unit updated successfully!');
                            location.reload();
                        } else {
                            // Show error message
                            alert(data.message || 'An error occurred while updating the unit.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the unit.');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });

        function viewUnitDetails(unitId) {
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('unitDetailsModal'));
            const modalTitle = document.getElementById('unitDetailsModalLabel');
            const modalContent = document.getElementById('unitDetailsContent');
            const editBtn = document.getElementById('editUnitBtn');
            
            // Reset modal content
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading unit details...</p>
                </div>
            `;
            editBtn.style.display = 'none';
            
            modal.show();
            
            // Fetch unit details
            fetch(`/landlord/units/${unitId}/details`)
                .then(response => response.json())
                .then(data => {
                    modalTitle.textContent = `Unit ${data.unit_number} - Details`;
                    
                    // Create the details HTML
                    modalContent.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Unit Information</h6>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Unit Number</label>
                                    <p class="mb-1">${data.unit_number}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Property</label>
                                    <p class="mb-1">${data.apartment_name}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Unit Type</label>
                                    <p class="mb-1">${data.unit_type ? data.unit_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Not specified'}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Monthly Rent</label>
                                    <p class="mb-1 text-success fw-bold">₱${Number(data.rent_amount).toLocaleString()}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Status</label>
                                    <p class="mb-1">
                                        <span class="badge bg-${data.status === 'occupied' ? 'success' : data.status === 'available' ? 'warning' : 'danger'}">
                                            ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Unit Specifications</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2 mb-3">
                                            <h4 class="text-primary mb-0">${data.bedrooms || 0}</h4>
                                            <small class="text-muted">Bedrooms</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2 mb-3">
                                            <h4 class="text-info mb-0">${data.bathrooms || 0}</h4>
                                            <small class="text-muted">Bathrooms</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Max Occupants</label>
                                    <p class="mb-1">${data.max_occupants || 'Not specified'}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Floor Number</label>
                                    <p class="mb-1">${data.floor_number || 'Not specified'}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted">Furnished</label>
                                    <p class="mb-1">
                                        <span class="badge bg-${data.is_furnished ? 'success' : 'secondary'}">
                                            ${data.is_furnished ? 'Yes' : 'No'}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        ${data.current_tenant ? `
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Current Tenant</h6>
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Name:</strong> ${data.current_tenant.name}<br>
                                            <strong>Email:</strong> ${data.current_tenant.email}<br>
                                            ${data.current_tenant.phone ? `<strong>Phone:</strong> ${data.current_tenant.phone}<br>` : ''}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Lease Start:</strong> ${data.current_tenant.lease_start}<br>
                                            <strong>Lease End:</strong> ${data.current_tenant.lease_end}<br>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="/landlord/tenant-assignments/${data.current_tenant.assignment_id}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Assignment Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${data.amenities && data.amenities.length > 0 ? `
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Amenities</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    ${data.amenities.map(amenity => `<span class="badge bg-soft-primary text-primary"><i class="fas fa-check me-1"></i>${amenity}</span>`).join('')}
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${data.description ? `
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Description</h6>
                                <p class="text-muted">${data.description}</p>
                            </div>
                        </div>
                        ` : ''}
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Quick Actions</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    ${data.status === 'available' ? `
                                        <a href="/landlord/tenant-assignments?unit_id=${data.id}" class="btn btn-success btn-sm">
                                            <i class="fas fa-user-plus"></i> Assign Tenant
                                        </a>
                                    ` : ''}
                                    <a href="/landlord/tenant-assignments?unit_id=${data.id}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-history"></i> Assignment History
                                    </a>
                                    <a href="/landlord/units/${data.apartment_id}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-building"></i> View Property
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Show edit button and set up click handler
                    editBtn.style.display = 'inline-block';
                    editBtn.onclick = function() {
                        editUnit(data.id);
                    };
                })
                .catch(error => {
                    console.error('Error fetching unit details:', error);
                    modalContent.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-danger">Error Loading Details</h5>
                            <p class="text-muted">Failed to load unit details. Please try again.</p>
                            <button class="btn btn-primary" onclick="viewUnitDetails(${unitId})">Retry</button>
                        </div>
                    `;
                });
        }

        // assignTenant and vacateUnit removed; tenant actions handled in Tenant Assignments tab

        function deleteUnit(unitId, unitNumber) {
            if (confirm(`Are you sure you want to delete Unit ${unitNumber}? This action cannot be undone.\n\nNote: You cannot delete units with active tenant assignments.`)) {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/landlord/units/${unitId}`;
                
                // CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                // DELETE method
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }

    </script>

    <!-- Unit Details Modal -->
    <div class="modal fade" id="unitDetailsModal" tabindex="-1" aria-labelledby="unitDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitDetailsModalLabel">Unit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="unitDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading unit details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editUnitBtn" style="display: none;">
                        <i class="fas fa-edit"></i> Edit Unit
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Unit Modal -->
    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUnitModalLabel">Edit Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUnitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body" id="editUnitContent">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading unit details...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveUnitBtn" style="display: none;">
                            <i class="fas fa-save"></i> Update Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html> 