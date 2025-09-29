<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $status
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $business_info
 * @property \Carbon\Carbon|null $approved_at
 * @property int|null $approved_by
 * @property string|null $rejection_reason
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'staff_type',
        'status',
        'phone',
        'address',
        'business_info',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'landlord_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    public function rfidCards()
    {
        return $this->hasMany(RfidCard::class, 'landlord_id');
    }

    public function landlordDocuments()
    {
        return $this->hasMany(LandlordDocument::class, 'landlord_id');
    }

    // Profiles
    public function landlordProfile()
    {
        return $this->hasOne(LandlordProfile::class);
    }

    public function tenantProfile()
    {
        return $this->hasOne(TenantProfile::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function superAdminProfile()
    {
        return $this->hasOne(SuperAdminProfile::class);
    }

    // Tenant assignments
    public function tenantAssignments()
    {
        return $this->hasMany(TenantAssignment::class, 'tenant_id');
    }

    public function landlordAssignments()
    {
        return $this->hasMany(TenantAssignment::class, 'landlord_id');
    }

    // Staff assignments
    public function staffAssignments()
    {
        return $this->hasMany(StaffAssignment::class, 'staff_id');
    }

    public function landlordStaffAssignments()
    {
        return $this->hasMany(StaffAssignment::class, 'landlord_id');
    }

    public function verifiedDocuments()
    {
        return $this->hasMany(TenantDocument::class, 'verified_by');
    }

    // Role helper methods
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    public function isTenant()
    {
        return $this->role === 'tenant';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    // Status helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    // Scopes
    public function scopePendingLandlords($query)
    {
        return $query->where('role', 'landlord')->where('status', 'pending');
    }

    public function scopeApprovedLandlords($query)
    {
        return $query->where('role', 'landlord')->where('status', 'approved');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function approve($adminId)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);
    }

    public function reject($adminId, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_at' => null,
            'approved_by' => $adminId,
            'rejection_reason' => $reason,
        ]);
    }
}
