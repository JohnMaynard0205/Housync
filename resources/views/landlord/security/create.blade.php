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
                                       placeholder="Click 'Scan RFID Card' to get UID"
                                       style="font-family: monospace;"
                                       readonly
                                       required>
                                <button type="button" 
                                        class="btn btn-primary" 
                                        id="open-scan-modal-btn"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#scanCardModal"
                                        title="Scan RFID card to get UID">
                                    <i class="fas fa-qrcode"></i>
                                    <span>Scan RFID Card</span>
                                </button>
                            </div>
                            <div class="form-text">
                                Click "Scan RFID Card" to open the scanner and automatically detect your card's UID.
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

<!-- RFID Card Scanning Modal -->
<div class="modal fade" id="scanCardModal" tabindex="-1" aria-labelledby="scanCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanCardModalLabel">
                    <i class="fas fa-qrcode me-2"></i>Scan RFID Card
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="scan-animation" class="mb-4">
                    <div class="scan-icon-container">
                        <i class="fas fa-wifi scan-icon" id="modal-scan-icon"></i>
                    </div>
                </div>
                
                <h6 id="scan-instruction" class="mb-3">Click "Start Scanning" to begin</h6>
                
                <div id="scan-result" class="alert alert-success" style="display: none;">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Card Detected!</strong><br>
                    <span class="card-uid-display" id="detected-uid"></span>
                </div>
                
                <div id="scan-error" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="error-message"></span>
                </div>
                
                <div id="scan-progress" class="progress mb-3" style="display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: 0%" 
                         id="scan-progress-bar">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="start-scan-btn">
                    <i class="fas fa-play me-2"></i>Start Scanning
                </button>
                <button type="button" class="btn btn-success" id="use-scanned-uid-btn" style="display: none;">
                    <i class="fas fa-check me-2"></i>Use This UID
                </button>
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
    
    // RFID Card Scanning Modal functionality
    const cardUidInput = document.getElementById('card_uid');
    const startScanBtn = document.getElementById('start-scan-btn');
    const useScannedUidBtn = document.getElementById('use-scanned-uid-btn');
    const modalScanIcon = document.getElementById('modal-scan-icon');
    const scanInstruction = document.getElementById('scan-instruction');
    const scanResult = document.getElementById('scan-result');
    const scanError = document.getElementById('scan-error');
    const scanProgress = document.getElementById('scan-progress');
    const scanProgressBar = document.getElementById('scan-progress-bar');
    const detectedUid = document.getElementById('detected-uid');
    const errorMessage = document.getElementById('error-message');
    
    let scanInterval = null;
    let currentScannedUid = null;
    
    // Start scan button click handler
    startScanBtn.addEventListener('click', function() {
        startModalScan();
    });
    
    // Use scanned UID button click handler
    useScannedUidBtn.addEventListener('click', function() {
        if (currentScannedUid) {
            cardUidInput.value = currentScannedUid;
            cardUidInput.classList.add('border-success');
            setTimeout(() => {
                cardUidInput.classList.remove('border-success');
            }, 3000);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('scanCardModal'));
            modal.hide();
        }
    });
    
    // Reset modal when it's closed
    document.getElementById('scanCardModal').addEventListener('hidden.bs.modal', function() {
        resetModal();
    });
    
    function startModalScan() {
        // Reset modal state
        resetModal();
        
        // Update UI to scanning state
        startScanBtn.style.display = 'none';
        modalScanIcon.className = 'fas fa-spinner fa-spin scan-icon scanning';
        scanInstruction.textContent = 'Initiating scan request...';
        scanProgress.style.display = 'block';
        
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
                pollModalScanStatus(data.scan_id, data.timeout);
            } else {
                showModalError('Failed to start scan: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Scan initiation error:', error);
            showModalError('Network error: Unable to start scan');
        });
    }
    
    function pollModalScanStatus(scanId, timeout) {
        modalScanIcon.className = 'fas fa-wifi scan-icon scanning';
        scanInstruction.textContent = 'Please tap your RFID card on the scanner now...';
        
        let pollCount = 0;
        const maxPolls = Math.ceil(timeout / 0.5); // Poll every 500ms
        
        scanInterval = setInterval(() => {
            pollCount++;
            
            // Update progress bar
            const progress = (pollCount / maxPolls) * 100;
            scanProgressBar.style.width = progress + '%';
            
            fetch(`/api/rfid/scan/${scanId}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.status === 'completed' && data.card_uid) {
                        // Success! Card was scanned
                        showModalSuccess(data.card_uid);
                        clearModalScanInterval();
                        
                    } else if (data.status === 'timeout') {
                        showModalError('Scan timed out. Please try again.');
                        clearModalScanInterval();
                        
                    } else if (data.status === 'error') {
                        showModalError('Scan error: ' + (data.error || 'Unknown error'));
                        clearModalScanInterval();
                        
                    } else if (data.status === 'waiting') {
                        // Still waiting, update remaining time
                        const remaining = Math.ceil(data.remaining_time || 0);
                        scanInstruction.textContent = `Please tap your RFID card now... (${remaining}s remaining)`;
                    }
                } else {
                    showModalError('Status check failed: ' + (data.error || 'Unknown error'));
                    clearModalScanInterval();
                }
            })
            .catch(error => {
                console.error('Status poll error:', error);
                if (pollCount >= maxPolls) {
                    showModalError('Scan timeout - no response from scanner');
                    clearModalScanInterval();
                }
            });
            
            // Stop polling after max attempts
            if (pollCount >= maxPolls) {
                showModalError('Scan timeout - please try again');
                clearModalScanInterval();
            }
        }, 500); // Poll every 500ms
    }
    
    function showModalSuccess(cardUid) {
        currentScannedUid = cardUid;
        
        // Update UI
        modalScanIcon.className = 'fas fa-check-circle scan-icon';
        scanInstruction.textContent = 'Card successfully detected!';
        scanProgress.style.display = 'none';
        scanResult.style.display = 'block';
        detectedUid.textContent = cardUid;
        useScannedUidBtn.style.display = 'inline-block';
    }
    
    function showModalError(message) {
        // Update UI
        modalScanIcon.className = 'fas fa-exclamation-triangle scan-icon';
        scanInstruction.textContent = 'Scan failed';
        scanProgress.style.display = 'none';
        scanError.style.display = 'block';
        errorMessage.textContent = message;
        startScanBtn.style.display = 'inline-block';
        startScanBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Try Again';
    }
    
    function resetModal() {
        // Clear intervals
        clearModalScanInterval();
        
        // Reset UI elements
        modalScanIcon.className = 'fas fa-wifi scan-icon';
        scanInstruction.textContent = 'Click "Start Scanning" to begin';
        scanResult.style.display = 'none';
        scanError.style.display = 'none';
        scanProgress.style.display = 'none';
        scanProgressBar.style.width = '0%';
        startScanBtn.style.display = 'inline-block';
        startScanBtn.innerHTML = '<i class="fas fa-play me-2"></i>Start Scanning';
        useScannedUidBtn.style.display = 'none';
        
        // Clear variables
        currentScannedUid = null;
    }
    
    function clearModalScanInterval() {
        if (scanInterval) {
            clearInterval(scanInterval);
            scanInterval = null;
        }
    }
    
    // Clean up intervals if user navigates away
    window.addEventListener('beforeunload', clearModalScanInterval);
});
</script>
@endsection
