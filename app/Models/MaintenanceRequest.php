<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $unit_id
 * @property int $tenant_id
 * @property int $landlord_id
 * @property string $title
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property string $category
 * @property \Carbon\Carbon $requested_date
 * @property \Carbon\Carbon|null $completed_date
 * @property int|null $assigned_staff_id
 * @property string|null $staff_notes
 * @property string|null $tenant_notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'tenant_id',
        'landlord_id',
        'title',
        'description',
        'priority',
        'status',
        'category',
        'requested_date',
        'completed_date',
        'assigned_staff_id',
        'staff_notes',
        'tenant_notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'completed_date' => 'date',
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

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    // Scopes
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeAssignedToStaff($query, $staffId)
    {
        return $query->where('assigned_staff_id', $staffId);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAssigned()
    {
        return $this->status === 'assigned';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'assigned' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'plumbing' => 'mdi-water',
            'electrical' => 'mdi-lightning-bolt',
            'hvac' => 'mdi-air-conditioner',
            'appliance' => 'mdi-washing-machine',
            'structural' => 'mdi-home',
            'cleaning' => 'mdi-broom',
            default => 'mdi-tools'
        };
    }
} 