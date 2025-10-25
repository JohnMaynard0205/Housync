@extends('layouts.landlord-app')

@section('title', 'Edit Bulk Units')

@push('styles')
<style>
    .bulk-edit-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .floor-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        border: 1px solid #e2e8f0;
    }

    .floor-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .floor-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .floor-controls {
        display: flex;
        gap: 1rem;
        align-items: center;
    }


    .bulk-actions {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        border: 1px solid #e2e8f0;
    }

    .summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    /* Table Styles */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .table thead th {
        background: #f8fafc;
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        border-bottom: 2px solid #e2e8f0;
    }

    .table tbody td {
        padding: 0.75rem;
        border: none;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr.table-warning {
        background: #fef3c7;
    }

    .form-control-sm {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    .btn-group .btn {
        border-radius: 0.375rem;
        margin: 0 0.125rem;
    }

    .btn-group .btn:first-child {
        margin-left: 0;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    @media (max-width: 768px) {
        .table-responsive {
            border-radius: 0.5rem;
        }
        
        .table thead th,
        .table tbody td {
            padding: 0.5rem 0.375rem;
            font-size: 0.8rem;
        }
        
        .form-control-sm {
            padding: 0.25rem 0.375rem;
            font-size: 0.8rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .floor-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="bulk-edit-container">
    <!-- Header -->
    <div class="content-header">
        <div>
            <h1>Edit Bulk Units</h1>
            <p style="color: #64748b; margin-top: 0.5rem;">Customize your generated units before finalizing</p>
        </div>
    </div>

    @if(isset($apartment))
    <!-- Property Info -->
    <div class="property-info" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
        <h4 style="color: #1e293b; margin-bottom: 0.5rem; font-size: 1.125rem;">{{ $apartment->name }}</h4>
        <p style="color: #64748b; margin: 0;"><strong>Type:</strong> {{ ucfirst($apartment->property_type) }} | 
           <strong>Floors:</strong> {{ $apartment->floors ?? 'Not specified' }} | 
           <strong>Address:</strong> {{ $apartment->address }}</p>
    </div>
    @endif

    <!-- Summary Stats -->
    <div class="summary-stats">
        <div class="stat-card">
            <div class="stat-number" id="totalUnits">0</div>
            <div class="stat-label">Total Units</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="totalFloors">{{ $apartment->floors ?? 1 }}</div>
            <div class="stat-label">Floors</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" id="avgUnitsPerFloor">0</div>
            <div class="stat-label">Avg Units/Floor</div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions">
        <h3 style="margin-bottom: 1rem; color: #1e293b;">Bulk Actions</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <button type="button" class="btn btn-outline" onclick="applyToAllFloors()">
                <i class="fas fa-copy"></i> Apply to All Floors
            </button>
            <button type="button" class="btn btn-outline" onclick="duplicateFloor()">
                <i class="fas fa-clone"></i> Duplicate Floor
            </button>
            <button type="button" class="btn btn-outline" onclick="addNewFloor()">
                <i class="fas fa-plus"></i> Add Floor
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('landlord.finalize-bulk-units', $apartment->id) }}" id="bulkEditForm">
        @csrf
        
        <!-- Floors Container -->
        <div id="floorsContainer">
            @for($floor = 1; $floor <= ($apartment->floors ?? 1); $floor++)
            <div class="floor-section" data-floor="{{ $floor }}">
                <div class="floor-header">
                    <h3 class="floor-title">Floor {{ $floor }}</h3>
                    <div class="floor-controls">
                        <button type="button" class="btn btn-sm btn-outline" onclick="addUnitToFloor({{ $floor }})">
                            <i class="fas fa-plus"></i> Add Unit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="removeFloor({{ $floor }})" style="color: #ef4444;">
                            <i class="fas fa-trash"></i> Remove Floor
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="floor-{{ $floor }}-units-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Unit Number</th>
                                <th style="width: 15%;">Unit Type</th>
                                <th style="width: 12%;" class="text-center">Beds / Baths</th>
                                <th style="width: 12%;" class="text-end">Rent (₱)</th>
                                <th style="width: 10%;" class="text-center">Status</th>
                                <th style="width: 10%;" class="text-center">Max Occupants</th>
                                <th style="width: 12%;" class="text-center">Leasing Type</th>
                                <th style="width: 17%;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="floor-{{ $floor }}-units">
                            <!-- Units will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            @endfor
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 2rem; border-top: 1px solid #e5e7eb; margin-top: 2rem;">
            <a href="{{ route('landlord.units', $apartment->id) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary" onclick="debugFormSubmission()">
                <i class="fas fa-save"></i> Finalize Units
            </button>
        </div>
    </form>
</div>

<script>
let unitsData = {};
let currentFloor = 1;

document.addEventListener('DOMContentLoaded', async function() {
    await initializeBulkEdit();
});

async function initializeBulkEdit() {
    // Initialize with units based on bulk creation parameters
    const totalFloors = {{ $apartment->floors ?? 1 }};
    const propertyType = '{{ $apartment->property_type }}';
    
    // Get bulk creation parameters from PHP
    const bulkParams = @json($bulkParams ?? []);
    const unitsPerFloor = bulkParams.units_per_floor || 4;
    const createAllBedrooms = bulkParams.create_all_bedrooms || false;
    const defaultUnitType = bulkParams.default_unit_type || 'two_bedroom';
    const defaultRent = bulkParams.default_rent || 15000;
    const defaultBedrooms = bulkParams.default_bedrooms || 2;
    const defaultBathrooms = bulkParams.default_bathrooms || 1;
    
    console.log('Bulk creation parameters:', bulkParams);
    console.log('Units per floor:', unitsPerFloor);
    console.log('Total floors:', totalFloors);
    console.log('Expected total units:', unitsPerFloor * totalFloors);
    
    if (propertyType === 'house' && createAllBedrooms) {
        // For houses, create bedroom units
        const bedrooms = {{ $apartment->bedrooms ?? 1 }};
        for (let i = 1; i <= bedrooms; i++) {
            addUnitToFloor(1, {
                unit_number: `Bedroom ${i}`,
                unit_type: defaultUnitType,
                rent_amount: defaultRent,
                bedrooms: defaultBedrooms,
                bathrooms: defaultBathrooms,
                status: 'available',
                leasing_type: 'separate',
                max_occupants: 4,
                is_furnished: false
            });
        }
    } else {
        // For buildings, create units per floor based on actual parameters
        console.log('Creating units for building...');
        let totalUnitsCreated = 0;
        
        // Ensure all floors exist first
        for (let floor = 1; floor <= totalFloors; floor++) {
            const floorContainer = document.getElementById(`floor-${floor}-units`);
            if (!floorContainer) {
                console.error(`Floor ${floor} container not found!`);
                continue;
            }
        }
        
        // Create all units with a delay to prevent DOM conflicts
        const createUnitsWithDelay = async () => {
            for (let floor = 1; floor <= totalFloors; floor++) {
                console.log(`Creating units for floor ${floor}...`);
                const floorContainer = document.getElementById(`floor-${floor}-units`);
                
                if (!floorContainer) {
                    console.error(`Floor ${floor} container not found! Skipping...`);
                    continue;
                }
                
                for (let unit = 1; unit <= unitsPerFloor; unit++) {
                    const unitNumber = floor + String(unit).padStart(2, '0');
                    console.log(`Creating unit ${unitNumber} for floor ${floor} (unit ${unit}/${unitsPerFloor})`);
                    
                    try {
                        const success = addUnitToFloor(floor, {
                            unit_number: unitNumber,
                            unit_type: defaultUnitType,
                            rent_amount: defaultRent,
                            bedrooms: defaultBedrooms,
                            bathrooms: defaultBathrooms,
                            status: 'available',
                            leasing_type: 'separate',
                            max_occupants: 4,
                            is_furnished: false
                        });
                        
                        if (success) {
                            totalUnitsCreated++;
                            console.log(`✓ Unit ${unitNumber} created successfully`);
                        } else {
                            console.error(`✗ Failed to create unit ${unitNumber} for floor ${floor}`);
                        }
                        
                        // Small delay to prevent DOM conflicts
                        await new Promise(resolve => setTimeout(resolve, 10));
                        
                    } catch (error) {
                        console.error(`Error creating unit ${unitNumber} for floor ${floor}:`, error);
                    }
                }
                
                // Delay between floors to ensure DOM is ready
                await new Promise(resolve => setTimeout(resolve, 50));
            }
        };
        
        await createUnitsWithDelay();
        
        console.log(`Total units created in JavaScript: ${totalUnitsCreated}`);
        console.log(`Expected units: ${unitsPerFloor * totalFloors}`);
        
        if (totalUnitsCreated !== (unitsPerFloor * totalFloors)) {
            console.error(`MISMATCH: Expected ${unitsPerFloor * totalFloors} units, but created ${totalUnitsCreated}`);
        }
    }
    
    updateStats();
}

function addUnitToFloor(floor, unitData = null) {
    const floorContainer = document.getElementById(`floor-${floor}-units`);
    
    if (!floorContainer) {
        console.error(`Floor container for floor ${floor} not found!`);
        return false;
    }
    
    const unitId = `unit-${floor}-${Date.now()}`;
    
    console.log(`Adding unit to floor ${floor}, container:`, floorContainer);
    
    const defaultData = unitData || {
        unit_number: `${floor}01`,
        unit_type: 'two_bedroom',
        rent_amount: 15000,
        bedrooms: 2,
        bathrooms: 1,
        status: 'available',
        leasing_type: 'separate',
        max_occupants: 4,
        is_furnished: false
    };
    
    console.log(`Creating unit with data:`, defaultData);
    
    const unitRow = document.createElement('tr');
    unitRow.className = 'unit-row';
    unitRow.id = unitId;
    unitRow.innerHTML = `
        <td>
            <input type="text" name="units[${unitId}][unit_number]" class="form-control form-control-sm" value="${defaultData.unit_number}" required>
        </td>
        <td>
            <select name="units[${unitId}][unit_type]" class="form-control form-control-sm" required>
                <option value="studio" ${defaultData.unit_type === 'studio' ? 'selected' : ''}>Studio</option>
                <option value="one_bedroom" ${defaultData.unit_type === 'one_bedroom' ? 'selected' : ''}>One Bedroom</option>
                <option value="two_bedroom" ${defaultData.unit_type === 'two_bedroom' ? 'selected' : ''}>Two Bedroom</option>
                <option value="three_bedroom" ${defaultData.unit_type === 'three_bedroom' ? 'selected' : ''}>Three Bedroom</option>
                <option value="penthouse" ${defaultData.unit_type === 'penthouse' ? 'selected' : ''}>Penthouse</option>
            </select>
        </td>
        <td class="text-center">
            <div class="d-flex justify-content-center align-items-center gap-2">
                <input type="number" name="units[${unitId}][bedrooms]" class="form-control form-control-sm" value="${defaultData.bedrooms}" min="0" max="10" required style="width: 60px;">
                <span class="text-muted">/</span>
                <input type="number" name="units[${unitId}][bathrooms]" class="form-control form-control-sm" value="${defaultData.bathrooms}" min="1" max="10" required style="width: 60px;">
            </div>
        </td>
        <td class="text-end">
            <input type="number" name="units[${unitId}][rent_amount]" class="form-control form-control-sm" value="${defaultData.rent_amount}" min="0" step="100" required>
        </td>
        <td class="text-center">
            <select name="units[${unitId}][status]" class="form-control form-control-sm" required>
                <option value="available" ${defaultData.status === 'available' ? 'selected' : ''}>Available</option>
                <option value="maintenance" ${defaultData.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
            </select>
        </td>
        <td class="text-center">
            <input type="number" name="units[${unitId}][max_occupants]" class="form-control form-control-sm" value="${defaultData.max_occupants}" min="1" max="20" required>
        </td>
        <td class="text-center">
            <select name="units[${unitId}][leasing_type]" class="form-control form-control-sm" required>
                <option value="separate" ${defaultData.leasing_type === 'separate' ? 'selected' : ''}>Separate</option>
                <option value="inclusive" ${defaultData.leasing_type === 'inclusive' ? 'selected' : ''}>Inclusive</option>
            </select>
        </td>
        <td class="text-center">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editUnit('${unitId}')" title="Edit Unit">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="duplicateUnit('${unitId}')" title="Duplicate Unit">
                    <i class="fas fa-copy"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeUnit('${unitId}')" title="Remove Unit">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    `;
    
    // Add hidden inputs
    const hiddenInputs = document.createElement('div');
    hiddenInputs.innerHTML = `
        <input type="hidden" name="units[${unitId}][floor_number]" value="${floor}">
        <input type="hidden" name="units[${unitId}][is_furnished]" value="${defaultData.is_furnished ? 1 : 0}">
    `;
    unitRow.appendChild(hiddenInputs);
    
    floorContainer.appendChild(unitRow);
    console.log(`Unit ${defaultData.unit_number} added to floor ${floor}. Total units in floor:`, floorContainer.children.length);
    updateStats();
    return true;
}

function editUnit(unitId) {
    const unitRow = document.getElementById(unitId);
    unitRow.classList.toggle('table-warning');
}

function duplicateUnit(unitId) {
    const unitRow = document.getElementById(unitId);
    const floor = unitRow.closest('.floor-section').dataset.floor;
    
    // Get current unit data
    const formData = new FormData();
    const inputs = unitRow.querySelectorAll('input, select');
    inputs.forEach(input => {
        formData.append(input.name, input.value);
    });
    
    // Create new unit with incremented number
    const currentNumber = unitRow.querySelector('input[name*="unit_number"]').value;
    const newNumber = incrementUnitNumber(currentNumber);
    
    addUnitToFloor(parseInt(floor), {
        unit_number: newNumber,
        unit_type: formData.get(`units[${unitId}][unit_type]`),
        rent_amount: formData.get(`units[${unitId}][rent_amount]`),
        bedrooms: formData.get(`units[${unitId}][bedrooms]`),
        bathrooms: formData.get(`units[${unitId}][bathrooms]`),
        status: formData.get(`units[${unitId}][status]`),
        leasing_type: formData.get(`units[${unitId}][leasing_type]`),
        max_occupants: formData.get(`units[${unitId}][max_occupants]`)
    });
}

function removeUnit(unitId) {
    if (confirm('Are you sure you want to remove this unit?')) {
        document.getElementById(unitId).remove();
        updateStats();
    }
}

function addNewFloor() {
    const floorsContainer = document.getElementById('floorsContainer');
    const currentFloors = floorsContainer.children.length;
    const newFloor = currentFloors + 1;
    
    const floorSection = document.createElement('div');
    floorSection.className = 'floor-section';
    floorSection.dataset.floor = newFloor;
    floorSection.innerHTML = `
        <div class="floor-header">
            <h3 class="floor-title">Floor ${newFloor}</h3>
            <div class="floor-controls">
                <button type="button" class="btn btn-sm btn-outline" onclick="addUnitToFloor(${newFloor})">
                    <i class="fas fa-plus"></i> Add Unit
                </button>
                <button type="button" class="btn btn-sm btn-outline" onclick="removeFloor(${newFloor})" style="color: #ef4444;">
                    <i class="fas fa-trash"></i> Remove Floor
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="floor-${newFloor}-units-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 12%;">Unit Number</th>
                        <th style="width: 15%;">Unit Type</th>
                        <th style="width: 12%;" class="text-center">Beds / Baths</th>
                        <th style="width: 12%;" class="text-end">Rent (₱)</th>
                        <th style="width: 10%;" class="text-center">Status</th>
                        <th style="width: 10%;" class="text-center">Max Occupants</th>
                        <th style="width: 12%;" class="text-center">Leasing Type</th>
                        <th style="width: 17%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="floor-${newFloor}-units">
                    <!-- Units will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    `;
    
    floorsContainer.appendChild(floorSection);
    updateStats();
}

function removeFloor(floor) {
    if (confirm(`Are you sure you want to remove Floor ${floor} and all its units?`)) {
        const floorSection = document.querySelector(`[data-floor="${floor}"]`);
        if (floorSection) {
            floorSection.remove();
            updateStats();
        }
    }
}

function incrementUnitNumber(currentNumber) {
    // Simple increment logic - can be enhanced
    const match = currentNumber.match(/^(\d+)(\d{2})$/);
    if (match) {
        const floor = match[1];
        const unit = parseInt(match[2]) + 1;
        return floor + String(unit).padStart(2, '0');
    }
    return currentNumber + '1';
}

function updateStats() {
    const totalUnits = document.querySelectorAll('.unit-row').length;
    const totalFloors = document.querySelectorAll('.floor-section').length;
    const avgUnitsPerFloor = totalFloors > 0 ? Math.round(totalUnits / totalFloors) : 0;
    
    document.getElementById('totalUnits').textContent = totalUnits;
    document.getElementById('totalFloors').textContent = totalFloors;
    document.getElementById('avgUnitsPerFloor').textContent = avgUnitsPerFloor;
}

function applyToAllFloors() {
    // Implementation for applying settings to all floors
    alert('Apply to all floors functionality will be implemented');
}

function duplicateFloor() {
    // Implementation for duplicating floor settings
    alert('Duplicate floor functionality will be implemented');
}

function debugFormSubmission() {
    // Count all unit rows
    const allUnitRows = document.querySelectorAll('.unit-row');
    console.log('Total unit rows found:', allUnitRows.length);
    
    // Count units per floor
    const floors = document.querySelectorAll('.floor-section');
    floors.forEach((floor, index) => {
        const floorNumber = floor.dataset.floor;
        const unitsInFloor = floor.querySelectorAll('.unit-row');
        console.log(`Floor ${floorNumber}: ${unitsInFloor.length} units`);
    });
    
    // Count form inputs
    const unitInputs = document.querySelectorAll('input[name*="[unit_number]"]');
    console.log('Unit number inputs found:', unitInputs.length);
    
    // Show alert with counts
    alert(`Debug Info:\nTotal unit rows: ${allUnitRows.length}\nUnit inputs: ${unitInputs.length}\nCheck console for detailed breakdown.`);
}
</script>
@endsection
