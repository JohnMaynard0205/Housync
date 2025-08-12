@extends('layouts.landlord-app')

@section('title', 'Assign RFID Card')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Assign RFID Card</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('landlord.security', ['apartment_id' => $apartmentId]) }}">Security</a>
                    </li>
                    <li class="breadcrumb-item active">Assign Card</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('landlord.security', ['apartment_id' => $apartmentId]) }}" 
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Security
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-id-card"></i> Card Assignment Form
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('landlord.security.store-card') }}">
                        @csrf
                        
                        <!-- Card UID -->
                        <div class="mb-3">
                            <label for="card_uid" class="form-label required">Card UID</label>
                            <input type="text" 
                                   class="form-control @error('card_uid') is-invalid @enderror" 
                                   id="card_uid" 
                                   name="card_uid" 
                                   value="{{ old('card_uid') }}"
                                   placeholder="e.g., A1B2C3D4"
                                   style="font-family: monospace;"
                                   required>
                            <div class="form-text">
                                Enter the unique identifier from the RFID card. This is usually printed on the card or can be read by scanning it.
                            </div>
                            @error('card_uid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Apartment Selection -->
                        <div class="mb-3">
                            <label for="apartment_id" class="form-label required">Apartment</label>
                            <select class="form-select @error('apartment_id') is-invalid @enderror" 
                                    id="apartment_id" 
                                    name="apartment_id" 
                                    required>
                                <option value="">Select an apartment</option>
                                @foreach($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" 
                                            {{ (old('apartment_id') ?: $apartmentId) == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('apartment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tenant Assignment -->
                        <div class="mb-3">
                            <label for="tenant_assignment_id" class="form-label required">Assign to Tenant</label>
                            <select class="form-select @error('tenant_assignment_id') is-invalid @enderror" 
                                    id="tenant_assignment_id" 
                                    name="tenant_assignment_id" 
                                    required>
                                <option value="">Select a tenant</option>
                                @foreach($tenantAssignments as $assignment)
                                    <option value="{{ $assignment->id }}" 
                                            data-apartment="{{ $assignment->unit->apartment_id }}"
                                            {{ old('tenant_assignment_id') == $assignment->id ? 'selected' : '' }}>
                                        {{ $assignment->tenant->name }} - Unit {{ $assignment->unit->unit_number }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Only active tenants are shown. Make sure the tenant is assigned to a unit first.
                            </div>
                            @error('tenant_assignment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Card Name (Optional) -->
                        <div class="mb-3">
                            <label for="card_name" class="form-label">Card Name (Optional)</label>
                            <input type="text" 
                                   class="form-control @error('card_name') is-invalid @enderror" 
                                   id="card_name" 
                                   name="card_name" 
                                   value="{{ old('card_name') }}"
                                   placeholder="e.g., Main Access Card, Backup Card">
                            <div class="form-text">
                                Give this card a descriptive name to help identify it later.
                            </div>
                            @error('card_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expiration Date (Optional) -->
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                            <input type="date" 
                                   class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" 
                                   name="expires_at" 
                                   value="{{ old('expires_at') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <div class="form-text">
                                Leave blank if the card should never expire. Card will automatically be deactivated after this date.
                            </div>
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Add any additional notes about this card assignment...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('landlord.security', ['apartment_id' => $apartmentId]) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Assign Card
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-question-circle"></i> How to Get Card UID
                    </h6>
                </div>
                <div class="card-body">
                    <div class="step-list">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <strong>Physical Card:</strong> Check if the UID is printed on the RFID card itself.
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <strong>Scan with ESP32:</strong> Use your ESP32 setup to scan the card and read the UID from the serial output.
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <strong>Check Access Logs:</strong> If the card has been scanned before, check the access logs for unregistered card attempts.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-shield-alt"></i> Security Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Set expiration dates for temporary access
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Use descriptive names for easy identification
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Regularly review and audit card assignments
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Deactivate cards immediately when tenants move out
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .required::after {
        content: " *";
        color: #dc3545;
    }
    
    .step-list {
        list-style: none;
        padding: 0;
    }
    
    .step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .step:last-child {
        margin-bottom: 0;
    }
    
    .step-number {
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .step-content {
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apartmentSelect = document.getElementById('apartment_id');
    const tenantSelect = document.getElementById('tenant_assignment_id');
    const tenantOptions = Array.from(tenantSelect.options);
    
    function filterTenants() {
        const selectedApartment = apartmentSelect.value;
        
        // Clear current options except the first one
        tenantSelect.innerHTML = '<option value="">Select a tenant</option>';
        
        // Add back filtered options
        tenantOptions.forEach(option => {
            if (option.value && (!selectedApartment || option.dataset.apartment === selectedApartment)) {
                tenantSelect.appendChild(option.cloneNode(true));
            }
        });
    }
    
    apartmentSelect.addEventListener('change', filterTenants);
    
    // Initial filter
    filterTenants();
});
</script>
@endsection
