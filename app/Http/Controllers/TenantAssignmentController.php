<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\TenantAssignment;
use App\Models\TenantDocument;
use App\Services\TenantAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantAssignmentController extends Controller
{
    protected $assignmentService;

    public function __construct(TenantAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Show tenant assignments for landlord
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'documents_uploaded', 'documents_verified']);
        $assignments = $this->assignmentService->getLandlordAssignments(Auth::id(), $filters);
        $stats = $this->assignmentService->getLandlordStats(Auth::id());

        return view('landlord.tenant-assignments', compact('assignments', 'stats', 'filters'));
    }
    
    public function store(Request $request, $unitId)
    {
        // Enhanced validation rules
        $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'address' => 'nullable|string|max:500',
            'lease_start_date' => 'required|date|after_or_equal:today',
            'lease_end_date' => 'required|date|after:lease_start_date|before:2 years',
            'rent_amount' => 'required|numeric|min:1000|max:100000',
            'security_deposit' => 'nullable|numeric|min:0|max:50000',
            'notes' => 'nullable|string|max:1000',
        ], [
            'phone.regex' => 'Please enter a valid phone number',
            'lease_end_date.before' => 'Lease cannot exceed 2 years',
            'rent_amount.min' => 'Rent must be at least ₱1,000',
            'rent_amount.max' => 'Rent cannot exceed ₱100,000',
            'security_deposit.max' => 'Security deposit cannot exceed ₱50,000',
        ]);

        try {
            // Use database transaction to ensure data consistency
            $result = DB::transaction(function() use ($request, $unitId) {
                return $this->assignmentService->assignTenantToUnit(
                    $unitId,
                    $request->all(),
                    Auth::id()
                );
            });

            if ($result['success']) {
                // Audit log for successful assignment
                Log::info('Tenant assigned successfully', [
                    'landlord_id' => Auth::id(),
                    'unit_id' => $unitId,
                    'tenant_email' => $request->email,
                    'lease_start_date' => $request->lease_start_date,
                    'lease_end_date' => $request->lease_end_date,
                    'rent_amount' => $request->rent_amount,
                    'timestamp' => now()
                ]);

                return redirect()->route('landlord.tenant-assignments')
                    ->with('success', 'Tenant assigned successfully!');
            } else {
                return back()->withInput()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Tenant assignment failed', [
                'landlord_id' => Auth::id(),
                'unit_id' => $unitId,
                'tenant_name' => $request->name,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->except(['_token']),
                'timestamp' => now()
            ]);

            return back()->withInput()->with('error', 'Failed to assign tenant. Please try again.');
        }
    }

    /**
     * Reassign a previously vacated tenant (existing tenant) to a new unit
     */
    public function reassign(Request $request, $assignmentId)
    {
        // Enhanced validation rules for reassignment
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'lease_start_date' => 'required|date|after_or_equal:today',
            'lease_end_date' => 'required|date|after:lease_start_date|before:2 years',
            'rent_amount' => 'required|numeric|min:1000|max:100000',
            'security_deposit' => 'nullable|numeric|min:0|max:50000',
            'notes' => 'nullable|string|max:1000',
        ], [
            'lease_end_date.before' => 'Lease cannot exceed 2 years',
            'rent_amount.min' => 'Rent must be at least ₱1,000',
            'rent_amount.max' => 'Rent cannot exceed ₱100,000',
            'security_deposit.max' => 'Security deposit cannot exceed ₱50,000',
        ]);

        try {
            // Use database transaction with race condition protection
            $result = DB::transaction(function() use ($request, $assignmentId) {
                // Fetch the vacated assignment within landlord scope
                $assignment = TenantAssignment::where('landlord_id', Auth::id())
                    ->with(['tenant', 'unit'])
                    ->findOrFail($assignmentId);

                if ($assignment->status !== 'terminated') {
                    throw new \Exception('Only vacated tenants can be reassigned.');
                }

                // Get the old unit to free it up
                $oldUnit = $assignment->unit;
                
                // Race condition protection: Lock the target unit and check availability
                $newUnit = Unit::whereHas('apartment', function($q) {
                    $q->where('landlord_id', Auth::id());
                })
                ->where('id', $request->unit_id)
                ->where('status', 'available')
                ->lockForUpdate() // This prevents race conditions
                ->first();

                if (!$newUnit) {
                    throw new \Exception('Selected unit is not available or does not belong to you.');
                }

                // Free up the old unit (make it available again)
                $oldUnit->update([
                    'status' => 'available',
                    'tenant_count' => 0,
                ]);

                // Update the existing assignment with new unit and details
                $assignment->update([
                    'unit_id' => $newUnit->id,
                    'lease_start_date' => $request->lease_start_date,
                    'lease_end_date' => $request->lease_end_date,
                    'rent_amount' => $request->rent_amount,
                    'security_deposit' => $request->security_deposit ?? 0,
                    'status' => 'active',
                    'notes' => $request->notes ?? null,
                    'documents_uploaded' => false, // Reset document status for new assignment
                    'documents_verified' => false,
                ]);

                // Update new unit status to occupied
                $newUnit->update([
                    'status' => 'occupied',
                    'tenant_count' => 1,
                ]);

                return [
                    'assignment' => $assignment,
                    'old_unit' => $oldUnit,
                    'new_unit' => $newUnit
                ];
            });

            // Audit log for successful reassignment
            Log::info('Tenant reassigned successfully', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $assignmentId,
                'old_unit_id' => $result['old_unit']->id,
                'new_unit_id' => $result['new_unit']->id,
                'tenant_id' => $result['assignment']->tenant_id,
                'tenant_name' => $result['assignment']->tenant->name,
                'lease_start_date' => $request->lease_start_date,
                'lease_end_date' => $request->lease_end_date,
                'rent_amount' => $request->rent_amount,
                'timestamp' => now()
            ]);

            return redirect()->route('landlord.tenant-assignments')
                ->with('success', 'Tenant reassigned successfully. Credentials remain the same.');

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Tenant reassignment failed', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $assignmentId,
                'unit_id' => $request->unit_id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_data' => $request->except(['_token']),
                'timestamp' => now()
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show tenant assignment details
     */
    public function show($id)
    {
        $assignment = $this->assignmentService->getAssignmentDetails($id, Auth::id());
        return view('landlord.assignment-details', compact('assignment'));
    }

    /**
     * Update assignment status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,terminated',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            // Get the assignment to log the old status
            $assignment = TenantAssignment::where('landlord_id', Auth::id())
                ->with('tenant')
                ->findOrFail($id);

            $oldStatus = $assignment->status;

            // Update the assignment status
            $updatedAssignment = $this->assignmentService->updateAssignmentStatus(
                $id,
                $request->status,
                Auth::id()
            );

            // Audit log for status change
            Log::info('Assignment status updated', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'tenant_id' => $assignment->tenant_id,
                'tenant_name' => $assignment->tenant->name,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'reason' => $request->reason ?? 'No reason provided',
                'timestamp' => now()
            ]);

            return back()->with('success', 'Assignment status updated successfully.');

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Assignment status update failed', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'new_status' => $request->status,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'timestamp' => now()
            ]);

            return back()->with('error', 'Failed to update assignment status. Please try again.');
        }
    }

    /**
     * Show tenant dashboard
     */
    public function tenantDashboard()
    {
        $tenant = Auth::user();
        $assignments = $tenant->tenantAssignments()
            ->with(['unit.apartment', 'documents'])
            ->orderByRaw("FIELD(status, 'active', 'pending_approval', 'terminated')")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($assignments->isEmpty()) {
            // Redirect prospects (tenants without assignments) to property listings
            return redirect()->route('explore')->with('info', 'Browse available properties and contact landlords to get assigned to a unit.');
        }

        return view('tenant.dashboard', compact('assignments'));
    }

    /**
     * Show document upload form for tenant
     */
    public function uploadDocuments()
    {
        $tenant = Auth::user();
        $assignment = $tenant->tenantAssignments()->with(['unit.apartment', 'documents'])->first();

        if (!$assignment) {
            return redirect()->route('tenant.dashboard')->with('error', 'No assignment found.');
        }

        return view('tenant.upload-documents', compact('assignment'));
    }

    /**
     * Store uploaded documents
     */
    public function storeDocuments(Request $request)
    {
        // Enhanced validation rules
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_types.*' => 'required|string|in:government_id,proof_of_income,employment_contract,bank_statement,character_reference,rental_history,other',
        ], [
            'documents.*.mimes' => 'Only PDF, JPG, JPEG, and PNG files are allowed',
            'documents.*.max' => 'Each file must not exceed 5MB',
            'document_types.*.in' => 'Invalid document type selected',
        ]);

        $tenant = Auth::user();
        $assignment = $tenant->tenantAssignments()->first();

        if (!$assignment) {
            return back()->with('error', 'No assignment found.');
        }

        try {
            $uploadedDocuments = [];
            
            // Use database transaction for document uploads
            DB::transaction(function() use ($request, $assignment, &$uploadedDocuments) {
                foreach ($request->file('documents') as $index => $file) {
                    $documentType = $request->document_types[$index];
                    
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('tenant-documents', $fileName, 'public');

                    $document = TenantDocument::create([
                        'tenant_assignment_id' => $assignment->id,
                        'document_type' => $documentType,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now(),
                        'verification_status' => 'pending',
                    ]);

                    $uploadedDocuments[] = $document;
                }

                // Mark documents as uploaded and update assignment status
                $assignment->update([
                    'documents_uploaded' => true,
                    'documents_verified' => false, // New documents are always pending verification
                ]);
            });

            // Audit log for successful document upload
            Log::info('Documents uploaded successfully', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'assignment_id' => $assignment->id,
                'unit_id' => $assignment->unit_id,
                'landlord_id' => $assignment->landlord_id,
                'documents_count' => count($uploadedDocuments),
                'document_types' => $request->document_types,
                'total_size' => array_sum(array_map(fn($doc) => $doc->file_size, $uploadedDocuments)),
                'timestamp' => now()
            ]);

            return redirect()->route('tenant.dashboard')
                ->with('success', 'Documents uploaded successfully. They will be reviewed by your landlord.');

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Document upload failed', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'assignment_id' => $assignment->id ?? 'N/A',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'files_count' => count($request->file('documents', [])),
                'timestamp' => now()
            ]);

            return back()->with('error', 'Failed to upload documents. Please try again.');
        }
    }

    /**
     * Verify documents (landlord only)
     */
    public function verifyDocuments(Request $request, $assignmentId)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $assignment = TenantAssignment::where('landlord_id', Auth::id())
            ->findOrFail($assignmentId);

        $this->assignmentService->verifyDocuments(
            $assignmentId,
            Auth::id(),
            $request->verification_notes
        );

        return back()->with('success', 'Documents verified successfully.');
    }

    /**
     * Verify individual document (landlord only)
     */
    public function verifyIndividualDocument(Request $request, $documentId)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $document = TenantDocument::with('tenantAssignment.tenant')->findOrFail($documentId);
            
            // Check if landlord has access to this document
            if ($document->tenantAssignment->landlord_id !== Auth::id()) {
                abort(403, 'Unauthorized access to document.');
            }

            $oldStatus = $document->verification_status;

            // Update the individual document
            $document->update([
                'verification_status' => 'verified',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => $request->verification_notes,
            ]);

            // Check if all documents for this assignment are verified
            $assignment = $document->tenantAssignment;
            $pendingDocuments = $assignment->documents()->where('verification_status', 'pending')->count();
            
            $allDocumentsVerified = false;
            if ($pendingDocuments === 0) {
                // All documents verified, update assignment status
                $assignment->update([
                    'documents_verified' => true,
                    'verification_notes' => 'All documents verified',
                ]);

                // Update assignment status to active if it was pending
                if ($assignment->status === 'pending') {
                    $assignment->update(['status' => 'active']);
                }
                
                $allDocumentsVerified = true;
            }

            // Audit log for document verification
            Log::info('Document verified successfully', [
                'landlord_id' => Auth::id(),
                'document_id' => $documentId,
                'assignment_id' => $assignment->id,
                'tenant_id' => $assignment->tenant_id,
                'tenant_name' => $assignment->tenant->name,
                'document_type' => $document->document_type,
                'document_name' => $document->file_name,
                'old_status' => $oldStatus,
                'new_status' => 'verified',
                'verification_notes' => $request->verification_notes,
                'all_documents_verified' => $allDocumentsVerified,
                'assignment_status_updated' => $allDocumentsVerified && $assignment->status === 'active',
                'timestamp' => now()
            ]);

            return back()->with('success', 'Document verified successfully.');

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Document verification failed', [
                'landlord_id' => Auth::id(),
                'document_id' => $documentId,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'timestamp' => now()
            ]);

            return back()->with('error', 'Failed to verify document. Please try again.');
        }
    }

    /**
     * Download document
     */
    public function downloadDocument($documentId)
    {
        $document = TenantDocument::with('tenantAssignment')->findOrFail($documentId);
        
        // Check if user has access to this document
        $user = Auth::user();
        if ($user->isLandlord()) {
            if ($document->tenantAssignment->landlord_id !== $user->id) {
                abort(403);
            }
        } elseif ($user->isTenant()) {
            if ($document->tenantAssignment->tenant_id !== $user->id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($document->file_path), [
            'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"'
        ]);
    }

    /**
     * Delete document (tenant only)
     */
    public function deleteDocument($documentId)
    {
        try {
            $document = TenantDocument::with('tenantAssignment.tenant')->findOrFail($documentId);
            
            // Check if user is the tenant who uploaded this document
            if ($document->tenantAssignment->tenant_id !== Auth::id()) {
                abort(403, 'Unauthorized access to document.');
            }

            $assignment = $document->tenantAssignment;
            $documentType = $document->document_type;
            $fileName = $document->file_name;
            $fileSize = $document->file_size;

            // Use database transaction for document deletion
            DB::transaction(function() use ($document, $assignment) {
                // Delete the file from storage
                if (Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                // Delete the document record
                $document->delete();

                // Update assignment status based on remaining documents
                $remainingDocuments = $assignment->documents()->count();
                
                if ($remainingDocuments === 0) {
                    // No documents left, mark as not uploaded
                    $assignment->update([
                        'documents_uploaded' => false,
                        'documents_verified' => false,
                    ]);
                } else {
                    // Check if all remaining documents are verified
                    $pendingDocuments = $assignment->documents()->where('verification_status', 'pending')->count();
                    $allVerified = $pendingDocuments === 0;
                    
                    $assignment->update([
                        'documents_uploaded' => true,
                        'documents_verified' => $allVerified,
                    ]);
                }
            });

            // Audit log for document deletion
            Log::info('Document deleted successfully', [
                'tenant_id' => Auth::id(),
                'tenant_name' => $assignment->tenant->name,
                'document_id' => $documentId,
                'assignment_id' => $assignment->id,
                'unit_id' => $assignment->unit_id,
                'landlord_id' => $assignment->landlord_id,
                'document_type' => $documentType,
                'document_name' => $fileName,
                'file_size' => $fileSize,
                'verification_status' => $document->verification_status,
                'remaining_documents' => $assignment->documents()->count(),
                'timestamp' => now()
            ]);

            return back()->with('success', 'Document deleted successfully.');

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Document deletion failed', [
                'tenant_id' => Auth::id(),
                'document_id' => $documentId,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'timestamp' => now()
            ]);

            return back()->with('error', 'Failed to delete document. Please try again.');
        }
    }

    /**
     * Get tenant credentials
     */
    public function getCredentials($id)
    {
        $assignment = TenantAssignment::where('landlord_id', Auth::id())
            ->with('tenant')
            ->findOrFail($id);

        return response()->json([
            'email' => $assignment->tenant->email,
            'password' => $assignment->generated_password ?? 'Password not available'
        ]);
    }

    /**
     * Get available units for assignment
     */
    public function getAvailableUnits()
    {
        $units = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->where('status', 'available')
        ->with('apartment')
        ->get();

        return response()->json($units);
    }

    /**
     * Delete tenant assignment (landlord only)
     */
    public function destroy($id)
    {
        try {
            // Use database transaction for assignment deletion
            $result = DB::transaction(function() use ($id) {
                $assignment = TenantAssignment::where('landlord_id', Auth::id())
                    ->with(['tenant', 'unit', 'documents'])
                    ->findOrFail($id);

                $tenantName = $assignment->tenant->name;
                $tenantId = $assignment->tenant_id;
                $unitId = $assignment->unit_id;
                $documentsCount = $assignment->documents->count();
                $totalFileSize = $assignment->documents->sum('file_size');

                // Delete all associated documents first
                foreach ($assignment->documents as $document) {
                    // Delete the file from storage
                    if (Storage::disk('public')->exists($document->file_path)) {
                        Storage::disk('public')->delete($document->file_path);
                    }
                    // Delete the document record
                    $document->delete();
                }

                // Update the unit status back to available
                $assignment->unit->update([
                    'status' => 'available',
                    'tenant_count' => 0
                ]);

                // Delete the tenant user account (optional - you may want to keep it)
                // Uncomment the line below if you want to delete the tenant user account
                // $assignment->tenant->delete();

                // Delete the assignment
                $assignment->delete();

                return [
                    'tenant_name' => $tenantName,
                    'tenant_id' => $tenantId,
                    'unit_id' => $unitId,
                    'documents_count' => $documentsCount,
                    'total_file_size' => $totalFileSize
                ];
            });

            // Audit log for assignment deletion
            Log::info('Tenant assignment deleted successfully', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'tenant_id' => $result['tenant_id'],
                'tenant_name' => $result['tenant_name'],
                'unit_id' => $result['unit_id'],
                'documents_deleted' => $result['documents_count'],
                'total_file_size_deleted' => $result['total_file_size'],
                'timestamp' => now()
            ]);

            return redirect()->route('landlord.tenant-assignments')
                ->with('success', 'Tenant assignment deleted successfully. Unit is now available for new assignments.');

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Tenant assignment deletion failed', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'timestamp' => now()
            ]);

            return back()->with('error', 'Failed to delete tenant assignment. Please try again.');
        }
    }

    /**
     * Show tenant profile page
     */
    public function tenantProfile()
    {
        try {
            $tenant = Auth::user();
            
            if (!$tenant) {
                return redirect()->route('login')->with('error', 'Please log in to access your profile.');
            }
            
            $assignment = $tenant->tenantAssignments()
                ->with([
                    'unit.apartment.landlord', 
                    'documents',
                    'landlord'
                ])
                ->where('status', 'active')
                ->first();

            // Get RFID cards if available
            $rfidCards = collect();
            if ($assignment && class_exists('\App\Models\RfidCard')) {
                try {
                    $rfidCards = \App\Models\RfidCard::where('tenant_id', $tenant->id)
                        ->where('apartment_id', $assignment->unit->apartment->id)
                        ->get();
                } catch (\Exception $e) {
                    // RFID functionality might not be fully implemented yet
                    $rfidCards = collect();
                }
            }

            return view('tenant-profile', compact('tenant', 'assignment', 'rfidCards'));
            
        } catch (\Exception $e) {
            \Log::error('Tenant profile error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.dashboard')->with('error', 'Unable to load profile. Please try again.');
        }
    }

    /**
     * Get tenant password (for profile view only)
     */
    public function getTenantPassword(Request $request)
    {
        try {
            $tenant = Auth::user();
            
            // Get the tenant assignment to find the generated password
            $assignment = $tenant->tenantAssignments()
                ->where('status', 'active')
                ->first();
            
            $password = null;
            if ($assignment && $assignment->generated_password) {
                // Return the generated password if available
                $password = $assignment->generated_password;
            } else {
                // If no generated password, return a message
                $password = 'No generated password available. Contact your landlord.';
            }
            
            return response()->json(['password' => $password]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to retrieve password'], 500);
        }
    }

    /**
     * Show tenant lease page
     */
    public function tenantLease()
    {
        try {
            $tenant = Auth::user();
            
            if (!$tenant) {
                return redirect()->route('login')->with('error', 'Please log in to access your lease information.');
            }
            
            $assignment = $tenant->tenantAssignments()
                ->with([
                    'unit.apartment.landlord', 
                    'documents',
                    'landlord'
                ])
                ->where('status', 'active')
                ->first();

            return view('tenant-lease', compact('tenant', 'assignment'));
            
        } catch (\Exception $e) {
            \Log::error('Tenant lease error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.dashboard')->with('error', 'Unable to load lease information. Please try again.');
        }
    }

    /**
     * Update tenant password (only if documents are verified)
     */
    public function updatePassword(Request $request)
    {
        try {
            $tenant = Auth::user();
            
            if (!$tenant) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Check if tenant's documents are verified
            $assignment = $tenant->tenantAssignments()
                ->where('status', 'active')
                ->first();

            if (!$assignment || !$assignment->documents_verified) {
                return response()->json([
                    'error' => 'Password change is only available after your documents have been verified by the landlord.'
                ], 403);
            }

            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Verify current password
            if (!Hash::check($request->current_password, $tenant->password)) {
                return response()->json(['error' => 'Current password is incorrect.'], 400);
            }

            // Update password
            $tenant->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Log the password change
            \Log::info('Tenant password updated', [
                'tenant_id' => $tenant->id,
                'tenant_email' => $tenant->email,
                'updated_at' => now()
            ]);

            return response()->json(['success' => 'Password updated successfully!']);

        } catch (\Exception $e) {
            \Log::error('Password update error: ' . $e->getMessage(), [
                'tenant_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'An error occurred while updating your password.'], 500);
        }
    }

    /**
     * Apply for a property as a tenant (from explore page)
     */
    public function applyForProperty(Request $request, $propertyId)
    {
        // Validate the application data
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'occupation' => 'required|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_types.*' => 'required|string|in:government_id,proof_of_income,employment_contract,bank_statement,character_reference,rental_history,other',
            'notes' => 'nullable|string|max:1000',
        ], [
            'documents.*.mimes' => 'Only PDF, JPG, JPEG, and PNG files are allowed',
            'documents.*.max' => 'Each file must not exceed 5MB',
            'document_types.*.in' => 'Invalid document type selected',
        ]);

        try {
            $tenant = Auth::user();
            
            // Get the property from explore page
            $property = \App\Models\Property::findOrFail($propertyId);
            
            // Find an available unit from this landlord's apartments
            $unit = Unit::whereHas('apartment', function($q) use ($property) {
                $q->where('landlord_id', $property->landlord_id);
            })
            ->where('status', 'available')
            ->with('apartment.landlord')
            ->first();
            
            if (!$unit) {
                Log::warning('No available units found for application', [
                    'tenant_id' => $tenant->id,
                    'property_id' => $propertyId,
                    'landlord_id' => $property->landlord_id,
                    'timestamp' => now()
                ]);
                
                return back()->with('error', 'No available units found for this property. The landlord may not have set up units yet. Please contact the landlord directly.');
            }
            
            // Check if tenant already has an application for this specific unit
            $existingApplicationForUnit = TenantAssignment::where('tenant_id', $tenant->id)
                ->where('unit_id', $unit->id)
                ->whereIn('status', ['active', 'pending_approval'])
                ->first();

            if ($existingApplicationForUnit) {
                return back()->with('error', 'You already have an active or pending application for this unit.');
            }
            
            Log::info('Found unit for application', [
                'unit_id' => $unit->id,
                'apartment_id' => $unit->apartment_id,
                'landlord_id' => $property->landlord_id
            ]);

            // Create the tenant assignment with pending_approval status
            DB::transaction(function() use ($request, $unit, $tenant, $property) {
                // Update user info if provided (profile-centric)
                if ($tenant->tenantProfile) {
                    $tenant->tenantProfile->update([
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'address' => $request->address,
                    ]);
                }

                // Create the assignment as pending approval
                $assignment = TenantAssignment::create([
                    'tenant_id' => $tenant->id,
                    'unit_id' => $unit->id,
                    'landlord_id' => $unit->apartment->landlord_id,
                    'status' => 'pending_approval',
                    'lease_start_date' => null,
                    'lease_end_date' => null,
                    'rent_amount' => $unit->rent_amount ?? 0,
                    'security_deposit' => 0,
                    'occupation' => $request->occupation,
                    'monthly_income' => $request->monthly_income,
                    'notes' => $request->notes,
                    'documents_uploaded' => true,
                    'documents_verified' => false,
                ]);

                // Upload and store documents
                foreach ($request->file('documents') as $index => $file) {
                    $documentType = $request->document_types[$index];
                    
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('tenant-documents', $fileName, 'public');

                    TenantDocument::create([
                        'tenant_assignment_id' => $assignment->id,
                        'document_type' => $documentType,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now(),
                        'verification_status' => 'pending',
                    ]);
                }
            });

            // Audit log
            Log::info('Tenant application submitted', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'property_id' => $property->id,
                'unit_id' => $unit->id,
                'landlord_id' => $unit->apartment->landlord_id,
                'property_name' => $unit->apartment->name,
                'documents_count' => count($request->file('documents')),
                'timestamp' => now()
            ]);

            return redirect()->route('explore')
                ->with('success', 'Your application has been submitted successfully! The landlord will review it shortly.');

        } catch (\Exception $e) {
            Log::error('Tenant application failed', [
                'tenant_id' => Auth::id(),
                'property_id' => $propertyId,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);

            // Show detailed error in development
            $errorMessage = config('app.debug') 
                ? 'Failed to submit application: ' . $e->getMessage()
                : 'Failed to submit application. Please try again.';

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Approve tenant application
     */
    public function approveApplication($id)
    {
        try {
            DB::transaction(function() use ($id) {
                $assignment = TenantAssignment::where('landlord_id', Auth::id())
                    ->where('status', 'pending_approval')
                    ->with(['unit', 'tenant'])
                    ->findOrFail($id);

                // Update assignment status to active
                $assignment->update([
                    'status' => 'active',
                    'documents_verified' => true,
                    'lease_start_date' => now(),
                    'lease_end_date' => now()->addYear(),
                ]);

                // Update unit status to occupied
                $assignment->unit->update([
                    'status' => 'occupied',
                    'tenant_count' => 1
                ]);

                // Verify all documents
                $assignment->documents()->update([
                    'verification_status' => 'verified',
                    'verified_by' => Auth::id(),
                    'verified_at' => now()
                ]);
            });

            Log::info('Tenant application approved', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Application approval failed', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application'
            ], 500);
        }
    }

    /**
     * Reject tenant application
     */
    public function rejectApplication(Request $request, $id)
    {
        try {
            DB::transaction(function() use ($request, $id) {
                $assignment = TenantAssignment::where('landlord_id', Auth::id())
                    ->where('status', 'pending_approval')
                    ->with(['unit', 'tenant', 'documents'])
                    ->findOrFail($id);

                // Delete all uploaded documents
                foreach ($assignment->documents as $document) {
                    if (Storage::disk('public')->exists($document->file_path)) {
                        Storage::disk('public')->delete($document->file_path);
                    }
                    $document->delete();
                }

                // Store rejection reason in notes
                $assignment->update([
                    'notes' => 'Application rejected. Reason: ' . ($request->reason ?? 'No reason provided'),
                ]);

                // Delete the assignment
                $assignment->delete();
            });

            Log::info('Tenant application rejected', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'reason' => $request->reason ?? 'No reason provided',
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Application rejection failed', [
                'landlord_id' => Auth::id(),
                'assignment_id' => $id,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application'
            ], 500);
        }
    }
} 