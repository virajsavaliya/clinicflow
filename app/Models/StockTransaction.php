<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'change_qty',
        'type',
        'reference',
        'user_id',
    ];

    protected $casts = [
        'change_qty' => 'integer',
    ];

    /**
     * Get the medicine for this transaction.
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the user who created this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is a purchase transaction.
     */
    public function isPurchase(): bool
    {
        return $this->type === 'purchase';
    }

    /**
     * Check if this is a prescription transaction.
     */
    public function isPrescription(): bool
    {
        return $this->type === 'prescription';
    }
}
