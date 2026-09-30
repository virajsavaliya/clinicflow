<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'item_type',
        'reference_id',
        'title',
        'qty',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the bill this item belongs to.
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Check if this is a medicine item.
     */
    public function isMedicine(): bool
    {
        return $this->item_type === 'medicine';
    }

    /**
     * Check if this is a doctor charge item.
     */
    public function isDoctorCharge(): bool
    {
        return $this->item_type === 'doctor_charge';
    }
}
