<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\StaffAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    /**
     * Show staff assignments for landlord
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'staff_type']);
        $assignments = $this->getLandlordStaffAssignments(Auth::id(), $filters);
        $stats = $this->getLandlordStaffStats(Auth::id());

        return view('landlord.staff', compact('assignments', 'stats', 'filters'));
    }

    /**
     * Show form to assign staff to unit
     */
    public function create($unitId = null)
    {
        $units = Unit::whereHas('apartment', function($query) {
            $query->where('landlord_id', Auth::id());
        })->with('apartment')->get();

        $selectedUnit = null;
        if ($unitId) {
            $selectedUnit = $units->find($unitId);
        }

        return view('landlord.assign-staff', compact('units', 'selectedUnit'));
    }

    /**
     * Add new staff member
     */
    public function addStaff(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'staff_type' => 'required|string|max:100',
            ]);

            // Generate unique email
            $baseEmail = strtolower(str_replace(' ', '.', $request->name)) . '@staff.housesync.com';
            $email = $baseEmail;
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = str_replace('@staff.housesync.com', $counter . '@staff.housesync.com', $baseEmail);
                $counter++;
            }

            // Generate password
            $password = Str::random(8);

            // Create staff user
            $staff = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'staff',
                'staff_type' => $request->staff_type,
                'status' => 'active',
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return redirect()->route('landlord.staff')
                ->with('success', 'Staff member added successfully!')
                ->with('staff_credentials', [
                    'email' => $email,
                    'password' => $password,
                    'staff_name' => $request->name,
                    'staff_type' => $request->staff_type
                ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add staff member: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Assign staff to unit
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'unit_id' => 'required|exists:units,id',
                'staff_id' => 'required|exists:users,id',
                'staff_type' => 'required|string|max:100',
                'assignment_start_date' => 'required|date|after_or_equal:today',
                'assignment_end_date' => 'nullable|date|after:assignment_start_date',
                'hourly_rate' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Verify landlord owns the unit
            $unit = Unit::whereHas('apartment', function($query) {
                $query->where('landlord_id', Auth::id());
            })->findOrFail($request->unit_id);

            // Verify the selected user is a staff member
            $staff = User::where('id', $request->staff_id)
                ->where('role', 'staff')
                ->where('status', 'active')
                ->firstOrFail();

            // Check if staff is already assigned to this unit
            $existingAssignment = StaffAssignment::where('unit_id', $request->unit_id)
                ->where('staff_id', $request->staff_id)
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                return back()->with('error', 'This staff member is already assigned to this unit.')->withInput();
            }

            // Create staff assignment
            $assignment = StaffAssignment::create([
                'unit_id' => $request->unit_id,
                'staff_id' => $staff->id,
                'landlord_id' => Auth::id(),
                'staff_type' => $request->staff_type,
                'assignment_start_date' => $request->assignment_start_date,
                'assignment_end_date' => $request->assignment_end_date,
                'hourly_rate' => $request->hourly_rate,
                'notes' => $request->notes,
            ]);

            return redirect()->route('landlord.staff')
                ->with('success', "Staff member {$staff->name} has been assigned to unit {$unit->unit_number} successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign staff: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show staff assignment details
     */
    public function show($id)
    {
        $assignment = StaffAssignment::where('landlord_id', Auth::id())
            ->with(['staff', 'unit.apartment'])
            ->findOrFail($id);

        return view('landlord.staff-details', compact('assignment'));
    }

    /**
     * Update staff assignment status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,terminated',
        ]);

        $assignment = StaffAssignment::where('landlord_id', Auth::id())
            ->findOrFail($id);

        $assignment->update(['status' => $request->status]);

        return back()->with('success', 'Staff assignment status updated successfully.');
    }

    /**
     * Delete staff assignment
     */
    public function destroy($id)
    {
        try {
            $assignment = StaffAssignment::where('landlord_id', Auth::id())
                ->with(['staff'])
                ->findOrFail($id);

            // Delete the staff user account
            $assignment->staff->delete();

            // Delete the assignment
            $assignment->delete();

            return redirect()->route('landlord.staff')
                ->with('success', 'Staff assignment deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete staff assignment. Please try again.');
        }
    }

    /**
     * Get staff members by type for assignment
     */
    public function getStaffByType($staffType)
    {
        // Get active staff members that match the selected staff type
        $staff = User::where('role', 'staff')
            ->where('status', 'active')
            ->where('staff_type', $staffType)
            ->select('id', 'name', 'email', 'staff_type')
            ->get();

        return response()->json(['staff' => $staff]);
    }

    /**
     * Get staff credentials
     */
    public function getCredentials($id)
    {
        $assignment = StaffAssignment::where('landlord_id', Auth::id())
            ->with('staff')
            ->findOrFail($id);

        return response()->json([
            'email' => $assignment->staff->email,
            'password' => $assignment->generated_password ?? 'Password not available'
        ]);
    }

    /**
     * Get landlord staff assignments with filters
     */
    private function getLandlordStaffAssignments($landlordId, $filters = [])
    {
        $query = StaffAssignment::where('landlord_id', $landlordId)
            ->with(['staff', 'unit.apartment']);

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['staff_type']) && $filters['staff_type']) {
            $query->where('staff_type', $filters['staff_type']);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get landlord staff statistics
     */
    private function getLandlordStaffStats($landlordId)
    {
        $assignments = StaffAssignment::where('landlord_id', $landlordId);
        
        return [
            'total_assignments' => $assignments->count(),
            'active_assignments' => $assignments->where('status', 'active')->count(),
            'inactive_assignments' => $assignments->where('status', 'inactive')->count(),
            'terminated_assignments' => $assignments->where('status', 'terminated')->count(),
            'total_staff_types' => $assignments->distinct('staff_type')->count(),
        ];
    }

    /**
     * Show staff dashboard
     */
    public function staffDashboard()
    {
        $staff = Auth::user();
        
        // Get staff's active assignment
        $assignment = StaffAssignment::where('staff_id', $staff->id)
            ->where('status', 'active')
            ->with(['unit.apartment', 'landlord'])
            ->first();

        if (!$assignment) {
            return view('staff.no-assignment');
        }

        // Get maintenance requests for the assigned unit
        $maintenanceRequests = $this->getMaintenanceRequests($assignment->unit_id);

        return view('staff.dashboard', compact('assignment', 'maintenanceRequests'));
    }

    /**
     * Get maintenance requests for a unit
     */
    private function getMaintenanceRequests($unitId)
    {
        return \App\Models\MaintenanceRequest::where('unit_id', $unitId)
            ->with(['tenant', 'assignedStaff'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
} 