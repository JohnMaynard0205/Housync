<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LandlordProfile;
use App\Models\TenantProfile;
use App\Models\StaffProfile;
use App\Models\SuperAdminProfile;
use App\Models\Apartment;
use App\Models\LandlordDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'pending_landlords' => User::pendingLandlords()->count(),
            'approved_landlords' => User::approvedLandlords()->count(),
            'total_tenants' => User::byRole('tenant')->count(),
            'total_apartments' => Apartment::count(),
        ];

        $pendingLandlords = User::pendingLandlords()->latest()->take(5)->get();
        $recentUsers = User::latest()->take(10)->get();

        return view('super-admin.dashboard', compact('stats', 'pendingLandlords', 'recentUsers'));
    }

    public function users()
    {
        $query = User::with('approvedBy');
        
        // Search by name or email
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by role
        if (request('role')) {
            $query->where('role', request('role'));
        }
        
        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $users = $query->latest()->paginate(15);
        return view('super-admin.users', compact('users'));
    }

    public function pendingLandlords()
    {
        $pendingLandlords = User::pendingLandlords()->with(['approvedBy', 'landlordDocuments'])->latest()->paginate(15);
        return view('super-admin.pending-landlords', compact('pendingLandlords'));
    }

    public function approveLandlord($id)
    {
        $landlord = User::findOrFail($id);
        
        if ($landlord->role !== 'landlord') {
            return back()->with('error', 'User is not a landlord.');
        }

        $landlord->approve(Auth::id());

        return back()->with('success', 'Landlord approved successfully.');
    }

    public function reviewLandlordDocuments($id)
    {
        $landlord = User::where('role', 'landlord')->findOrFail($id);
        $documents = $landlord->landlordDocuments()->latest()->get();
        return view('super-admin.review-landlord-docs', compact('landlord', 'documents'));
    }

    public function verifyLandlordDocument(Request $request, $docId)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $doc = LandlordDocument::findOrFail($docId);
        $doc->update([
            'verification_status' => $request->status,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'verification_notes' => $request->notes,
        ]);

        return back()->with('success', 'Document updated.');
    }

    public function rejectLandlord(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $landlord = User::findOrFail($id);
        
        if ($landlord->role !== 'landlord') {
            return back()->with('error', 'User is not a landlord.');
        }

        $landlord->reject(Auth::id(), $request->reason);

        return back()->with('success', 'Landlord rejected successfully.');
    }

    public function createUser()
    {
        return view('super-admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,landlord,tenant',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'business_info' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'business_info' => $request->business_info,
            'status' => $request->role === 'landlord' ? 'pending' : 'active',
        ]);

        // Create role-specific profile
        switch ($request->role) {
            case 'landlord':
                LandlordProfile::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'business_info' => $request->business_info,
                ]);
                if ($request->approve_immediately) {
                    $user->approve(Auth::id());
                }
                break;
            case 'tenant':
                TenantProfile::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
                break;
            case 'staff':
                StaffProfile::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'staff_type' => $request->staff_type,
                ]);
                break;
            case 'super_admin':
                SuperAdminProfile::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'address' => $request->address,
                ]);
                break;
        }

        return redirect()->route('super-admin.users')->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('super-admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,landlord,tenant',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'business_info' => 'nullable|string|max:1000',
        ]);

        $user->update($request->only([
            'name', 'email', 'role', 'phone', 'address', 'business_info'
        ]));

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('super-admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        
        return back()->with('success', 'User deleted successfully.');
    }

    public function apartments()
    {
        $query = Apartment::with('landlord', 'units');
        
        // Search by apartment name or address
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        // Filter by landlord
        if (request('landlord')) {
            $query->where('landlord_id', request('landlord'));
        }
        
        $apartments = $query->latest()->paginate(15);
        return view('super-admin.apartments', compact('apartments'));
    }
}
