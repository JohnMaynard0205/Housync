<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'type',
        'price',
        'address',
        'city',
        'state',
        'zip_code',
        'bedrooms',
        'bathrooms',
        'area',
        'image_path',
        'availability_status',
        'available_from',
        'available_to',
        'landlord_id',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'available_from' => 'date',
        'available_to' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    /**
     * Relationships
     */
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Accessors
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path && file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }
        
        // Return placeholder image
        return asset('images/placeholder-property.jpg');
    }

    /**
     * Query Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    public function scopeFilterByType($query, $type)
    {
        if (!empty($type)) {
            return $query->where('type', $type);
        }
        return $query;
    }

    public function scopeFilterByAvailability($query, $availability)
    {
        if (!empty($availability)) {
            return $query->where('availability_status', $availability);
        }
        return $query;
    }

    public function scopeFilterByDateRange($query, $from, $to)
    {
        if (!empty($from) && !empty($to)) {
            return $query->whereBetween('available_from', [$from, $to])
                         ->orWhereBetween('available_to', [$from, $to]);
        } elseif (!empty($from)) {
            return $query->where('available_from', '>=', $from);
        } elseif (!empty($to)) {
            return $query->where('available_to', '<=', $to);
        }
        return $query;
    }

    public function scopeFilterByAmenities($query, $amenityIds)
    {
        if (!empty($amenityIds) && is_array($amenityIds)) {
            return $query->whereHas('amenities', function ($q) use ($amenityIds) {
                $q->whereIn('amenities.id', $amenityIds);
            }, '=', count($amenityIds));
        }
        return $query;
    }

    public function scopeFilterByPriceRange($query, $minPrice, $maxPrice)
    {
        if (!empty($minPrice) && !empty($maxPrice)) {
            return $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif (!empty($minPrice)) {
            return $query->where('price', '>=', $minPrice);
        } elseif (!empty($maxPrice)) {
            return $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });

        static::updating(function ($property) {
            if ($property->isDirty('title') && empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });
    }
}

