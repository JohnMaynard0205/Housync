<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    /**
     * Landlord billing overview (Payments page in landlord portal)
     */
    public function landlordIndex(Request $request)
    {
        $landlordId = Auth::id();

        // Basic filters (status / type) kept simple to avoid errors
        $status = $request->query('status');
        $type = $request->query('type');

        $query = Bill::forLandlord($landlordId)->with(['tenant', 'unit']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $bills = $query->orderByDesc('due_date')->orderByDesc('created_at')->paginate(10);

        // Simple summary cards (use null coalescing to avoid errors on empty data)
        $summary = [
            'total_amount'      => Bill::forLandlord($landlordId)->sum('amount') ?? 0,
            'total_collected'   => Bill::forLandlord($landlordId)->sum('amount_paid') ?? 0,
            'total_outstanding' => Bill::forLandlord($landlordId)->sum('balance') ?? 0,
            'pending_count'     => Bill::forLandlord($landlordId)->where('status', 'unpaid')->orWhere('status', 'partially_paid')->count(),
        ];

        return view('landlord.billing', compact('bills', 'summary', 'status', 'type'));
    }

    /**
     * Tenant payments view (Payments page in tenant portal)
     */
    public function tenantIndex()
    {
        $tenantId = Auth::id();

        $bills = Bill::forTenant($tenantId)
            ->with(['unit', 'landlord'])
            ->orderByDesc('due_date')
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'total_due'       => $bills->sum('balance'),
            'total_paid'      => $bills->sum('amount_paid'),
            'upcoming_count'  => $bills->where('status', 'unpaid')->count(),
            'overdue_count'   => $bills->where('status', 'overdue')->count(),
        ];

        return view('tenant.payments', compact('bills', 'summary'));
    }
}



