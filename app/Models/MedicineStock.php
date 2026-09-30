<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineStock extends Model
{
    use HasFactory;

    protected $table = 'medicine_stock';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $primaryKey = 'medicine_id';

    protected $fillable = [
        'medicine_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    /**
     * Get the medicine this stock belongs to.
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Check if stock is low based on reorder level.
     */
    public function isLow(): bool
    {
        return $this->qty <= $this->medicine->reorder_level;
    }
}
