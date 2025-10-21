<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\TenantAssignment;
use App\Models\TenantDocument;
use App\Models\User;
use App\Models\Property;
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
        $filters = $request->only(['status']);
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
            ->with(['unit.apartment', 'tenant.documents']) // Documents are now at tenant level
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
        
        // Get all personal documents for this tenant
        $personalDocuments = TenantDocument::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get the active assignment if one exists (optional now)
        $assignment = $tenant->tenantAssignments()->with(['unit.apartment'])->first();

        return view('tenant.upload-documents', compact('assignment', 'personalDocuments'));
    }

    /**
     * Store uploaded documents (personal documents for tenant profile)
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
        
        // No assignment required - these are personal documents
        try {
            $uploadedDocuments = [];
            
            // Use database transaction for document uploads
            DB::transaction(function() use ($request, $tenant, &$uploadedDocuments) {
                foreach ($request->file('documents') as $index => $file) {
                    $documentType = $request->document_types[$index];
                    
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('tenant-documents', $fileName, 'public');

                    $document = TenantDocument::create([
                        'tenant_id' => $tenant->id,
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
            });

            // Audit log for successful document upload
            Log::info('Personal documents uploaded successfully', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'documents_count' => count($uploadedDocuments),
                'document_types' => $request->document_types,
                'total_size' => array_sum(array_map(fn($doc) => $doc->file_size, $uploadedDocuments)),
                'timestamp' => now()
            ]);

            return redirect()->route('tenant.upload-documents')
                ->with('success', 'Personal documents uploaded successfully! You can now apply for properties.');

        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Personal document upload failed', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
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
     * Download document
     */
    public function downloadDocument($documentId)
    {
        $document = TenantDocument::with('tenant')->findOrFail($documentId);
        
        // Check if user has access to this document
        $user = Auth::user();
        if ($user->isLandlord()) {
            // Landlord can access document if they have any assignment with this tenant
            $hasAccess = TenantAssignment::where('tenant_id', $document->tenant_id)
                ->where('landlord_id', $user->id)
                ->exists();
            if (!$hasAccess) {
                abort(403);
            }
        } elseif ($user->isTenant()) {
            if ($document->tenant_id !== $user->id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        // Check if this is a view request (inline) or download request
        $inline = request()->get('inline', false);
        
        if ($inline) {
            // Display inline for viewing (images, PDFs)
            return response()->file(Storage::disk('public')->path($document->file_path), [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
        } else {
            // Force download
            return response()->file(Storage::disk('public')->path($document->file_path), [
                'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"'
            ]);
        }
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

                // Document deletion complete - no need to update assignment status
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
                'remaining_documents' => $assignment->tenant->documents()->count(),
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
                    ->with(['tenant.documents', 'unit'])
                    ->findOrFail($id);

                $tenantName = $assignment->tenant->name;
                $tenantId = $assignment->tenant_id;
                $unitId = $assignment->unit_id;
                $documentsCount = $assignment->tenant->documents->count();
                $totalFileSize = $assignment->tenant->documents->sum('file_size');

                // NOTE: We DO NOT delete tenant's personal documents
                // Documents belong to the tenant, not the assignment
                // The tenant may have other applications or need these documents later

                // Update the unit status back to available
                $assignment->unit->update([
                    'status' => 'available',
                    'tenant_count' => 0
                ]);

                // Delete the assignment only
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
                    'landlord'
                ])
                ->where('status', 'active')
                ->first();

            // Get all documents belonging to this tenant
            $personalDocuments = TenantDocument::where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->get();

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

            return view('tenant-profile', compact('tenant', 'assignment', 'rfidCards', 'personalDocuments'));
            
        } catch (\Exception $e) {
            \Log::error('Tenant profile error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.dashboard')->with('error', 'Unable to load profile. Please try again.');
        }
    }

    // Removed getTenantPassword - tenants create their own passwords during registration

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

            if (!$assignment) {
                return response()->json([
                    'error' => 'No active assignment found.'
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
        $tenant = Auth::user();
        
        // Check if tenant has uploaded personal documents
        $personalDocuments = TenantDocument::where('tenant_id', $tenant->id)->get();
        
        if ($personalDocuments->isEmpty()) {
            return back()->with('error', 'You must upload your personal documents before applying for a property. Please visit your profile or upload documents page to add required documents.');
        }
        
        // Validate the application data (no documents required in form anymore)
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'occupation' => 'required|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
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
            DB::transaction(function() use ($request, $unit, $tenant, $property, $personalDocuments) {
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
                ]);

                // Documents remain at tenant level - they are NOT linked to assignments
                // Landlord will view the tenant's personal documents when reviewing the application
            });

            // Audit log
            Log::info('Tenant application submitted', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'property_id' => $property->id,
                'unit_id' => $unit->id,
                'landlord_id' => $unit->apartment->landlord_id,
                'property_name' => $unit->apartment->name,
                'documents_count' => $personalDocuments->count(),
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
                    'lease_start_date' => now(),
                    'lease_end_date' => now()->addYear(),
                ]);

                // Update unit status to occupied
                $assignment->unit->update([
                    'status' => 'occupied',
                    'tenant_count' => 1
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
                    ->with(['unit', 'tenant.documents'])
                    ->findOrFail($id);

                // NOTE: We DO NOT delete tenant's personal documents when rejecting
                // Documents belong to the tenant and can be used for other applications

                // Store rejection reason in notes
                $assignment->update([
                    'notes' => 'Application rejected. Reason: ' . ($request->reason ?? 'No reason provided'),
                ]);

                // Delete the assignment only
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