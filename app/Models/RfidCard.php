<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $card_uid
 * @property int|null $tenant_assignment_id
 * @property int $landlord_id
 * @property int $apartment_id
 * @property string|null $card_name
 * @property string $status
 * @property \Carbon\Carbon $issued_at
 * @property \Carbon\Carbon|null $expires_at
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class RfidCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_uid',
        'tenant_assignment_id',
        'landlord_id',
        'apartment_id',
        'card_name',
        'status',
        'issued_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function tenantAssignment()
    {
        return $this->belongsTo(TenantAssignment::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    //--
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeCompromised($query)
    {
        return $query->whereIn('status', ['lost', 'stolen']);
    }

    public function scopeForApartment($query, $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId);
    }

    public function scopeForLandlord($query, $landlordId)
    {
        return $query->where('landlord_id', $landlordId);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active' && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
//--
    public function isCompromised()
    {
        return in_array($this->status, ['lost', 'stolen']);
    }
//--
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => $this->isExpired() ? 'warning' : 'success',
            'inactive' => 'secondary',
            'lost' => 'warning',
            'stolen' => 'danger',
            default => 'secondary'
        };
    }

    public function getDisplayStatusAttribute()
    {
        if ($this->status === 'active' && $this->isExpired()) {
            return 'expired';
        }
        return $this->status;
    }

    // Check if card can grant access
    public function canGrantAccess()
    {
        if (!$this->isActive()) {
            return false;
        }

        if (!$this->tenant_assignment_id) {
            return false;
        }

        $assignment = $this->tenantAssignment;
        if (!$assignment || !$assignment->isActive()) {
            return false;
        }

        return true;
    }

    // Get access denial reason
    public function getAccessDenialReason()
    {
        if ($this->isCompromised()) {
            return 'card_' . $this->status;
        }

        if ($this->status !== 'active') {
            return 'card_inactive';
        }

        if ($this->isExpired()) {
            return 'card_expired';
        }

        if (!$this->tenant_assignment_id) {
            return 'card_not_assigned';
        }

        $assignment = $this->tenantAssignment;
        if (!$assignment || !$assignment->isActive()) {
            return 'tenant_inactive';
        }

        return null;
    }
}