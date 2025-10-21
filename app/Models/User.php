<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $role
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * Delegated to Profile:
 * @property string $name (via profile)
 * @property string $status (via profile)
 * @property string|null $phone (via profile)
 * @property string|null $address (via profile)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Note: name, phone, address, etc. are now in profile tables
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    /**
     * Eager load profile relationship based on role
     */
    protected $with = [];
    
    /**
     * Boot the model and auto-load appropriate profile
     */
    protected static function booted()
    {
        static::retrieved(function ($user) {
            $user->load($user->getProfileRelation());
        });
    }

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

    /**
     * Get the profile relation name based on role
     */
    public function getProfileRelation()
    {
        return match($this->role) {
            'super_admin' => 'superAdminProfile',
            'landlord' => 'landlordProfile',
            'tenant' => 'tenantProfile',
            'staff' => 'staffProfile',
            default => null,
        };
    }

    /**
     * Get the profile instance based on role
     */
    public function profile()
    {
        return match($this->role) {
            'super_admin' => $this->superAdminProfile,
            'landlord' => $this->landlordProfile,
            'tenant' => $this->tenantProfile,
            'staff' => $this->staffProfile,
            default => null,
        };
    }

    // Accessors - Delegate to Profile
    public function getNameAttribute($value)
    {
        return $this->profile()?->name ?? $value ?? 'Unknown';
    }

    public function getPhoneAttribute($value)
    {
        return $this->profile()?->phone ?? $value;
    }

    public function getAddressAttribute($value)
    {
        return $this->profile()?->address ?? $value;
    }

    public function getStatusAttribute($value)
    {
        return $this->profile()?->status ?? $value ?? 'active';
    }

    // Landlord-specific accessors
    public function getBusinessInfoAttribute($value)
    {
        if ($this->isLandlord()) {
            return $this->landlordProfile?->business_info ?? $value;
        }
        return $value;
    }

    public function getApprovedAtAttribute($value)
    {
        if ($this->isLandlord()) {
            return $this->landlordProfile?->approved_at ?? $value;
        }
        return $value;
    }

    public function getApprovedByAttribute($value)
    {
        if ($this->isLandlord()) {
            return $this->landlordProfile?->approved_by ?? $value;
        }
        return $value;
    }

    public function getRejectionReasonAttribute($value)
    {
        if ($this->isLandlord()) {
            return $this->landlordProfile?->rejection_reason ?? $value;
        }
        return $value;
    }

    // Staff-specific accessor
    public function getStaffTypeAttribute($value)
    {
        if ($this->isStaff()) {
            return $this->staffProfile?->staff_type ?? $value;
        }
        return $value;
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
        return $query->where('role', 'landlord')
            ->whereHas('landlordProfile', function($q) {
                $q->where('status', 'pending');
            });
    }

    public function scopeApprovedLandlords($query)
    {
        return $query->where('role', 'landlord')
            ->whereHas('landlordProfile', function($q) {
                $q->where('status', 'approved');
            });
    }

    public function scopeRejectedLandlords($query)
    {
        return $query->where('role', 'landlord')
            ->whereHas('landlordProfile', function($q) {
                $q->where('status', 'rejected');
            });
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods - Now update profiles instead of users table
    public function approve($adminId)
    {
        if ($this->isLandlord() && $this->landlordProfile) {
            $this->landlordProfile->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $adminId,
                'rejection_reason' => null,
            ]);
        }
    }

    public function reject($adminId, $reason = null)
    {
        if ($this->isLandlord() && $this->landlordProfile) {
            $this->landlordProfile->update([
                'status' => 'rejected',
                'approved_at' => null,
                'approved_by' => $adminId,
                'rejection_reason' => $reason,
            ]);
        }
    }
    
    /**
     * Create or update profile when user is created/updated
     */
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            $user->createProfileIfNeeded();
        });
    }
    
    /**
     * Create profile if it doesn't exist
     */
    public function createProfileIfNeeded()
    {
        $profileClass = match($this->role) {
            'super_admin' => SuperAdminProfile::class,
            'landlord' => LandlordProfile::class,
            'tenant' => TenantProfile::class,
            'staff' => StaffProfile::class,
            default => null,
        };
        
        if ($profileClass && !$this->profile()) {
            $profileClass::create([
                'user_id' => $this->id,
                'name' => 'New User', // Default, will be updated
                'status' => $this->role === 'landlord' ? 'pending' : 'active',
            ]);
        }
    }
}

