<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    // ==================== LANDLORD METHODS ====================
    
    /**
     * Display a listing of maintenance requests for landlord
     */
    public function index(Request $request)
    {
        $landlordId = Auth::id();
        
        // Base query
        $query = MaintenanceRequest::with(['unit.apartment', 'tenant.tenantProfile', 'assignedStaff.staffProfile'])
            ->where('landlord_id', $landlordId);
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }
        
        // Search by title or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Get maintenance requests with pagination
        $maintenanceRequests = $query->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => MaintenanceRequest::where('landlord_id', $landlordId)->count(),
            'pending' => MaintenanceRequest::where('landlord_id', $landlordId)->where('status', 'pending')->count(),
            'assigned' => MaintenanceRequest::where('landlord_id', $landlordId)->where('status', 'assigned')->count(),
            'in_progress' => MaintenanceRequest::where('landlord_id', $landlordId)->where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::where('landlord_id', $landlordId)->where('status', 'completed')->count(),
            'urgent' => MaintenanceRequest::where('landlord_id', $landlordId)->where('priority', 'urgent')->count(),
        ];
        
        return view('landlord.maintenance.index', compact('maintenanceRequests', 'stats'));
    }
    
    /**
     * Show form for landlord to create a maintenance request
     */
    public function create()
    {
        $landlordId = Auth::id();
        
        // Get landlord's units
        $units = Unit::whereHas('apartment', function($query) use ($landlordId) {
            $query->where('landlord_id', $landlordId);
        })
        ->with(['apartment', 'currentTenant.tenantProfile'])
        ->orderBy('unit_number')
        ->get();
        
        if ($units->isEmpty()) {
            return redirect()
                ->route('landlord.maintenance')
                ->with('error', 'You need to create units before creating maintenance requests.');
        }
        
        // Get available staff for this landlord
        $availableStaff = User::where('role', 'staff')
            ->whereHas('staffProfile', function($query) use ($landlordId) {
                $query->where('status', 'active')
                      ->where('created_by_landlord_id', $landlordId);
            })
            ->with('staffProfile')
            ->get();
        
        return view('landlord.maintenance.create', compact('units', 'availableStaff'));
    }
    
    /**
     * Store a landlord-created maintenance request
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:plumbing,electrical,hvac,appliance,structural,cleaning,other',
            'assigned_staff_id' => 'nullable|exists:users,id',
            'expected_completion_date' => 'nullable|date|after_or_equal:today',
            'staff_notes' => 'nullable|string|max:1000',
        ]);
        
        $landlordId = Auth::id();
        
        // Verify the unit belongs to this landlord
        $unit = Unit::whereHas('apartment', function($query) use ($landlordId) {
            $query->where('landlord_id', $landlordId);
        })->findOrFail($request->unit_id);
        
        // If staff is being assigned, verify they belong to this landlord
        if ($request->assigned_staff_id) {
            $staffMember = User::where('id', $request->assigned_staff_id)
                ->where('role', 'staff')
                ->whereHas('staffProfile', function($query) use ($landlordId) {
                    $query->where('created_by_landlord_id', $landlordId);
                })
                ->first();
            
            if (!$staffMember) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Invalid staff selection. You can only assign your own staff members.');
            }
        }
        
        // Determine status based on whether staff is assigned
        $status = $request->assigned_staff_id ? 'assigned' : 'pending';
        
        // Create the maintenance request
        $maintenanceRequest = MaintenanceRequest::create([
            'unit_id' => $request->unit_id,
            'tenant_id' => null, // Landlord-created, no tenant
            'landlord_id' => $landlordId,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => $status,
            'requested_date' => now(),
            'assigned_staff_id' => $request->assigned_staff_id,
            'expected_completion_date' => $request->expected_completion_date,
            'staff_notes' => $request->staff_notes,
        ]);
        
        return redirect()
            ->route('landlord.maintenance.show', $maintenanceRequest->id)
            ->with('success', 'Maintenance request created successfully!');
    }
    
    /**
     * Display the specified maintenance request
     */
    public function show($id)
    {
        $landlordId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::with([
            'unit.apartment', 
            'tenant.tenantProfile', 
            'assignedStaff.staffProfile',
            'landlord'
        ])
        ->where('landlord_id', $landlordId)
        ->findOrFail($id);
        
        // Get available staff for assignment
        // SECURITY: Only show staff created by THIS landlord (multi-tenant isolation)
        $availableStaff = User::where('role', 'staff')
            ->whereHas('staffProfile', function($query) use ($landlordId) {
                $query->where('status', 'active')
                      ->where('created_by_landlord_id', $landlordId); // CRITICAL: Landlord isolation
            })
            ->with('staffProfile')
            ->get();
        
        return view('landlord.maintenance.show', compact('maintenanceRequest', 'availableStaff'));
    }
    
    /**
     * Assign staff to a maintenance request
     */
    public function assignStaff(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'expected_completion_date' => 'nullable|date|after_or_equal:today',
        ]);
        
        $landlordId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('landlord_id', $landlordId)
            ->findOrFail($id);
        
        // SECURITY VALIDATION: Verify the staff member belongs to this landlord
        $staffMember = User::where('id', $request->staff_id)
            ->where('role', 'staff')
            ->whereHas('staffProfile', function($query) use ($landlordId) {
                $query->where('created_by_landlord_id', $landlordId);
            })
            ->first();
        
        if (!$staffMember) {
            return redirect()
                ->route('landlord.maintenance.show', $id)
                ->with('error', 'Invalid staff selection. You can only assign your own staff members.');
        }
        
        $updateData = [
            'assigned_staff_id' => $request->staff_id,
            'status' => 'assigned',
        ];
        
        if ($request->expected_completion_date) {
            $updateData['expected_completion_date'] = $request->expected_completion_date;
        }
        
        $maintenanceRequest->update($updateData);
        
        return redirect()
            ->route('landlord.maintenance.show', $id)
            ->with('success', 'Staff assigned successfully!');
    }
    
    /**
     * Update the status of a maintenance request
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
        ]);
        
        $landlordId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('landlord_id', $landlordId)
            ->findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        // If marking as completed, set completed date
        if ($request->status === 'completed') {
            $updateData['completed_date'] = now();
        }
        
        $maintenanceRequest->update($updateData);
        
        return redirect()
            ->route('landlord.maintenance.show', $id)
            ->with('success', 'Status updated successfully!');
    }
    
    /**
     * Update staff notes
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'staff_notes' => 'required|string',
        ]);
        
        $landlordId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('landlord_id', $landlordId)
            ->findOrFail($id);
        
        $maintenanceRequest->update([
            'staff_notes' => $request->staff_notes,
        ]);
        
        return redirect()
            ->route('landlord.maintenance.show', $id)
            ->with('success', 'Notes updated successfully!');
    }
    
    /**
     * Cancel a maintenance request
     */
    public function cancel($id)
    {
        $landlordId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('landlord_id', $landlordId)
            ->findOrFail($id);
        
        $maintenanceRequest->update([
            'status' => 'cancelled',
        ]);
        
        return redirect()
            ->route('landlord.maintenance')
            ->with('success', 'Maintenance request cancelled successfully!');
    }
    
    /**
     * Delete a maintenance request
     */
    public function destroy($id)
    {
        $landlordId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('landlord_id', $landlordId)
            ->findOrFail($id);
        
        $maintenanceRequest->delete();
        
        return redirect()
            ->route('landlord.maintenance')
            ->with('success', 'Maintenance request deleted successfully!');
    }
    
    // ==================== TENANT METHODS ====================
    
    /**
     * Display maintenance requests for tenant
     */
    public function tenantIndex(Request $request)
    {
        $tenantId = Auth::id();
        
        // Get tenant's active assignment to find landlord
        $activeAssignment = \App\Models\TenantAssignment::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();
        
        if (!$activeAssignment) {
            return view('tenant.maintenance.no-assignment');
        }
        
        // Base query
        $query = MaintenanceRequest::with(['unit.apartment', 'assignedStaff.staffProfile', 'landlord'])
            ->where('tenant_id', $tenantId);
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Get maintenance requests with pagination
        $maintenanceRequests = $query->paginate(10);
        
        // Get statistics
        $stats = [
            'total' => MaintenanceRequest::where('tenant_id', $tenantId)->count(),
            'pending' => MaintenanceRequest::where('tenant_id', $tenantId)->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::where('tenant_id', $tenantId)->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed' => MaintenanceRequest::where('tenant_id', $tenantId)->where('status', 'completed')->count(),
        ];
        
        return view('tenant.maintenance.index', compact('maintenanceRequests', 'stats', 'activeAssignment'));
    }
    
    /**
     * Show form for creating a new maintenance request
     */
    public function tenantCreate()
    {
        $tenantId = Auth::id();
        
        // Get tenant's active assignment
        $activeAssignment = \App\Models\TenantAssignment::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['unit.apartment', 'landlord'])
            ->first();
        
        if (!$activeAssignment) {
            return redirect()
                ->route('tenant.dashboard')
                ->with('error', 'You need an active unit assignment to create a maintenance request.');
        }
        
        return view('tenant.maintenance.create', compact('activeAssignment'));
    }
    
    /**
     * Store a newly created maintenance request
     */
    public function tenantStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:plumbing,electrical,hvac,appliance,structural,cleaning,other',
            'tenant_notes' => 'nullable|string',
        ]);
        
        $tenantId = Auth::id();
        
        // Get tenant's active assignment
        $activeAssignment = \App\Models\TenantAssignment::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->first();
        
        if (!$activeAssignment) {
            return redirect()
                ->route('tenant.dashboard')
                ->with('error', 'You need an active unit assignment to create a maintenance request.');
        }
        
        // Create the maintenance request
        $maintenanceRequest = MaintenanceRequest::create([
            'unit_id' => $activeAssignment->unit_id,
            'tenant_id' => $tenantId,
            'landlord_id' => $activeAssignment->landlord_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'pending',
            'requested_date' => now(),
            'tenant_notes' => $request->tenant_notes,
        ]);
        
        return redirect()
            ->route('tenant.maintenance')
            ->with('success', 'Maintenance request submitted successfully! Your landlord will be notified.');
    }
    
    /**
     * Display the specified maintenance request for tenant
     */
    public function tenantShow($id)
    {
        $tenantId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::with([
            'unit.apartment', 
            'landlord',
            'assignedStaff.staffProfile'
        ])
        ->where('tenant_id', $tenantId)
        ->findOrFail($id);
        
        return view('tenant.maintenance.show', compact('maintenanceRequest'));
    }
    
    /**
     * Update tenant notes on a maintenance request
     */
    public function tenantUpdateNotes(Request $request, $id)
    {
        $request->validate([
            'tenant_notes' => 'required|string',
        ]);
        
        $tenantId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('tenant_id', $tenantId)
            ->findOrFail($id);
        
        // Only allow updates if request is not completed or cancelled
        if (in_array($maintenanceRequest->status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('tenant.maintenance.show', $id)
                ->with('error', 'Cannot update notes on a completed or cancelled request.');
        }
        
        $maintenanceRequest->update([
            'tenant_notes' => $request->tenant_notes,
        ]);
        
        return redirect()
            ->route('tenant.maintenance.show', $id)
            ->with('success', 'Notes updated successfully!');
    }
    
    /**
     * Cancel a maintenance request (tenant side)
     */
    public function tenantCancel($id)
    {
        $tenantId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('tenant_id', $tenantId)
            ->findOrFail($id);
        
        // Only allow cancellation if not yet completed
        if ($maintenanceRequest->status === 'completed') {
            return redirect()
                ->route('tenant.maintenance')
                ->with('error', 'Cannot cancel a completed request.');
        }
        
        $maintenanceRequest->update([
            'status' => 'cancelled',
        ]);
        
        return redirect()
            ->route('tenant.maintenance')
            ->with('success', 'Maintenance request cancelled successfully!');
    }
    
    // ==================== STAFF METHODS ====================
    
    /**
     * Display maintenance requests assigned to staff
     */
    public function staffIndex(Request $request)
    {
        $staffId = Auth::id();
        
        // Base query - get requests assigned to this staff
        $query = MaintenanceRequest::with(['unit.apartment', 'tenant.tenantProfile', 'landlord'])
            ->where('assigned_staff_id', $staffId);
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Get maintenance requests with pagination
        $maintenanceRequests = $query->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => MaintenanceRequest::where('assigned_staff_id', $staffId)->count(),
            'pending' => MaintenanceRequest::where('assigned_staff_id', $staffId)->whereIn('status', ['pending', 'assigned'])->count(),
            'in_progress' => MaintenanceRequest::where('assigned_staff_id', $staffId)->where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::where('assigned_staff_id', $staffId)->where('status', 'completed')->count(),
        ];
        
        return view('staff.maintenance.index', compact('maintenanceRequests', 'stats'));
    }
    
    /**
     * Display specific maintenance request details for staff
     */
    public function staffShow($id)
    {
        $staffId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::with([
            'unit.apartment', 
            'tenant.tenantProfile', 
            'landlord.landlordProfile',
            'assignedStaff.staffProfile'
        ])
        ->where('assigned_staff_id', $staffId)
        ->findOrFail($id);
        
        return view('staff.maintenance.show', compact('maintenanceRequest'));
    }
    
    /**
     * Update maintenance request status by staff
     */
    public function staffUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:in_progress,completed',
        ]);
        
        $staffId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('assigned_staff_id', $staffId)
            ->findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        // If marking as completed, set completed date
        if ($request->status === 'completed') {
            $updateData['completed_date'] = now();
        }
        
        $maintenanceRequest->update($updateData);
        
        return redirect()
            ->route('staff.maintenance.show', $id)
            ->with('success', 'Status updated successfully!');
    }
    
    /**
     * Update staff notes on maintenance request
     */
    public function staffUpdateNotes(Request $request, $id)
    {
        $request->validate([
            'staff_notes' => 'required|string',
        ]);
        
        $staffId = Auth::id();
        
        $maintenanceRequest = MaintenanceRequest::where('assigned_staff_id', $staffId)
            ->findOrFail($id);
        
        // Only allow updates if request is not completed or cancelled
        if (in_array($maintenanceRequest->status, ['completed', 'cancelled'])) {
            return redirect()
                ->route('staff.maintenance.show', $id)
                ->with('error', 'Cannot update notes on a completed or cancelled request.');
        }
        
        $maintenanceRequest->update([
            'staff_notes' => $request->staff_notes,
        ]);
        
        return redirect()
            ->route('staff.maintenance.show', $id)
            ->with('success', 'Notes updated successfully!');
    }
}

