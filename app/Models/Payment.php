<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $bill_id
 * @property int|null $tenant_id
 * @property float $amount
 * @property \Carbon\Carbon $paid_at
 * @property string $method
 * @property string|null $reference_number
 * @property string|null $proof_path
 * @property string $status
 * @property string|null $notes
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'tenant_id',
        'amount',
        'paid_at',
        'method',
        'reference_number',
        'proof_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // Helpers
    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }
}



