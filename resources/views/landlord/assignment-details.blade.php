@extends('layouts.landlord-app')

@section('title', 'Assignment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('landlord.tenant-assignments') }}">Tenant Assignments</a></li>
                        <li class="breadcrumb-item active">Assignment Details</li>
                    </ol>
                </div>
                <h4 class="page-title">Assignment Details</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Assignment Information -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Assignment Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tenant Name:</strong></td>
                                    <td>{{ $assignment->tenant->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit Number:</strong></td>
                                    <td>{{ $assignment->unit->unit_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Apartment:</strong></td>
                                    <td>{{ $assignment->unit->apartment->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit Type:</strong></td>
                                    <td>{{ $assignment->unit->unit_type }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly Rent:</strong></td>
                                    <td>₱{{ number_format($assignment->rent_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($assignment->status === 'pending_approval')
                                    <tr>
                                        <td><strong>Occupation:</strong></td>
                                        <td>{{ $assignment->occupation ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Monthly Income:</strong></td>
                                        <td>₱{{ number_format($assignment->monthly_income ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Application Date:</strong></td>
                                        <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td><strong>Lease Start:</strong></td>
                                        <td>{{ $assignment->lease_start_date ? $assignment->lease_start_date->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lease End:</strong></td>
                                        <td>{{ $assignment->lease_end_date ? $assignment->lease_end_date->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Security Deposit:</strong></td>
                                        <td>₱{{ number_format($assignment->security_deposit, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Assignment Status:</strong></td>
                                    <td>
                                        @php
                                            $badgeClass = match($assignment->status) {
                                                'active' => 'success',
                                                'pending' => 'warning',
                                                'pending_approval' => 'warning',
                                                'terminated' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusLabel = match($assignment->status) {
                                                'pending_approval' => 'Pending Approval',
                                                default => ucfirst($assignment->status)
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Documents Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->documents_status_badge_class }}">
                                            {{ ucfirst($assignment->documents_status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($assignment->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Notes:</h6>
                            <p class="text-muted">{{ $assignment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Approve/Reject Buttons for Pending Applications -->
            @if($assignment->status === 'pending_approval')
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <button class="btn btn-success btn-lg" onclick="approveApplication({{ $assignment->id }})">
                                <i class="mdi mdi-check-circle me-1"></i> Approve Application
                            </button>
                            <button class="btn btn-danger btn-lg" onclick="rejectApplication({{ $assignment->id }})">
                                <i class="mdi mdi-close-circle me-1"></i> Reject Application
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    
                    <div class="d-grid gap-2">
                        @if($assignment->status === 'pending')
                            <form method="POST" action="{{ route('landlord.update-assignment-status', $assignment->id) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to activate this assignment?')">
                                    <i class="mdi mdi-check me-1"></i> Activate Assignment
                                </button>
                            </form>
                        @endif

                        @if($assignment->status === 'active')
                            <form method="POST" action="{{ route('landlord.update-assignment-status', $assignment->id) }}" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="terminated">
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to vacate this tenant from the unit?')">
                                    <i class="mdi mdi-close me-1"></i> Vacate Tenant
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('landlord.tenant-history', ['tenant_name' => $assignment->tenant->email]) }}" class="btn btn-outline-info">
                            <i class="mdi mdi-history me-1"></i> View Tenant History
                        </a>

                        <a href="{{ route('landlord.tenant-assignments') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to Assignments
                        </a>
                    </div>
                </div>
            </div>

            <!-- Document Verification -->
            @if($assignment->documents_uploaded && !$assignment->documents_verified)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Document Verification</h5>
                    
                    <form method="POST" action="{{ route('landlord.verify-documents', $assignment->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="verification_notes" class="form-label">Verification Notes (Optional)</label>
                            <textarea class="form-control" id="verification_notes" name="verification_notes" rows="3" placeholder="Add any notes about the document verification..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="mdi mdi-check me-1"></i> Verify Documents
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Uploaded Documents -->
    @if($assignment->documents->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Uploaded Documents</h5>
                    
                    <div class="row">
                        @foreach($assignment->documents as $document)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card document-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $document->document_type_label }}</h6>
                                            <small class="text-muted">{{ $document->file_name }}</small>
                                        </div>
                                        <span class="badge bg-{{ $document->verification_status_badge_class }}">
                                            {{ ucfirst($document->verification_status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="mdi mdi-calendar me-1"></i>
                                            Uploaded: {{ $document->uploaded_at->format('M d, Y H:i') }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="mdi mdi-file me-1"></i>
                                            Size: {{ $document->file_size_formatted }}
                                        </small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        @if(in_array($document->mime_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']))
                                            <!-- Image Preview -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewImage('{{ asset('storage/' . $document->file_path) }}', '{{ $document->file_name }}')">
                                                <i class="mdi mdi-eye me-1"></i> View Image
                                            </button>
                                        @elseif($document->mime_type === 'application/pdf')
                                            <!-- PDF Viewer -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewPDF('{{ asset('storage/' . $document->file_path) }}', '{{ $document->file_name }}')">
                                                <i class="mdi mdi-file-pdf me-1"></i> View PDF
                                            </button>
                                        @else
                                            <!-- Generic File Viewer -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewFile('{{ asset('storage/' . $document->file_path) }}', '{{ $document->file_name }}')">
                                                <i class="mdi mdi-eye me-1"></i> View File
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('tenant.download-document', $document->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="mdi mdi-download me-1"></i> Download
                                        </a>

                                        @if($document->verification_status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success" onclick="verifyDocument({{ $document->id }}, '{{ $document->file_name }}')">
                                                <i class="mdi mdi-check me-1"></i> Verify
                                            </button>
                                        @elseif($document->verification_status === 'verified')
                                            <span class="btn btn-sm btn-success disabled">
                                                <i class="mdi mdi-check me-1"></i> Verified
                                            </span>
                                        @endif
                                    </div>

                                    @if($document->verification_notes)
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <strong>Verification Notes:</strong><br>
                                            {{ $document->verification_notes }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="mdi mdi-file-document-outline" style="font-size: 3rem; color: #6c757d;"></i>
                    <h5 class="mt-3">No Documents Uploaded</h5>
                    <p class="text-muted">The tenant hasn't uploaded any documents yet.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreview" src="" alt="Document Preview" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="imageDownloadLink" href="" class="btn btn-primary" download>
                    <i class="mdi mdi-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- PDF Viewer Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalTitle">PDF Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfViewer" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="pdfDownloadLink" href="" class="btn btn-primary" download>
                    <i class="mdi mdi-download me-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- File Viewer Modal -->
<div class="modal fade" id="fileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileModalTitle">File Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="alert alert-info">
                    <i class="mdi mdi-file-document me-2"></i>
                    This file type cannot be previewed directly. Please download the file to view it.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="fileDownloadLink" href="" class="btn btn-primary" download>
                    <i class="mdi mdi-download me-1"></i> Download File
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Document Verification Modal -->
<div class="modal fade" id="documentVerificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="documentVerificationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Document Verification</h6>
                        <p class="mb-2">You are about to verify: <strong id="documentName"></strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="verification_notes" class="form-label">Verification Notes (Optional)</label>
                        <textarea class="form-control" id="verification_notes" name="verification_notes" rows="3" placeholder="Add any notes about this document verification..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check me-1"></i> Verify Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
function viewImage(imageUrl, fileName) {
    document.getElementById('imagePreview').src = imageUrl;
    document.getElementById('imageModalTitle').textContent = fileName;
    document.getElementById('imageDownloadLink').href = imageUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

function viewPDF(pdfUrl, fileName) {
    document.getElementById('pdfViewer').src = pdfUrl;
    document.getElementById('pdfModalTitle').textContent = fileName;
    document.getElementById('pdfDownloadLink').href = pdfUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
    modal.show();
}

function viewFile(fileUrl, fileName) {
    document.getElementById('fileModalTitle').textContent = fileName;
    document.getElementById('fileDownloadLink').href = fileUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('fileModal'));
    modal.show();
}

function verifyDocument(documentId, fileName) {
    document.getElementById('documentName').textContent = fileName;
    document.getElementById('documentVerificationForm').action = `/landlord/documents/${documentId}/verify`;
    
    const modal = new bootstrap.Modal(document.getElementById('documentVerificationModal'));
    modal.show();
}

// Approve application
function approveApplication(assignmentId) {
    if (!confirm('Are you sure you want to approve this tenant application? This will activate the tenant and mark the unit as occupied.')) {
        return;
    }
    
    fetch(`/landlord/tenant-assignments/${assignmentId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Application approved successfully!');
            window.location.href = '/landlord/tenant-assignments';
        } else {
            alert(data.message || 'Failed to approve application');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving the application');
    });
}

// Reject application
function rejectApplication(assignmentId) {
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason === null) return; // User cancelled
    
    if (!confirm('Are you sure you want to reject this tenant application?')) {
        return;
    }
    
    fetch(`/landlord/tenant-assignments/${assignmentId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Application rejected successfully');
            window.location.href = '/landlord/tenant-assignments';
        } else {
            alert(data.message || 'Failed to reject application');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rejecting the application');
    });
}
</script>

<style>
.document-card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #e5e7eb;
}

.document-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#imagePreview {
    max-width: 100%;
    border-radius: 8px;
}

#pdfViewer {
    border-radius: 8px;
}
</style>
@endpush 