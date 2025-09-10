@extends('layouts.landlord-app')

@section('title', 'Tenant Assignments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tenant Assignments</li>
                    </ol>
                </div>
                <h4 class="page-title">Tenant Assignments</h4>
            </div>
        </div>
    </div>

    <!-- Success Alert (without credentials) -->
    @if(session('credentials'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle me-2" style="font-size: 1.5rem; color: #28a745;"></i>
                <h5 class="alert-heading mb-0">✅ Tenant Assigned Successfully!</h5>
            </div>
            <p class="mt-2 mb-0">New tenant credentials have been generated and are ready for sharing.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Total Assignments">Total Assignments</h5>
                            <h3 class="mt-3 mb-3">{{ $stats['total_assignments'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary rounded">
                                <i class="mdi mdi-account-multiple font-20 text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Active Assignments">Active Assignments</h5>
                            <h3 class="mt-3 mb-3">{{ $stats['active_assignments'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success rounded">
                                <i class="mdi mdi-check-circle font-20 text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Pending Documents">Pending Documents</h5>
                            <h3 class="mt-3 mb-3">{{ $stats['pending_documents'] }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning rounded">
                                <i class="mdi mdi-file-document font-20 text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Total Revenue">Total Revenue</h5>
                            <h3 class="mt-3 mb-3">₱{{ number_format($stats['total_revenue'], 2) }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info rounded">
                                <i class="mdi mdi-currency-php font-20 text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('landlord.tenant-assignments') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="documents_uploaded" class="form-label">Documents Uploaded</label>
                            <select name="documents_uploaded" id="documents_uploaded" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('documents_uploaded') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ request('documents_uploaded') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="documents_verified" class="form-label">Documents Verified</label>
                            <select name="documents_verified" id="documents_verified" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ request('documents_verified') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ request('documents_verified') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2 align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('landlord.tenant-assignments') }}" class="btn btn-secondary">Clear</a>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#assignTenantModal">
                                <i class="mdi mdi-account-plus me-1"></i> Assign Tenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Tenant Modal -->
    <div class="modal fade" id="assignTenantModal" tabindex="-1" aria-labelledby="assignTenantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignTenantModalLabel">Assign Tenant to Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="#" id="assignTenantForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label">Select Unit <span class="text-danger">*</span></label>
                                <select class="form-select" id="unit_id" name="unit_id" required>
                                    <option value="">-- Select an Available Unit --</option>
                                    @foreach(\App\Models\Unit::available()->whereHas('apartment', function($q){ $q->where('landlord_id', auth()->id()); })->get() as $unit)
                                        <option value="{{ $unit->id }}">
                                            {{ $unit->unit_number }} ({{ $unit->apartment->name ?? 'N/A' }}) - ₱{{ number_format($unit->rent_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Tenant Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lease_start_date" class="form-label">Lease Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="lease_start_date" name="lease_start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lease_end_date" class="form-label">Lease End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="lease_end_date" name="lease_end_date" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rent_amount" class="form-label">Monthly Rent <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="rent_amount" name="rent_amount" required>
                            </div>
                            <div class="col-md-6">
                                <label for="security_deposit" class="form-label">Security Deposit</label>
                                <input type="number" step="0.01" class="form-control" id="security_deposit" name="security_deposit">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                        <div class="alert alert-info mt-3">
                            <h6 class="alert-heading">Workflow</h6>
                            <ul class="mb-0">
                                <li>A new tenant account will be automatically created</li>
                                <li>Login credentials will be generated and shown after assignment</li>
                                <li>The tenant will receive access to their dashboard</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="assignTenantSubmit" disabled>
                            <i class="mdi mdi-account-plus me-1"></i> Assign Tenant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Unit</th>
                                    <th>Apartment</th>
                                    <th>Lease Period</th>
                                    <th>Rent</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <span class="avatar-title bg-soft-primary rounded-circle">
                                                    {{ substr($assignment->tenant->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="font-14 mb-0">{{ $assignment->tenant->name }}</h5>
                                                <small class="text-muted">{{ $assignment->tenant->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $assignment->unit->unit_number }}</span>
                                    </td>
                                    <td>{{ $assignment->unit->apartment->name }}</td>
                                    <td>
                                        <div>
                                            <small class="text-muted">Start: {{ $assignment->lease_start_date->format('M d, Y') }}</small><br>
                                            <small class="text-muted">End: {{ $assignment->lease_end_date->format('M d, Y') }}</small>
                                        </div>
                                    </td>
                                    <td>₱{{ number_format($assignment->rent_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status_badge_class }}">
                                            {{ $assignment->status === 'terminated' ? 'Terminated' : ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->documents_status_badge_class }}">
                                            {{ ucfirst($assignment->documents_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" title="View Details">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('landlord.assignment-details', $assignment->id) }}">
                                                    <i class="mdi mdi-eye me-1"></i> View Details
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="viewCredentials({{ $assignment->id }}, '{{ $assignment->tenant->email }}')" title="View Login Credentials">
                                                    <i class="mdi mdi-key me-1"></i> View Credentials
                                                </a></li>
                                                @if($assignment->status === 'pending')
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $assignment->id }}, 'active')" title="Activate Assignment">
                                                    <i class="mdi mdi-check me-1"></i> Activate
                                                </a></li>
                                                @endif
                                                @if($assignment->status === 'active')
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus({{ $assignment->id }}, 'terminated')" title="Terminate Assignment">
                                                    <i class="mdi mdi-close me-1"></i> Terminate
                                                </a></li>
                                                @endif
                                                @if($assignment->status === 'terminated')
                                                <li><button type="button" class="dropdown-item" onclick="window.reassignExistingTenant({{ $assignment->id }}, '{{ addslashes($assignment->tenant->name) }}', '{{ addslashes($assignment->tenant->phone ?? '') }}');" title="Assign Tenant Again">
                                                    <i class="mdi mdi-account-plus me-1"></i> Assign
                                                </button></li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteTenantAssignment({{ $assignment->id }}, '{{ $assignment->tenant->name }}')" title="Delete Assignment">
                                                    <i class="mdi mdi-delete me-1"></i> Delete Assignment
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No tenant assignments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $assignments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form -->
<form id="statusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="statusInput">
</form>

<!-- Delete Assignment Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Credentials Modal -->
<div class="modal fade" id="credentialsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tenant Login Credentials</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Login Information</h6>
                    <p class="mb-2">Share these credentials with the tenant:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Email:</strong><br>
                            <code id="tenantEmail"></code>
                        </div>
                        <div class="col-md-6">
                            <strong>Password:</strong><br>
                            <code id="tenantPassword"></code>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Important Notes:</h6>
                    <ul class="mb-0">
                        <li>The tenant should change their password after first login</li>
                        <li>These credentials are for initial access only</li>
                        <li>Keep these credentials secure and private</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyCredentials()">
                    <i class="mdi mdi-content-copy me-1"></i> Copy Credentials
                </button>
            </div>
        </div>
    </div>
</div>

<!-- New Tenant Assignment Credentials Modal -->
<div class="modal fade" id="newCredentialsModal" tabindex="-1" aria-labelledby="newCredentialsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="newCredentialsModalLabel">
                    <i class="mdi mdi-check-circle me-2"></i>Tenant Assigned Successfully!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="alert alert-success mb-3">
                        <i class="mdi mdi-account-plus" style="font-size: 3rem; color: #28a745;"></i>
                        <h4 class="mt-2 mb-0">New Tenant Account Created!</h4>
                        <p class="mb-0">Please share these login credentials with the tenant.</p>
                    </div>
                </div>
                
                <!-- Credentials Display -->
                <div class="credentials-box p-4 mb-4" style="background: #f8f9fa; border: 2px solid #28a745; border-radius: 10px;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-primary">
                                <i class="mdi mdi-email me-1"></i>Email Address:
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newTenantEmail" readonly 
                                       style="background: white; font-weight: bold; font-size: 1.1rem; color: #0d6efd;">
                                <button class="btn btn-outline-primary" type="button" onclick="copyText('newTenantEmail')" title="Copy email">
                                    <i class="mdi mdi-content-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-primary">
                                <i class="mdi mdi-key me-1"></i>Password:
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newTenantPassword" readonly 
                                       style="background: white; font-weight: bold; font-size: 1.1rem; color: #dc3545;">
                                <button class="btn btn-outline-primary" type="button" onclick="copyText('newTenantPassword')" title="Copy password">
                                    <i class="mdi mdi-content-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-center mb-4">
                    <button type="button" class="btn btn-primary px-4" onclick="copyAllNewCredentials()" title="Copy both email and password">
                        <i class="mdi mdi-content-copy me-1"></i> Copy All Credentials
                    </button>
                    <button type="button" class="btn btn-outline-success px-4" onclick="printNewCredentials()" title="Print credentials">
                        <i class="mdi mdi-printer me-1"></i> Print Credentials
                    </button>

                </div>
                
                <!-- Important Notice -->
                <div class="alert alert-warning border-0" style="background: linear-gradient(135deg, #fff3cd 0%, #fdf7e3 100%); border-left: 4px solid #ffc107 !important;">
                    <h6 class="alert-heading fw-bold">
                        <i class="mdi mdi-alert-circle me-2"></i>Important Information:
                    </h6>
                    <ul class="mb-0">
                        <li><strong>Save these credentials securely</strong> - They won't be shown again after closing this window</li>
                        <li><strong>Share with tenant immediately</strong> - They need these to access their dashboard</li>
                        <li><strong>Document upload required</strong> - Assignment status will remain "Active" but tenant must upload documents</li>
                        <li><strong>First-time login</strong> - Tenant can change password after logging in</li>
                    </ul>
                </div>
                
                <!-- Next Steps -->
                <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #d1ecf1 0%, #e3f2fd 100%); border-left: 4px solid #17a2b8 !important;">
                    <h6 class="alert-heading fw-bold">
                        <i class="mdi mdi-list-status me-2"></i>Next Steps:
                    </h6>
                    <ol class="mb-0">
                        <li>Share these credentials with the tenant</li>
                        <li>Tenant logs in and uploads required documents</li>
                        <li>Review and verify documents when submitted</li>
                        <li>Assignment becomes fully active once documents are verified</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Close
                </button>
                <button type="button" class="btn btn-success" onclick="copyAllNewCredentials(); alert('Credentials copied! You can now close this window.'); document.querySelector('#newCredentialsModal .btn-close').click();">
                    <i class="mdi mdi-check-all me-1"></i>Copy & Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.isReassigning = false;

// Simple test function to verify JavaScript is working  
window.testAssignButton = function(assignmentId) {
    alert('Assign button test clicked for assignment ' + assignmentId);
    console.log('Test function called for assignment:', assignmentId);
    return false; // Prevent any default action
};

// Simple function to open assign modal for existing tenant reassignment
window.reassignExistingTenant = function(assignmentId, tenantName, tenantPhone) {
    console.log('Reassigning existing tenant:', { assignmentId, tenantName, tenantPhone });
    
    // Set global flag to indicate this is a reassignment
    window.isReassigning = true;
    
    // Get form elements
    const form = document.getElementById('assignTenantForm');
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const unitSelect = document.getElementById('unit_id');
    const submitButton = document.getElementById('assignTenantSubmit');
    const modalTitle = document.getElementById('assignTenantModalLabel');
    
    if (!form || !nameInput || !phoneInput) {
        alert('Error: Form elements not found. Please refresh the page.');
        return;
    }
    
    // Set form action to reassign endpoint
    form.action = `/landlord/tenant-assignments/${assignmentId}/reassign`;
    
    // Reset form first
    form.reset();
    
    // Pre-fill and lock tenant name
    nameInput.value = tenantName || '';
    nameInput.readOnly = true;
    nameInput.style.backgroundColor = '#f8f9fa';
    nameInput.placeholder = 'Existing tenant (locked)';
    
    // Pre-fill and lock tenant phone
    phoneInput.value = tenantPhone || '';
    phoneInput.readOnly = true;
    phoneInput.style.backgroundColor = '#f8f9fa';
    phoneInput.placeholder = tenantPhone ? 'Existing phone (locked)' : 'No phone on file (locked)';
    
    // Reset unit selection
    if (unitSelect) {
        unitSelect.value = '';
    }
    
    // Update modal title
    if (modalTitle) {
        modalTitle.textContent = 'Reassign Existing Tenant to New Unit';
    }
    
    // Update submit button
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="mdi mdi-account-plus me-1"></i> Reassign Tenant';
    }
    
    // Open the modal
    const modal = new bootstrap.Modal(document.getElementById('assignTenantModal'));
    modal.show();
};

// Check if there are validation errors and reopen modal if needed
document.addEventListener('DOMContentLoaded', function() {
    @if ($errors->any())
        // Reopen the modal if there are validation errors
        const modal = new bootstrap.Modal(document.getElementById('assignTenantModal'));
        modal.show();
        
        // Restore form values from old input
        @if (old('unit_id'))
            document.getElementById('unit_id').value = '{{ old('unit_id') }}';
            document.getElementById('unit_id').dispatchEvent(new Event('change'));
        @endif
        @if (old('name'))
            document.getElementById('name').value = '{{ old('name') }}';
        @endif
        @if (old('phone'))
            document.getElementById('phone').value = '{{ old('phone') }}';
        @endif
        @if (old('address'))
            document.getElementById('address').value = '{{ old('address') }}';
        @endif
        @if (old('lease_start_date'))
            document.getElementById('lease_start_date').value = '{{ old('lease_start_date') }}';
        @endif
        @if (old('lease_end_date'))
            document.getElementById('lease_end_date').value = '{{ old('lease_end_date') }}';
        @endif
        @if (old('rent_amount'))
            document.getElementById('rent_amount').value = '{{ old('rent_amount') }}';
        @endif
        @if (old('security_deposit'))
            document.getElementById('security_deposit').value = '{{ old('security_deposit') }}';
        @endif
        @if (old('notes'))
            document.getElementById('notes').value = '{{ old('notes') }}';
        @endif
    @endif
});
function updateStatus(assignmentId, status) {
    if (confirm('Are you sure you want to update this assignment status?')) {
        const form = document.getElementById('statusForm');
        const statusInput = document.getElementById('statusInput');
        
        form.action = `/landlord/tenant-assignments/${assignmentId}/status`;
        statusInput.value = status;
        form.submit();
    }
}

// Simple reassign function - make sure it's in global scope
window.reassignTenant = function(assignmentId, tenantName, tenantPhone) {
    alert('reassignTenant function called successfully!'); // Debug alert
    console.log('reassignTenant called with:', { assignmentId, tenantName, tenantPhone }); // Debug log
    
    // Wait for DOM to be ready if needed
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.reassignTenant(assignmentId, tenantName, tenantPhone);
        });
        return;
    }
    
    // Open assign modal pre-filled for re-assigning an existing vacated tenant
    const form = document.getElementById('assignTenantForm');
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const unitSelect = document.getElementById('unit_id');
    const assignTenantSubmit = document.getElementById('assignTenantSubmit');
    
    console.log('Form elements found:', { 
        form: !!form, 
        nameInput: !!nameInput, 
        phoneInput: !!phoneInput, 
        unitSelect: !!unitSelect, 
        assignTenantSubmit: !!assignTenantSubmit 
    }); // Debug log
    
    // Check if all required elements are present
    if (!form || !nameInput || !phoneInput || !unitSelect || !assignTenantSubmit) {
        console.error('Missing required form elements');
        console.log('Available form elements on page:', {
            allForms: document.querySelectorAll('form').length,
            allInputs: document.querySelectorAll('input').length,
            allSelects: document.querySelectorAll('select').length,
            allButtons: document.querySelectorAll('button').length
        });
        alert('Error: Some form elements are missing. Please refresh the page and try again.');
        return;
    }
    
    // Set reassigning mode first
    window.isReassigning = true;
    
    // Post to a reassign endpoint using the assignment id
    form.action = `/landlord/tenant-assignments/${assignmentId}/reassign`;
    console.log('Reassign form action set to:', form.action); // Debug log
    
    // Clear previous values
    form.reset();
    
    // Populate and lock tenant fields for existing tenant
    if (tenantName) {
        nameInput.value = tenantName;
        nameInput.readOnly = true;
        nameInput.classList.add('bg-light');
        nameInput.setAttribute('placeholder', 'Tenant name (locked for reassignment)');
    }
    if (tenantPhone) {
        phoneInput.value = tenantPhone;
        phoneInput.readOnly = true;
        phoneInput.classList.add('bg-light');
        phoneInput.setAttribute('placeholder', 'Phone number (locked for reassignment)');
    } else {
        // still lock the field to avoid editing; leave empty if unknown
        phoneInput.readOnly = true;
        phoneInput.classList.add('bg-light');
        phoneInput.setAttribute('placeholder', 'Phone number not available (locked)');
    }
    
    // Reset unit selection and enable submit button once unit is selected
    unitSelect.value = '';
    assignTenantSubmit.disabled = true;
    assignTenantSubmit.innerHTML = '<i class="mdi mdi-account-plus me-1"></i> Reassign Tenant';
    
    // Update modal title for reassignment
    const modalTitle = document.getElementById('assignTenantModalLabel');
    if (modalTitle) {
        modalTitle.textContent = 'Reassign Tenant to New Unit';
    }
    
    // Show modal
    try {
        const modalElement = document.getElementById('assignTenantModal');
        console.log('Modal element found:', !!modalElement); // Debug log
        
        if (!modalElement) {
            console.error('Modal element not found!');
            alert('Error: Modal not found. Please refresh the page and try again.');
            return;
        }
        
        // Check if Bootstrap is available
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap is not loaded!');
            alert('Error: Bootstrap is not loaded. Please refresh the page and try again.');
            return;
        }
        
        const modal = new bootstrap.Modal(modalElement);
    modal.show();
        console.log('Modal should be showing now'); // Debug log
    } catch (error) {
        console.error('Error showing modal:', error);
        alert('Error opening modal: ' + error.message);
}
};

function viewCredentials(assignmentId, email) {
    // Fetch credentials from the server
    fetch(`/landlord/tenant-assignments/${assignmentId}/credentials`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('tenantEmail').textContent = data.email;
            document.getElementById('tenantPassword').textContent = data.password;
            
            const modal = new bootstrap.Modal(document.getElementById('credentialsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching credentials:', error);
            alert('Error fetching credentials. Please try again.');
        });
}

function copyCredentials() {
    const email = document.getElementById('tenantEmail').textContent;
    const password = document.getElementById('tenantPassword').textContent;
    const credentials = `Email: ${email}\nPassword: ${password}`;
    
    navigator.clipboard.writeText(credentials).then(function() {
        alert('Credentials copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard. Please copy manually.');
    });
}

function copyAssignedCredentials() {
    const email = document.getElementById('assignedTenantEmail').value;
    const password = document.getElementById('assignedTenantPassword').value;
    const credentials = `Email: ${email}\nPassword: ${password}`;
    navigator.clipboard.writeText(credentials).then(function() {
        alert('Credentials copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard. Please copy manually.');
    });
}

function copyText(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(element.value).then(function() {
        // Show success feedback
        const btn = element.nextElementSibling;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="mdi mdi-check"></i>';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        document.execCommand('copy');
        alert('Copied to clipboard!');
    });
}

function copyAllCredentials() {
    const email = document.getElementById('assignedTenantEmail').value;
    const password = document.getElementById('assignedTenantPassword').value;
    const credentials = `Tenant Login Credentials:
Email: ${email}
Password: ${password}

Please use these credentials to log in and upload your required documents.`;
    
    navigator.clipboard.writeText(credentials).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Copied!';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 3000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard. Please copy manually.');
    });
}

function printCredentials() {
    const email = document.getElementById('assignedTenantEmail').value;
    const password = document.getElementById('assignedTenantPassword').value;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Tenant Login Credentials</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; color: #28a745; margin-bottom: 30px; }
                .credentials { background: #f8f9fa; padding: 20px; border: 2px solid #28a745; border-radius: 8px; margin: 20px 0; }
                .credential-item { margin: 15px 0; }
                .label { font-weight: bold; color: #333; }
                .value { font-size: 1.2rem; color: #dc3545; font-weight: bold; margin-left: 10px; }
                .footer { margin-top: 30px; color: #666; font-size: 0.9rem; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>🏠 HouseSync - Tenant Login Credentials</h2>
                <p>Generated on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
            </div>
            
            <div class="credentials">
                <div class="credential-item">
                    <span class="label">📧 Email Address:</span>
                    <span class="value">${email}</span>
                </div>
                <div class="credential-item">
                    <span class="label">🔑 Password:</span>
                    <span class="value">${password}</span>
                </div>
            </div>
            
            <div class="footer">
                <h4>Important Instructions:</h4>
                <ul>
                    <li>Use these credentials to log in to your tenant dashboard</li>
                    <li>Upload all required documents after logging in</li>
                    <li>Your assignment will be activated once documents are verified</li>
                    <li>Keep these credentials secure and private</li>
                </ul>
                <p><strong>Login URL:</strong> ${window.location.origin}/login</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function deleteTenantAssignment(assignmentId, tenantName) {
    if (confirm(`Are you sure you want to delete the assignment for ${tenantName}? This action cannot be undone.`)) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/landlord/tenant-assignments/${assignmentId}`;
        deleteForm.submit();
    }
}

// Set up modal form to update action URL with selected unit
const assignTenantForm = document.getElementById('assignTenantForm');
const unitSelect = document.getElementById('unit_id');
const assignTenantSubmit = document.getElementById('assignTenantSubmit');

if (assignTenantForm && unitSelect && assignTenantSubmit) {
    unitSelect.addEventListener('change', function() {
        console.log('Unit selected:', this.value, 'isReassigning:', window.isReassigning); // Debug log
        if (this.value) {
            if (!window.isReassigning) {
                const baseAction = "/landlord/units/";
                const fullAction = baseAction + this.value + "/assign-tenant";
                assignTenantForm.action = fullAction;
                console.log('Form action set to:', fullAction); // Debug log
            } else {
                console.log('Reassigning mode - keeping existing form action:', assignTenantForm.action); // Debug log
            }
            
            // Auto-populate rent amount from selected unit
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.textContent) {
                // Extract rent amount from option text (format: "Unit Number (Apartment Name) - ₱XX,XXX.XX")
                const rentMatch = selectedOption.textContent.match(/₱([\d,]+\.?\d*)/);
                if (rentMatch) {
                    const rentAmount = rentMatch[1].replace(/,/g, '');
                    document.getElementById('rent_amount').value = rentAmount;
                }
            }
            
            assignTenantSubmit.disabled = false;
            
            // Update button text based on mode
            if (window.isReassigning) {
                assignTenantSubmit.innerHTML = '<i class="mdi mdi-account-plus me-1"></i> Reassign Tenant';
        } else {
                assignTenantSubmit.innerHTML = '<i class="mdi mdi-account-plus me-1"></i> Assign Tenant';
            }
        } else {
            if (!window.isReassigning) {
            assignTenantForm.action = '#';
            }
            assignTenantSubmit.disabled = true;
            // Clear rent amount when no unit selected
            document.getElementById('rent_amount').value = '';
        }
    });
    
    // Prevent form submission if no unit is selected or action is not set
    assignTenantForm.addEventListener('submit', function(e) {
        console.log('Form submitting with action:', this.action); // Debug log
        console.log('Unit selected:', unitSelect.value); // Debug log
        
        if (!unitSelect.value) {
            e.preventDefault();
            alert('Please select a unit to assign.');
            return false;
        }
        
        if (this.action === '#' || this.action.endsWith('#')) {
            e.preventDefault();
            alert('Form action not properly set. Please select a unit again.');
            return false;
        }
        
        // Show loading state
        assignTenantSubmit.disabled = true;
        if (window.isReassigning) {
            assignTenantSubmit.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Reassigning...';
        } else {
            assignTenantSubmit.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Assigning...';
        }
    });
}
// Reset modal state when closed for fresh new assignment flow
const assignModalEl = document.getElementById('assignTenantModal');
if (assignModalEl) {
    assignModalEl.addEventListener('hidden.bs.modal', function () {
        console.log('Modal closing, resetting state'); // Debug log
        
        // Reset reassigning flag
        window.isReassigning = false;
        
        const nameInput = document.getElementById('name');
        const phoneInput = document.getElementById('phone');
        
        // Reset name input
        if (nameInput) {
            nameInput.readOnly = false;
            nameInput.classList.remove('bg-light');
            nameInput.value = '';
            nameInput.removeAttribute('placeholder');
        }
        
        // Reset phone input
        if (phoneInput) {
            phoneInput.readOnly = false;
            phoneInput.classList.remove('bg-light');
            phoneInput.value = '';
            phoneInput.removeAttribute('placeholder');
        }
        
        // Reset form action and disable submit until unit selected in new-assignment mode
        if (assignTenantForm) {
        assignTenantForm.action = '#';
            assignTenantForm.reset(); // Reset all form fields
        }
        
        if (assignTenantSubmit) {
        assignTenantSubmit.disabled = true;
            assignTenantSubmit.innerHTML = '<i class="mdi mdi-account-plus me-1"></i> Assign Tenant'; // Reset button text
        }
        
        if (unitSelect) {
        unitSelect.value = '';
        }
        
        // Reset modal title
        const modalTitle = document.getElementById('assignTenantModalLabel');
        if (modalTitle) {
            modalTitle.textContent = 'Assign Tenant to Unit';
        }
        
        // Remove any error messages
        const errorAlert = document.querySelector('#assignTenantModal .alert-danger');
        if (errorAlert) {
            errorAlert.remove();
        }
    });
}
// Set minimum date for lease start date to today
const today = new Date().toISOString().split('T')[0];
document.getElementById('lease_start_date').min = today;
document.getElementById('lease_start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('lease_end_date');
    endDateInput.min = startDate;
    if (endDateInput.value && endDateInput.value < startDate) {
        endDateInput.value = '';
    }
});

// New functions for the new credentials modal
function copyAllNewCredentials() {
    const email = document.getElementById('newTenantEmail').value;
    const password = document.getElementById('newTenantPassword').value;
    const credentials = `🏠 HouseSync - New Tenant Account Created

📧 Email: ${email}
🔑 Password: ${password}

Instructions:
1. Use these credentials to log in to your tenant dashboard
2. Upload required documents after logging in
3. Change your password after first login for security

Login URL: ${window.location.origin}/login

Keep these credentials secure and private.`;
    
    navigator.clipboard.writeText(credentials).then(function() {
        // Show success toast
        showSuccessToast('All credentials copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Could not copy to clipboard. Please copy manually.');
    });
}

function printNewCredentials() {
    const email = document.getElementById('newTenantEmail').value;
    const password = document.getElementById('newTenantPassword').value;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Tenant Login Credentials</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; color: #28a745; margin-bottom: 30px; }
                .credentials { background: #f8f9fa; padding: 20px; border: 2px solid #28a745; border-radius: 8px; margin: 20px 0; }
                .field { margin: 10px 0; }
                .label { font-weight: bold; color: #333; }
                .value { font-family: monospace; font-size: 1.1em; color: #0d6efd; }
                .instructions { background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0; }
                .warning { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🏠 HouseSync</h1>
                <h2>New Tenant Account Credentials</h2>
                <p>Generated on ${new Date().toLocaleString()}</p>
            </div>
            
            <div class="credentials">
                <div class="field">
                    <div class="label">📧 Email Address:</div>
                    <div class="value">${email}</div>
                </div>
                <div class="field">
                    <div class="label">🔑 Password:</div>
                    <div class="value">${password}</div>
                </div>
            </div>
            
            <div class="instructions">
                <h3>📋 Instructions for Tenant:</h3>
                <ol>
                    <li>Use these credentials to log in to your tenant dashboard</li>
                    <li>Upload all required documents after logging in</li>
                    <li>Change your password after first login for security</li>
                    <li>Contact your landlord if you have any issues</li>
                </ol>
                <p><strong>Login URL:</strong> ${window.location.origin}/login</p>
            </div>
            
            <div class="warning">
                <h3>⚠️ Important Security Notes:</h3>
                <ul>
                    <li>Keep these credentials secure and private</li>
                    <li>Do not share with unauthorized persons</li>
                    <li>Change password after first login</li>
                    <li>Report any suspicious activity immediately</li>
                </ul>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function emailCredentials() {
    const email = document.getElementById('newTenantEmail').value;
    const password = document.getElementById('newTenantPassword').value;
    
    const subject = 'HouseSync - Your New Tenant Account Credentials';
    const body = `Dear Tenant,

Welcome to HouseSync! Your new tenant account has been created.

Your login credentials:
📧 Email: ${email}
🔑 Password: ${password}

Please follow these steps:
1. Log in to your tenant dashboard at: ${window.location.origin}/login
2. Upload all required documents
3. Change your password after first login

If you have any questions, please contact your landlord.

Best regards,
Your Landlord`;

    const mailtoLink = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.open(mailtoLink);
}

function showSuccessToast(message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'alert alert-success position-fixed';
    toast.style.cssText = `
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    `;
    toast.innerHTML = `
        <i class="mdi mdi-check-circle me-2"></i>${message}
        <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Auto-show credentials modal when page loads with credentials
document.addEventListener('DOMContentLoaded', function() {
    @if(session('credentials'))
        const credentialsData = @json(session('credentials'));
        showNewCredentialsModal(credentialsData.email, credentialsData.password);
    @endif
});

function showNewCredentialsModal(email, password) {
    // Populate the modal with credentials
    document.getElementById('newTenantEmail').value = email;
    document.getElementById('newTenantPassword').value = password;
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('newCredentialsModal'), {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();
}
</script>
@endpush 