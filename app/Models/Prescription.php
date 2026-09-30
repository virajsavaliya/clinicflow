<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
    ];

    /**
     * Get the visit for this prescription.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get all items in this prescription.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /**
     * Get the total medicine cost for this prescription.
     */
    public function getTotalCost(): float
    {
        return $this->items()->sum('subtotal');
    }

    /**
     * Get the total quantity of medicines in this prescription.
     */
    public function getTotalQuantity(): int
    {
        return $this->items()->sum('qty');
    }
}
