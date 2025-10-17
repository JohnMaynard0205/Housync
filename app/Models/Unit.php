<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $unit_number
 * @property int $apartment_id
 * @property string $unit_type
 * @property float $rent_amount
 * @property string $status
 * @property string $leasing_type
 * @property int $tenant_count
 * @property int|null $max_occupants
 * @property int|null $floor_number
 * @property string|null $description
 * @property float|null $floor_area
 * @property int $bedrooms
 * @property int $bathrooms
 * @property bool $is_furnished
 * @property array|null $amenities
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_number',
        'apartment_id',
        'unit_type',
        'rent_amount',
        'status',
        'leasing_type',
        'tenant_count',
        'max_occupants',
        'floor_number',
        'description',
        'floor_area',
        'bedrooms',
        'bathrooms',
        'is_furnished',
        'amenities',
        'notes',
        'cover_image',
        'gallery',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'floor_area' => 'decimal:2',
        'is_furnished' => 'boolean',
        'amenities' => 'array',
        'gallery' => 'array',
        'tenant_count' => 'integer',
        'max_occupants' => 'integer',
        'floor_number' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
    ];

    protected $appends = ['cover_image_url', 'gallery_urls'];

    // Accessors
    public function getCoverImageUrlAttribute()
    {
        if (empty($this->cover_image)) {
            return null;
        }

        // If already starts with http/https, return as is
        if (str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }

        // Return the API URL with storage path for Railway
        return url('api/storage/' . $this->cover_image);
    }

    public function getGalleryUrlsAttribute()
    {
        if (empty($this->gallery) || !is_array($this->gallery)) {
            return [];
        }

        return array_map(function ($path) {
            // If already starts with http/https, return as is
            if (str_starts_with($path, 'http')) {
                return $path;
            }
            // Return the API URL with storage path for Railway
            return url('api/storage/' . $path);
        }, $this->gallery);
    }

    // Relationships
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function tenantAssignment()
    {
        return $this->hasOne(TenantAssignment::class);
    }

    public function tenantAssignments()
    {
        return $this->hasMany(TenantAssignment::class);
    }

    public function currentTenant()
    {
        return $this->hasOneThrough(User::class, TenantAssignment::class, 'unit_id', 'id', 'id', 'tenant_id');
    }

    // Helper method to get landlord through apartment
    public function getLandlord()
    {
        return $this->apartment ? $this->apartment->landlord : null;
    }

    // Scopes for filtering
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('unit_type', $type);
    }

    public function scopeRentRange($query, $min, $max)
    {
        return $query->whereBetween('rent_amount', [$min, $max]);
    }

    // Helper methods
    public function getFormattedRentAttribute()
    {
        return '₱' . number_format($this->rent_amount, 2);
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'available';
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'occupied' => 'occupied',
            'available' => 'available',
            'maintenance' => 'maintenance',
            default => 'available'
        };
    }

    // Leasing type helper methods
    public function getLeasingTypeLabelAttribute()
    {
        return match($this->leasing_type) {
            'separate' => 'Separate Bills',
            'inclusive' => 'All Inclusive',
            default => 'Separate Bills'
        };
    }

    public function getLeasingTypeDescriptionAttribute()
    {
        return match($this->leasing_type) {
            'separate' => 'Tenant pays rent + utilities separately',
            'inclusive' => 'Rent includes all utilities and bills',
            default => 'Tenant pays rent + utilities separately'
        };
    }

    public function isInclusiveLeasing()
    {
        return $this->leasing_type === 'inclusive';
    }

    public function isSeparateLeasing()
    {
        return $this->leasing_type === 'separate';
    }
}
