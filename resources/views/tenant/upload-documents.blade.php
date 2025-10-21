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
                <h4 class="page-title">Manage Personal Documents</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($personalDocuments->isEmpty())
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-information me-2"></i>
                    <strong>Important:</strong> You must upload your personal documents before you can apply for any property. Please upload at least one document to get started.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Uploaded Personal Documents -->
    @if($personalDocuments->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="mdi mdi-file-document-multiple me-2"></i>Your Personal Documents
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>File Name</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personalDocuments as $doc)
                                <tr>
                                    <td><strong>{{ $doc->document_type_label }}</strong></td>
                                    <td>{{ $doc->file_name }}</td>
                                    <td>{{ $doc->file_size_formatted }}</td>
                                    <td>{{ $doc->uploaded_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            @if(in_array($doc->mime_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']))
                                                <button type="button" onclick="viewImage('{{ route('tenant.download-document', $doc->id) }}?inline=true', '{{ $doc->file_name }}')" 
                                                        class="btn btn-sm btn-success" 
                                                        title="View Image">
                                                    <i class="mdi mdi-eye"></i> View
                                                </button>
                                            @elseif($doc->mime_type === 'application/pdf')
                                                <button type="button" onclick="viewPDF('{{ route('tenant.download-document', $doc->id) }}?inline=true', '{{ $doc->file_name }}')" 
                                                        class="btn btn-sm btn-success" 
                                                        title="View PDF">
                                                    <i class="mdi mdi-file-pdf-box"></i> View
                                                </button>
                                            @endif
                                            <a href="{{ route('tenant.download-document', $doc->id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Download">
                                                <i class="mdi mdi-download"></i> Download
                                            </a>
                                            <form method="POST" action="{{ route('tenant.delete-document', $doc->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="mdi mdi-delete"></i> Delete
                                                </button>
                                            </form>
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

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Upload New Documents</h5>
                    <p class="text-muted">Add your personal documents here. These documents will be used when you apply for properties.</p>
                    
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

            <!-- Assignment Info (if exists) -->
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
                            @if($assignment->lease_start_date && $assignment->lease_end_date)
                            <tr>
                                <td><strong>Lease Period:</strong></td>
                                <td>{{ $assignment->lease_start_date->format('M d, Y') }} - {{ $assignment->lease_end_date->format('M d, Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="imageViewerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 1100; padding: 20px;">
    <div style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <button onclick="closeImageViewer()" style="position: absolute; top: 20px; right: 20px; background: rgba(255, 255, 255, 0.2); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; z-index: 1101; transition: all 0.2s;">
            <i class="mdi mdi-close"></i>
        </button>
        <div style="text-align: center; margin-bottom: 20px;">
            <h3 id="imageViewerTitle" style="color: white; margin: 0; font-size: 20px; font-weight: 600;"></h3>
        </div>
        <div style="max-width: 90%; max-height: 90%; display: flex; align-items: center; justify-content: center;">
            <img id="imageViewerImage" src="" alt="" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);">
        </div>
    </div>
</div>

<!-- PDF Viewer Modal -->
<div id="pdfViewerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 1100; padding: 20px;">
    <div style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="pdfViewerTitle" style="color: white; margin: 0; font-size: 20px; font-weight: 600;"></h3>
            <button onclick="closePDFViewer()" style="background: rgba(255, 255, 255, 0.2); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; transition: all 0.2s;">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
        <div style="flex: 1; background: white; border-radius: 8px; overflow: hidden;">
            <iframe id="pdfViewerFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
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

@endsection

@push('styles')
<style>
.action-buttons {
    display: flex;
    gap: 0.25rem;
}
.action-buttons > * {
    margin: 0;
}
</style>
@endpush

@push('scripts')
<script>
let documentFieldCount = 0;

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
document.getElementById('documentForm').addEventListener('submit', function(e) {
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

// Image viewer functions
function viewImage(imagePath, fileName) {
    const modal = document.getElementById('imageViewerModal');
    const image = document.getElementById('imageViewerImage');
    const title = document.getElementById('imageViewerTitle');
    
    image.src = imagePath;
    image.alt = fileName;
    title.textContent = fileName;
    modal.style.display = 'block';
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeImageViewer() {
    const modal = document.getElementById('imageViewerModal');
    modal.style.display = 'none';
    
    // Re-enable body scroll
    document.body.style.overflow = 'auto';
}

// PDF viewer functions
function viewPDF(pdfPath, fileName) {
    const modal = document.getElementById('pdfViewerModal');
    const iframe = document.getElementById('pdfViewerFrame');
    const title = document.getElementById('pdfViewerTitle');
    
    iframe.src = pdfPath;
    title.textContent = fileName;
    modal.style.display = 'block';
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closePDFViewer() {
    const modal = document.getElementById('pdfViewerModal');
    const iframe = document.getElementById('pdfViewerFrame');
    modal.style.display = 'none';
    iframe.src = ''; // Clear iframe to stop loading
    
    // Re-enable body scroll
    document.body.style.overflow = 'auto';
}

// Close modals on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageViewer();
        closePDFViewer();
    }
});

// Close modals on background click
document.getElementById('imageViewerModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeImageViewer();
    }
});

document.getElementById('pdfViewerModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closePDFViewer();
    }
});
</script>
@endpush 