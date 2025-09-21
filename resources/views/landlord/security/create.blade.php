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
                                       placeholder="e.g., A1B2C3D4"
                                       style="font-family: monospace;"
                                       required>
                                <button type="button" 
                                        class="btn btn-outline-primary" 
                                        id="scan-card-btn"
                                        title="Scan RFID card to get UID automatically">
                                    <i class="fas fa-wifi" id="scan-icon"></i>
                                    <span id="scan-text">Scan Card</span>
                                </button>
                            </div>
                            <div class="form-text">
                                Enter the unique identifier from the RFID card manually, or click "Scan Card" to read it automatically.
                            </div>
                            <div id="scan-status" class="mt-2" style="display: none;"></div>
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
                                <strong>Scan Button:</strong> Click the "Scan Card" button above and tap your RFID card on the scanner to automatically get the UID.
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <strong>Physical Card:</strong> Check if the UID is printed on the RFID card itself.
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
    
    // RFID Card Scanning functionality
    const scanBtn = document.getElementById('scan-card-btn');
    const scanIcon = document.getElementById('scan-icon');
    const scanText = document.getElementById('scan-text');
    const scanStatus = document.getElementById('scan-status');
    const cardUidInput = document.getElementById('card_uid');
    
    let scanInterval = null;
    let scanTimeout = null;
    
    scanBtn.addEventListener('click', function() {
        startCardScan();
    });
    
    function startCardScan() {
        // Disable the button and show loading state
        scanBtn.disabled = true;
        scanIcon.className = 'fas fa-spinner fa-spin';
        scanText.textContent = 'Initiating...';
        
        // Show status
        showScanStatus('info', 'Initiating scan request...', true);
        
        // Make API request to start scanning
        fetch('/api/rfid/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                timeout: 15 // 15 seconds timeout
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pollScanStatus(data.scan_id, data.timeout);
            } else {
                showScanStatus('danger', 'Failed to start scan: ' + (data.error || 'Unknown error'));
                resetScanButton();
            }
        })
        .catch(error => {
            console.error('Scan initiation error:', error);
            showScanStatus('danger', 'Network error: Unable to start scan');
            resetScanButton();
        });
    }
    
    function pollScanStatus(scanId, timeout) {
        scanText.textContent = 'Tap Card Now';
        showScanStatus('warning', 'Please tap your RFID card on the scanner now...', true);
        
        let pollCount = 0;
        const maxPolls = Math.ceil(timeout / 0.5); // Poll every 500ms
        
        scanInterval = setInterval(() => {
            pollCount++;
            
            fetch(`/api/rfid/scan/${scanId}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'completed' && data.card_uid) {
                        // Success! Card was scanned
                        cardUidInput.value = data.card_uid;
                        showScanStatus('success', `Card scanned successfully! UID: ${data.card_uid}`);
                        clearScanInterval();
                        resetScanButton();
                        
                        // Add a subtle highlight to the input
                        cardUidInput.classList.add('border-success');
                        setTimeout(() => {
                            cardUidInput.classList.remove('border-success');
                        }, 3000);
                        
                    } else if (data.status === 'timeout') {
                        showScanStatus('warning', 'Scan timed out. Please try again.');
                        clearScanInterval();
                        resetScanButton();
                        
                    } else if (data.status === 'error') {
                        showScanStatus('danger', 'Scan error: ' + (data.error || 'Unknown error'));
                        clearScanInterval();
                        resetScanButton();
                        
                    } else if (data.status === 'waiting') {
                        // Still waiting, update remaining time
                        const remaining = Math.ceil(data.remaining_time || 0);
                        showScanStatus('warning', `Waiting for card tap... (${remaining}s remaining)`, true);
                    }
                } else {
                    showScanStatus('danger', 'Status check failed: ' + (data.error || 'Unknown error'));
                    clearScanInterval();
                    resetScanButton();
                }
            })
            .catch(error => {
                console.error('Status poll error:', error);
                if (pollCount >= maxPolls) {
                    showScanStatus('danger', 'Scan timeout - no response from scanner');
                    clearScanInterval();
                    resetScanButton();
                }
            });
            
            // Stop polling after max attempts
            if (pollCount >= maxPolls) {
                showScanStatus('warning', 'Scan timeout - please try again');
                clearScanInterval();
                resetScanButton();
            }
        }, 500); // Poll every 500ms
    }
    
    function showScanStatus(type, message, spinner = false) {
        const alertClass = `alert alert-${type}`;
        const spinnerHtml = spinner ? '<i class="fas fa-spinner fa-spin me-2"></i>' : '';
        
        scanStatus.innerHTML = `
            <div class="${alertClass} alert-dismissible fade show mb-0" role="alert">
                ${spinnerHtml}${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        scanStatus.style.display = 'block';
        
        // Auto-hide success and info messages after 5 seconds
        if (type === 'success' || type === 'info') {
            setTimeout(() => {
                const alert = scanStatus.querySelector('.alert');
                if (alert && alert.classList.contains(`alert-${type}`)) {
                    scanStatus.style.display = 'none';
                }
            }, 5000);
        }
    }
    
    function clearScanInterval() {
        if (scanInterval) {
            clearInterval(scanInterval);
            scanInterval = null;
        }
        if (scanTimeout) {
            clearTimeout(scanTimeout);
            scanTimeout = null;
        }
    }
    
    function resetScanButton() {
        scanBtn.disabled = false;
        scanIcon.className = 'fas fa-wifi';
        scanText.textContent = 'Scan Card';
    }
    
    // Clean up intervals if user navigates away
    window.addEventListener('beforeunload', clearScanInterval);
});
</script>
@endsection
