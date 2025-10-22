@extends('layouts.app')

@section('title', 'Upload Documents')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Upload Documents</li>
                    </ol>
                </div>
                <h4 class="page-title">Upload Required Documents</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Show Info Banner if No Assignment -->
    @if(!$assignment)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Upload Your Documents Early!</h6>
                <p class="mb-0">
                    You can upload your documents now, even before applying for a property. This will speed up the application process when you find a place you like!
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Existing Documents -->
    @if($personalDocuments->count() > 0)
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-folder-open me-2"></i>Your Uploaded Documents ({{ $personalDocuments->count() }})</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personalDocuments as $doc)
                                <tr>
                                    <td>
                                        <i class="fas fa-file-{{ $doc->mime_type == 'application/pdf' ? 'pdf' : 'image' }} me-1"></i>
                                        {{ $doc->document_type_label }}
                                    </td>
                                    <td>{{ $doc->file_name }}</td>
                                    <td>{{ $doc->file_size_formatted }}</td>
                                    <td>{{ $doc->uploaded_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $doc->verification_status_badge_class }}">
                                            {{ ucfirst($doc->verification_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($doc->mime_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']))
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewImage('{{ $doc->file_path }}', '{{ $doc->file_name }}')">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        @elseif($doc->mime_type === 'application/pdf')
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewPDF('{{ $doc->file_path }}', '{{ $doc->file_name }}')">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        @else
                                            <a href="{{ $doc->file_path }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @endif
                                        
                                        <a href="{{ $doc->file_path }}" download="{{ $doc->file_name }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        
                                        <form method="POST" action="{{ route('tenant.delete-document', $doc->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this document?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
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
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Document Upload Form</h5>
                    
                    <form method="POST" action="{{ route('tenant.store-documents') }}" enctype="multipart/form-data" id="documentForm">
                        @csrf
                        
                        <div id="documentFields">
                            <!-- Document fields will be added here dynamically -->
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary" onclick="addDocumentField()">
                                    <i class="mdi mdi-plus me-1"></i> Add Another Document
                                </button>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('tenant.dashboard') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-upload me-1"></i> Upload Documents
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- Document Guidelines -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Document Guidelines</h5>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Accepted Formats:</h6>
                        <ul class="mb-0">
                            <li>PDF files</li>
                            <li>JPG/JPEG images</li>
                            <li>PNG images</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">File Size Limit:</h6>
                        <p class="mb-0">Maximum 5MB per document</p>
                    </div>

                    <h6>Required Documents:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Government ID</strong><br>
                            <small class="text-muted">Passport, Driver's License, or any valid government-issued ID</small>
                        </li>
                        <li class="mb-2">
                            <strong>Proof of Income</strong><br>
                            <small class="text-muted">Recent payslips, employment contract, or business registration</small>
                        </li>
                        <li class="mb-2">
                            <strong>Bank Statement</strong><br>
                            <small class="text-muted">Last 3 months of bank statements</small>
                        </li>
                        <li class="mb-2">
                            <strong>Character Reference</strong><br>
                            <small class="text-muted">Letter from employer, colleague, or community leader</small>
                        </li>
                        <li class="mb-2">
                            <strong>Rental History</strong><br>
                            <small class="text-muted">Previous rental agreements or landlord references (if applicable)</small>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Assignment Info -->
            @if($assignment)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Your Assignment</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Unit:</strong></td>
                                <td>{{ $assignment->unit->unit_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Apartment:</strong></td>
                                <td>{{ $assignment->unit->apartment->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Monthly Rent:</strong></td>
                                <td>₱{{ number_format($assignment->rent_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lease Period:</strong></td>
                                <td>{{ $assignment->lease_start_date->format('M d, Y') }} - {{ $assignment->lease_end_date->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-lightbulb me-2"></i>Next Steps</h5>
                    <p>After uploading your documents:</p>
                    <ol>
                        <li>Browse available properties</li>
                        <li>Apply for units you like</li>
                        <li>Your documents will automatically be included with your application</li>
                        <li>Landlords can review your application faster!</li>
                    </ol>
                    <a href="{{ route('explore') }}" class="btn btn-primary w-100 mt-2">
                        <i class="fas fa-search me-2"></i>Browse Properties
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Document Type Options -->
<template id="documentTypeOptions">
    <option value="">Select Document Type</option>
    <option value="government_id">Government ID</option>
    <option value="proof_of_income">Proof of Income</option>
    <option value="employment_contract">Employment Contract</option>
    <option value="bank_statement">Bank Statement</option>
    <option value="character_reference">Character Reference</option>
    <option value="rental_history">Rental History</option>
    <option value="other">Other Document</option>
</template>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="background-color: #f8f9fa;">
                <img id="imagePreview" src="" alt="Document Preview" class="img-fluid" style="max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <a id="imageDownloadLink" href="" class="btn btn-success" download>
                    <i class="fas fa-download me-1"></i> Download Image
                </a>
            </div>
        </div>
    </div>
</div>

<!-- PDF Viewer Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalTitle">PDF Viewer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfViewer" src="" width="100%" height="600px" frameborder="0" style="border-radius: 0 0 8px 8px;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <a id="pdfDownloadLink" href="" class="btn btn-success" download>
                    <i class="fas fa-download me-1"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let documentFieldCount = 0;

// Image viewer function
function viewImage(imageUrl, fileName) {
    document.getElementById('imagePreview').src = imageUrl;
    document.getElementById('imageModalTitle').textContent = fileName;
    document.getElementById('imageDownloadLink').href = imageUrl;
    document.getElementById('imageDownloadLink').download = fileName;
    
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// PDF viewer function
function viewPDF(pdfUrl, fileName) {
    document.getElementById('pdfViewer').src = pdfUrl;
    document.getElementById('pdfModalTitle').textContent = fileName;
    document.getElementById('pdfDownloadLink').href = pdfUrl;
    document.getElementById('pdfDownloadLink').download = fileName;
    
    const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
    pdfModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Add initial document field
    addDocumentField();
});

function addDocumentField() {
    documentFieldCount++;
    
    const container = document.getElementById('documentFields');
    const template = document.getElementById('documentTypeOptions');
    
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'row mb-3 document-field';
    fieldDiv.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Document Type <span class="text-danger">*</span></label>
            <select name="document_types[]" class="form-select" required>
                ${template.innerHTML}
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">File <span class="text-danger">*</span></label>
            <input type="file" name="documents[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <small class="text-muted">Max size: 5MB</small>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDocumentField(this)">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(fieldDiv);
}

function removeDocumentField(button) {
    const fieldDiv = button.closest('.document-field');
    fieldDiv.remove();
}

// Form validation
document.getElementById('documentForm')?.addEventListener('submit', function(e) {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    
    for (let input of fileInputs) {
        if (input.files.length > 0) {
            const file = input.files[0];
            
            if (file.size > maxSize) {
                e.preventDefault();
                alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                return false;
            }
            
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                alert(`File "${file.name}" is not an accepted format. Please use PDF, JPG, JPEG, or PNG.`);
                return false;
            }
        }
    }
});
</script>
@endpush 