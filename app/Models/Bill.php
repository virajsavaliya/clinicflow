<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'medicine_total',
        'doctor_charges_total',
        'taxes',
        'total_amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'medicine_total' => 'decimal:2',
        'doctor_charges_total' => 'decimal:2',
        'taxes' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Get the visit for this bill.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get all items in this bill.
     */
    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Get medicine items.
     */
    public function medicineItems(): HasMany
    {
        return $this->items()->where('item_type', 'medicine');
    }

    /**
     * Get doctor charge items.
     */
    public function chargeItems(): HasMany
    {
        return $this->items()->where('item_type', 'doctor_charge');
    }

    /**
     * Check if bill is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if bill is unpaid.
     */
    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    /**
     * Check if bill is partially paid.
     */
    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }

    /**
     * Get the balance due.
     */
    public function getBalanceDue(): float
    {
        return $this->total_amount - $this->paid_amount;
    }
}
