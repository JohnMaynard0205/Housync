<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $card_uid
 * @property int|null $rfid_card_id
 * @property int|null $tenant_assignment_id
 * @property int|null $apartment_id
 * @property string $access_result
 * @property string|null $denial_reason
 * @property \Carbon\Carbon $access_time
 * @property string $reader_location
 * @property array|null $raw_data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_uid',
        'rfid_card_id',
        'tenant_assignment_id',
        'apartment_id',
        'access_result',
        'denial_reason',
        'access_time',
        'reader_location',
        'raw_data',
    ];

    protected $casts = [
        'access_time' => 'datetime',
        'raw_data' => 'array',
    ];

    // Relationships
    public function rfidCard()
    {
        return $this->belongsTo(RfidCard::class);
    }

    public function tenantAssignment()
    {
        return $this->belongsTo(TenantAssignment::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    // Scopes
    public function scopeGranted($query)
    {
        return $query->where('access_result', 'granted');
    }

    public function scopeDenied($query)
    {
        return $query->where('access_result', 'denied');
    }

    public function scopeForApartment($query, $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId);
    }

    public function scopeForCard($query, $cardUid)
    {
        return $query->where('card_uid', $cardUid);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('access_time', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('access_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('access_time', now()->month)
                    ->whereYear('access_time', now()->year);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('access_time', [$startDate, $endDate]);
    }

    public function scopeRecentActivity($query, $hours = 24)
    {
        return $query->where('access_time', '>=', now()->subHours($hours));
    }

    // Helper methods
    public function isGranted()
    {
        return $this->access_result === 'granted';
    }

    public function isDenied()
    {
        return $this->access_result === 'denied';
    }

    public function getResultBadgeClassAttribute()
    {
        return match($this->access_result) {
            'granted' => 'success',
            'denied' => 'danger',
            default => 'secondary'
        };
    }

    public function getDenialReasonDisplayAttribute()
    {
        return match($this->denial_reason) {
            'card_not_found' => 'Card not registered',
            'card_inactive' => 'Card deactivated',
            'card_expired' => 'Card expired',
            'tenant_inactive' => 'Tenant access revoked',
            'outside_access_hours' => 'Outside allowed hours',
            'card_stolen' => 'Card reported stolen',
            'card_lost' => 'Card reported lost',
            default => $this->denial_reason
        };
    }

    public function getTenantNameAttribute()
    {
        return $this->tenantAssignment?->tenant?->name ?? 'Unknown';
    }

    public function getApartmentNameAttribute()
    {
        return $this->apartment?->name ?? 'Unknown';
    }

    // Static methods for statistics
    public static function getAccessStats($apartmentId = null, $days = 30)
    {
        $query = static::query();
        
        if ($apartmentId) {
            $query->where('apartment_id', $apartmentId);
        }
        
        $query->where('access_time', '>=', now()->subDays($days));
        
        return [
            'total_attempts' => $query->count(),
            'granted' => $query->where('access_result', 'granted')->count(),
            'denied' => $query->where('access_result', 'denied')->count(),
            'unique_cards' => $query->distinct('card_uid')->count(),
        ];
    }

    public static function getRecentActivity($apartmentId = null, $limit = 10)
    {
        $query = static::with(['rfidCard', 'tenantAssignment.tenant', 'apartment'])
                      ->orderBy('access_time', 'desc');
        
        if ($apartmentId) {
            $query->where('apartment_id', $apartmentId);
        }
        
        return $query->limit($limit)->get();
    }

    public static function getDeniedAccessReasons($apartmentId = null, $days = 30)
    {
        $query = static::where('access_result', 'denied')
                      ->where('access_time', '>=', now()->subDays($days));
        
        if ($apartmentId) {
            $query->where('apartment_id', $apartmentId);
        }
        
        return $query->groupBy('denial_reason')
                    ->selectRaw('denial_reason, count(*) as count')
                    ->orderBy('count', 'desc')
                    ->get();
    }
}