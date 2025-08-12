<?php

namespace App\Http\Controllers;

use App\Models\RfidCard;
use App\Models\AccessLog;
use App\Models\TenantAssignment;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RfidController extends Controller
{
    /**
     * Display RFID management dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $apartmentId = $request->get('apartment_id');
        
        // Get landlord's apartments
        $apartments = $user->apartments;
        
        // If no specific apartment selected, use the first one
        if (!$apartmentId && $apartments->count() > 0) {
            $apartmentId = $apartments->first()->id;
        }
        
        // Get RFID cards for the selected apartment
        $cards = RfidCard::with(['tenantAssignment.tenant', 'apartment'])
                         ->forLandlord($user->id);
        
        if ($apartmentId) {
            $cards = $cards->forApartment($apartmentId);
        }
        
        $cards = $cards->orderBy('created_at', 'desc')->paginate(10);
        
        // Get recent access logs
        $recentLogs = AccessLog::with(['rfidCard', 'tenantAssignment.tenant', 'apartment'])
                              ->when($apartmentId, function($query) use ($apartmentId) {
                                  return $query->where('apartment_id', $apartmentId);
                              })
                              ->orderBy('access_time', 'desc')
                              ->limit(10)
                              ->get();
        
        // Get access statistics
        $stats = AccessLog::getAccessStats($apartmentId, 30);
        
        return view('landlord.security.index', compact(
            'cards', 'apartments', 'apartmentId', 'recentLogs', 'stats'
        ));
    }
    
    /**
     * Show form to assign a new RFID card
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $apartmentId = $request->get('apartment_id');
        
        // Get landlord's apartments
        $apartments = $user->apartments;
        
        // Get active tenant assignments for the apartment
        $tenantAssignments = TenantAssignment::with(['tenant', 'unit'])
                                           ->where('landlord_id', $user->id)
                                           ->when($apartmentId, function($query) use ($apartmentId) {
                                               return $query->whereHas('unit', function($q) use ($apartmentId) {
                                                   $q->where('apartment_id', $apartmentId);
                                               });
                                           })
                                           ->active()
                                           ->get();
        
        return view('landlord.security.create', compact(
            'apartments', 'apartmentId', 'tenantAssignments'
        ));
    }
    
    /**
     * Store a new RFID card assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'card_uid' => 'required|string|max:255|unique:rfid_cards',
            'tenant_assignment_id' => 'required|exists:tenant_assignments,id',
            'apartment_id' => 'required|exists:apartments,id',
            'card_name' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $user = Auth::user();
        
        // Verify the tenant assignment belongs to this landlord
        $tenantAssignment = TenantAssignment::where('id', $request->tenant_assignment_id)
                                          ->where('landlord_id', $user->id)
                                          ->first();
        
        if (!$tenantAssignment) {
            return back()->withErrors(['tenant_assignment_id' => 'Invalid tenant assignment.']);
        }
        
        // Verify the apartment belongs to this landlord
        $apartment = $user->apartments()->find($request->apartment_id);
        if (!$apartment) {
            return back()->withErrors(['apartment_id' => 'Invalid apartment.']);
        }
        
        try {
            $rfidCard = RfidCard::create([
                'card_uid' => strtoupper($request->card_uid),
                'tenant_assignment_id' => $request->tenant_assignment_id,
                'landlord_id' => $user->id,
                'apartment_id' => $request->apartment_id,
                'card_name' => $request->card_name,
                'status' => 'active',
                'issued_at' => now(),
                'expires_at' => $request->expires_at,
                'notes' => $request->notes,
            ]);
            
            return redirect()->route('landlord.security.index', ['apartment_id' => $request->apartment_id])
                           ->with('success', 'RFID card assigned successfully!');
                           
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to assign RFID card: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show RFID card details
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $card = RfidCard::with(['tenantAssignment.tenant', 'apartment'])
                       ->where('landlord_id', $user->id)
                       ->findOrFail($id);
        
        // Get access logs for this card
        $accessLogs = AccessLog::with(['apartment'])
                             ->where('rfid_card_id', $id)
                             ->orderBy('access_time', 'desc')
                             ->paginate(15);
        
        return view('landlord.security.show', compact('card', 'accessLogs'));
    }
    
    /**
     * Show access logs
     */
    public function accessLogs(Request $request)
    {
        $user = Auth::user();
        $apartmentId = $request->get('apartment_id');
        $cardUid = $request->get('card_uid');
        $result = $request->get('result');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Get landlord's apartments
        $apartments = $user->apartments;
        
        // Build query
        $query = AccessLog::with(['rfidCard', 'tenantAssignment.tenant', 'apartment'])
                         ->whereIn('apartment_id', $apartments->pluck('id'));
        
        // Apply filters
        if ($apartmentId) {
            $query->where('apartment_id', $apartmentId);
        }
        
        if ($cardUid) {
            $query->where('card_uid', 'like', "%{$cardUid}%");
        }
        
        if ($result) {
            $query->where('access_result', $result);
        }
        
        if ($dateFrom) {
            $query->whereDate('access_time', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('access_time', '<=', $dateTo);
        }
        
        $logs = $query->orderBy('access_time', 'desc')->paginate(20);
        
        // Get denied access reasons for stats
        $deniedReasons = AccessLog::getDeniedAccessReasons($apartmentId);
        
        return view('landlord.security.access-logs', compact(
            'logs', 'apartments', 'apartmentId', 'cardUid', 'result', 
            'dateFrom', 'dateTo', 'deniedReasons'
        ));
    }
    
    /**
     * Deactivate/reactivate RFID card
     */
    public function toggleStatus($id)
    {
        $user = Auth::user();
        
        $card = RfidCard::where('landlord_id', $user->id)->findOrFail($id);
        
        $newStatus = $card->status === 'active' ? 'inactive' : 'active';
        $card->update(['status' => $newStatus]);
        
        $action = $newStatus === 'active' ? 'activated' : 'deactivated';
        
        return back()->with('success', "RFID card {$action} successfully!");
    }
    
    /**
     * API endpoint for ESP32 to verify card access
     */
    public function verifyAccess(Request $request)
    {
        $cardUid = strtoupper($request->input('card_uid'));
        
        if (!$cardUid) {
            return response()->json(['error' => 'Card UID required'], 400);
        }
        
        $rfidCard = RfidCard::with(['tenantAssignment.tenant'])->where('card_uid', $cardUid)->first();
        
        $result = [
            'card_uid' => $cardUid,
            'access_granted' => false,
            'tenant_name' => null,
            'denial_reason' => null,
            'timestamp' => now()->toISOString()
        ];
        
        if (!$rfidCard) {
            $result['denial_reason'] = 'card_not_found';
        } elseif ($rfidCard->canGrantAccess()) {
            $result['access_granted'] = true;
            $result['tenant_name'] = $rfidCard->tenantAssignment->tenant->name;
        } else {
            $result['denial_reason'] = $rfidCard->getAccessDenialReason();
        }
        
        // Log the access attempt
        AccessLog::create([
            'card_uid' => $cardUid,
            'rfid_card_id' => $rfidCard?->id,
            'tenant_assignment_id' => $rfidCard?->tenant_assignment_id,
            'apartment_id' => $rfidCard?->apartment_id,
            'access_result' => $result['access_granted'] ? 'granted' : 'denied',
            'denial_reason' => $result['denial_reason'],
            'access_time' => now(),
            'reader_location' => $request->input('reader_location', 'main_entrance'),
            'raw_data' => $request->all()
        ]);
        
        return response()->json($result);
    }
}
