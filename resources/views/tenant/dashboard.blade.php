@extends('layouts.app')

@section('title', 'Tenant Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">Welcome, {{ auth()->user()->name }}!</h4>
            </div>
        </div>
    </div>

    <!-- Assignment Status -->
    <div class="row">
        <div class="col-12">
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
        </div>
    </div>

    <!-- Assignment Information -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Your Assignment Details</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
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
                                    <td><strong>Bedrooms:</strong></td>
                                    <td>{{ $assignment->unit->bedrooms }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bathrooms:</strong></td>
                                    <td>{{ $assignment->unit->bathrooms }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Lease Start:</strong></td>
                                    <td>{{ $assignment->lease_start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lease End:</strong></td>
                                    <td>{{ $assignment->lease_end_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly Rent:</strong></td>
                                    <td>₱{{ number_format($assignment->rent_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Security Deposit:</strong></td>
                                    <td>₱{{ number_format($assignment->security_deposit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Assignment Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status_badge_class }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($assignment->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Additional Notes:</h6>
                            <p class="text-muted">{{ $assignment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- Document Status -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Document Status</h5>
                    
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $assignment->documents_status_badge_class }} fs-6">
                            {{ ucfirst($assignment->documents_status) }}
                        </span>
                    </div>

                    @if(!$assignment->documents_uploaded)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Documents Required</h6>
                            <p class="mb-2">Please upload the required documents to complete your assignment.</p>
                            <a href="{{ route('tenant.upload-documents') }}" class="btn btn-warning btn-sm">
                                Upload Documents
                            </a>
                        </div>
                    @elseif(!$assignment->documents_verified)
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Documents Under Review</h6>
                            <p class="mb-0">Your documents have been uploaded and are being reviewed by your landlord.</p>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <h6 class="alert-heading">Documents Verified</h6>
                            <p class="mb-0">All your documents have been verified and approved.</p>
                        </div>
                    @endif

                    @if($assignment->verification_notes)
                        <div class="mt-3">
                            <h6>Verification Notes:</h6>
                            <p class="text-muted small">{{ $assignment->verification_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    
                    <div class="d-grid gap-2">
                        @if(!$assignment->documents_uploaded)
                            <a href="{{ route('tenant.upload-documents') }}" class="btn btn-primary">
                                <i class="mdi mdi-upload me-1"></i> Upload Documents
                            </a>
                        @endif
                        
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="mdi mdi-file-document me-1"></i> View Documents
                        </a>
                        
                        <a href="#" class="btn btn-outline-info">
                            <i class="mdi mdi-message me-1"></i> Contact Landlord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    @if($assignment->documents->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Uploaded Documents</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-centered">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>File Name</th>
                                    <th>Uploaded</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignment->documents as $document)
                                <tr>
                                    <td>{{ $document->document_type_label }}</td>
                                    <td>{{ $document->file_name }}</td>
                                    <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $document->verification_status_badge_class }}">
                                            {{ ucfirst($document->verification_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(in_array($document->mime_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']))
                                                <!-- Image Preview -->
                                                <button type="button" class="btn btn-sm btn-outline-info" onclick="viewImage('{{ asset('storage/' . $document->file_path) }}', '{{ $document->file_name }}')">
                                                    <i class="mdi mdi-eye"></i> View
                                                </button>
                                            @elseif($document->mime_type === 'application/pdf')
                                                <!-- PDF Viewer -->
                                                <button type="button" class="btn btn-sm btn-outline-info" onclick="viewPDF('{{ asset('storage/' . $document->file_path) }}', '{{ $document->file_name }}')">
                                                    <i class="mdi mdi-file-pdf"></i> View
                                                </button>
                                            @else
                                                <!-- Generic File Viewer -->
                                                <button type="button" class="btn btn-sm btn-outline-info" onclick="viewFile('{{ asset('storage/' . $document->file_path) }}', '{{ $document->file_name }}')">
                                                    <i class="mdi mdi-eye"></i> View
                                                </button>
                                            @endif
                                            
                                            <a href="{{ route('tenant.download-document', $document->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-download"></i> Download
                                            </a>
                                            
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteDocument({{ $document->id }}, '{{ $document->file_name }}')">
                                                <i class="mdi mdi-delete"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Required Documents Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Required Documents</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Essential Documents:</h6>
                            <ul>
                                <li><strong>Government ID:</strong> Passport, Driver's License, or any valid government-issued ID</li>
                                <li><strong>Proof of Income:</strong> Recent payslips, employment contract, or business registration</li>
                                <li><strong>Bank Statement:</strong> Last 3 months of bank statements</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Additional Documents:</h6>
                            <ul>
                                <li><strong>Character Reference:</strong> Letter from employer, colleague, or community leader</li>
                                <li><strong>Rental History:</strong> Previous rental agreements or landlord references (if applicable)</li>
                                <li><strong>Other:</strong> Any additional documents that may be required</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6 class="alert-heading">Document Guidelines:</h6>
                        <ul class="mb-0">
                            <li>All documents should be clear and legible</li>
                            <li>Accepted formats: PDF, JPG, JPEG, PNG</li>
                            <li>Maximum file size: 5MB per document</li>
                            <li>Documents will be reviewed within 2-3 business days</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Document Modal -->
<div class="modal fade" id="deleteDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">⚠️ Warning</h6>
                    <p class="mb-2">You are about to delete: <strong id="documentToDelete"></strong></p>
                    <p class="mb-0">This action cannot be undone. The document will be permanently removed.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteDocumentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete me-1"></i> Delete Document
                    </button>
                </form>
            </div>
        </div>
    </div>
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

@endsection

@push('scripts')
<script>
function deleteDocument(documentId, fileName) {
    document.getElementById('documentToDelete').textContent = fileName;
    document.getElementById('deleteDocumentForm').action = `/tenant/delete-document/${documentId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteDocumentModal'));
    modal.show();
}

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
</script>
@endpush 