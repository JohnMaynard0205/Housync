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
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control @error('card_uid') is-invalid @enderror" 
                                       id="card_uid" 
                                       name="card_uid" 
                                       value="{{ old('card_uid') }}"
                                       placeholder="Click 'Get Card UID' to generate UID"
                                       style="font-family: monospace;"
                                       readonly
                                       required>
                                <button type="button" 
                                        class="btn btn-primary" 
                                        id="simple-scan-btn"
                                        title="Get Card UID directly">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Get Card UID</span>
                                </button>
                            </div>
                            <div class="form-text">
                                Click "Get Card UID" to get the latest card UID from ESP32Reader.php. Tap a new RFID card on the reader first.
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
                                <strong>Scan RFID Card:</strong> Click the "Scan RFID Card" button to open the scanner modal.
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <strong>Start Scanning:</strong> Click "Start Scanning" in the modal and tap your RFID card on the scanner.
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <strong>Use UID:</strong> Once detected, click "Use This UID" to automatically fill the form field.
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

<!-- Direct scan status display -->
<div id="scan-status-container" class="mt-3" style="display: none;">
    <div class="alert alert-info" id="scan-status">
        <i class="fas fa-spinner fa-spin me-2"></i>
        <span id="scan-status-text">Preparing to scan...</span>
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
    
    /* RFID Scan Modal Styles */
    .scan-icon-container {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #007bff, #0056b3);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }
    
    .scan-icon {
        font-size: 2rem;
        color: white;
        transition: transform 0.3s ease;
    }
    
    .scan-icon.scanning {
        animation: pulse-scan 1.5s infinite;
    }
    
    @keyframes pulse-scan {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .card-uid-display {
        font-family: 'Courier New', monospace;
        font-size: 1.2rem;
        font-weight: bold;
        color: #155724;
        background: rgba(212, 237, 218, 0.5);
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        display: inline-block;
        margin-top: 0.5rem;
    }
    
    #scanCardModal .modal-content {
        border-radius: 1rem;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }
    
    #scanCardModal .modal-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 1rem 1rem 0 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .progress {
        height: 8px;
        background-color: #e9ecef;
        border-radius: 0.5rem;
    }
    
    .progress-bar {
        background: linear-gradient(90deg, #007bff, #0056b3);
        border-radius: 0.5rem;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apartmentSelect = document.getElementById('apartment_id');
    const tenantSelect = document.getElementById('tenant_assignment_id');
    const tenantOptions = Array.from(tenantSelect.options);
    
    // Tenant filtering functionality
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
    
    // Simple Card UID Scanner - Direct ESP32 Connection
    const cardUidInput = document.getElementById('card_uid');
    const simpleScanBtn = document.getElementById('simple-scan-btn');
    const scanStatusContainer = document.getElementById('scan-status-container');
    const scanStatus = document.getElementById('scan-status');
    
    let isScanning = false;
    
    // Simple scan button click handler
    simpleScanBtn.addEventListener('click', function() {
        if (isScanning) return; // Prevent multiple scans
        getCardUIDSimple();
    });
    
    function getCardUIDSimple() {
        isScanning = true;
        
        // Update button state
        simpleScanBtn.disabled = true;
        simpleScanBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting UID...';
        
        // Show status
        showScanStatus('info', 'Getting latest Card UID from ESP32Reader.php...');
        
        // First try to get the latest real Card UID from ESP32Reader.php
        fetch('/api/rfid/latest-uid', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.card_uid) {
                // Success! Real Card UID received from ESP32Reader.php
                cardUidInput.value = data.card_uid;
                cardUidInput.classList.add('border-success');
                
                let message = `Card UID from ESP32: ${data.card_uid}`;
                if (data.age_seconds !== undefined) {
                    message += ` (${data.age_seconds}s ago)`;
                }
                showScanStatus('success', message);
                
                setTimeout(() => {
                    hideScanStatus();
                    cardUidInput.classList.remove('border-success');
                }, 5000);
                
            } else {
                // If no recent card, try fallback generator or show instructions
                if (data.error && data.error.includes('No card has been scanned')) {
                    showScanStatus('warning', 'No recent card found. Please tap an RFID card on the ESP32 reader first.');
                    // Try fallback after a moment
                    setTimeout(() => {
                        tryFallbackGenerator();
                    }, 2000);
                } else if (data.error && data.error.includes('too old')) {
                    showScanStatus('warning', 'Last card is too old. Please tap a new RFID card on the ESP32 reader.');
                    setTimeout(() => {
                        tryFallbackGenerator();
                    }, 2000);
                } else {
                    tryFallbackGenerator();
                }
            }
            
            resetScanState();
        })
        .catch(error => {
            console.error('ESP32Reader error:', error);
            // If ESP32Reader.php is not available, try fallback
            showScanStatus('warning', 'ESP32Reader.php not responding. Trying fallback...');
            setTimeout(() => {
                tryFallbackGenerator();
            }, 1000);
            resetScanState();
        });
    }
    
    function tryFallbackGenerator() {
        showScanStatus('info', 'Using test Card UID generator...');
        
        fetch('/api/rfid/generate-uid', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.card_uid) {
                cardUidInput.value = data.card_uid;
                cardUidInput.classList.add('border-success');
                
                let message = `Test Card UID: ${data.card_uid}`;
                if (data.test_mode) {
                    message += ' (Generated for testing)';
                }
                showScanStatus('info', message);
                
                setTimeout(() => {
                    hideScanStatus();
                    cardUidInput.classList.remove('border-success');
                }, 4000);
            } else {
                showManualUIDInput();
            }
        })
        .catch(error => {
            console.error('Fallback generator error:', error);
            showManualUIDInput();
        });
    }
    
    function generateTestUID() {
        // Generate a random 8-character hex UID for testing
        const chars = '0123456789ABCDEF';
        let uid = '';
        for (let i = 0; i < 8; i++) {
            uid += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return uid;
    }
    
    function showManualUIDInput() {
        // If automatic scanning fails, allow manual input
        cardUidInput.readOnly = false;
        cardUidInput.placeholder = 'Enter Card UID manually (e.g., A1B2C3D4)';
        cardUidInput.focus();
        
        showScanStatus('warning', 'Automatic scan failed. Please enter Card UID manually or check ESP32 connection.');
        
        // Add input validation
        cardUidInput.addEventListener('input', function() {
            let value = this.value.toUpperCase().replace(/[^0-9A-F]/g, '');
            if (value.length > 8) value = value.substring(0, 8);
            this.value = value;
            
            if (value.length === 8) {
                this.classList.add('border-success');
                showScanStatus('success', `Card UID entered: ${value}`);
            } else {
                this.classList.remove('border-success');
            }
        });
    }
    
    
    function showScanStatus(type, message) {
        scanStatusContainer.style.display = 'block';
        scanStatus.className = `alert alert-${type}`;
        
        let icon = 'fas fa-info-circle';
        if (type === 'success') icon = 'fas fa-check-circle';
        if (type === 'danger') icon = 'fas fa-exclamation-triangle';
        if (type === 'info') icon = 'fas fa-spinner fa-spin';
        
        scanStatus.innerHTML = `<i class="${icon} me-2"></i><span id="scan-status-text">${message}</span>`;
    }
    
    function hideScanStatus() {
        scanStatusContainer.style.display = 'none';
    }
    
    function resetScanState() {
        isScanning = false;
        simpleScanBtn.disabled = false;
        simpleScanBtn.innerHTML = '<i class="fas fa-credit-card"></i> <span>Get Card UID</span>';
        
        setTimeout(() => {
            hideScanStatus();
        }, 5000); // Hide status after 5 seconds
    }
});
</script>
@endsection
