<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $unit_id
 * @property int $tenant_id
 * @property int $landlord_id
 * @property \Carbon\Carbon $assigned_at
 * @property \Carbon\Carbon $lease_start_date
 * @property \Carbon\Carbon $lease_end_date
 * @property float $rent_amount
 * @property float $security_deposit
 * @property string $status
 * @property string|null $notes
 * @property bool $documents_uploaded
 * @property bool $documents_verified
 * @property string|null $verification_notes
 * @property string|null $generated_password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class TenantAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'landlord_id',
        'assigned_at',
        'lease_start_date',
        'lease_end_date',
        'rent_amount',
        'security_deposit',
        'status',
        'notes',
        'occupation',
        'monthly_income',
        'documents_uploaded',
        'documents_verified',
        'verification_notes',
        'generated_password',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'lease_start_date' => 'date',
        'lease_end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'monthly_income' => 'decimal:2',
        'documents_uploaded' => 'boolean',
        'documents_verified' => 'boolean',
    ];

    // Relationships
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function documents()
    {
        return $this->hasMany(TenantDocument::class);
    }

    public function rfidCards()
    {
        return $this->hasManyThrough(
            RfidCard::class,
            TenantRfidAssignment::class,
            'tenant_assignment_id', // Foreign key on tenant_rfid_assignments table
            'id', // Foreign key on rfid_cards table
            'id', // Local key on tenant_assignments table
            'rfid_card_id' // Local key on tenant_rfid_assignments table
        );
    }

    public function tenantRfidAssignments()
    {
        return $this->hasMany(TenantRfidAssignment::class);
    }

    public function activeRfidCards()
    {
        return $this->rfidCards()->whereHas('tenantRfidAssignments', function($query) {
            $query->where('tenant_assignment_id', $this->id)
                  ->where('status', 'active');
        });
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePendingDocuments($query)
    {
        return $query->where('documents_uploaded', false);
    }

    public function scopeDocumentsUploaded($query)
    {
        return $query->where('documents_uploaded', true);
    }

    public function scopeDocumentsVerified($query)
    {
        return $query->where('documents_verified', true);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isTerminated()
    {
        return $this->status === 'terminated';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'pending' => 'warning',
            'terminated' => 'danger',
            default => 'secondary'
        };
    }

    public function getDocumentsStatusAttribute()
    {
        // Check if there are any documents uploaded
        $hasDocuments = $this->documents()->count() > 0;
        
        if (!$hasDocuments) {
            return 'pending';
        }
        
        // Check if all documents are verified
        $pendingDocuments = $this->documents()->where('verification_status', 'pending')->count();
        $verifiedDocuments = $this->documents()->where('verification_status', 'verified')->count();
        $totalDocuments = $this->documents()->count();
        
        if ($pendingDocuments > 0) {
            return 'uploaded'; // Some documents are pending verification
        }
        
        if ($verifiedDocuments === $totalDocuments && $totalDocuments > 0) {
            return 'verified'; // All documents are verified
        }
        
        return 'uploaded'; // Default fallback
    }

    public function getDocumentsStatusBadgeClassAttribute()
    {
        return match($this->getDocumentsStatusAttribute()) {
            'pending' => 'danger',
            'uploaded' => 'warning',
            'verified' => 'success',
            default => 'secondary'
        };
    }
} 