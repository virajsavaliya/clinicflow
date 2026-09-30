<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'barcode',
        'unit_price',
        'reorder_level',
        'generic_name',
        'type',
        'dosage',
        'quantity',
        'description',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'reorder_level' => 'integer',
    ];

    /**
     * Get the stock for this medicine.
     */
    public function stock(): HasOne
    {
        return $this->hasOne(MedicineStock::class);
    }

    /**
     * Get all stock transactions for this medicine.
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Get the current stock quantity.
     */
    public function currentStock(): int
    {
        return $this->stock?->qty ?? 0;
    }

    /**
     * Check if medicine is low on stock.
     */
    public function isLowOnStock(): bool
    {
        return $this->currentStock() <= $this->reorder_level;
    }

    /**
     * Get medicine stock with a fallback.
     */
    public function getOrCreateStock()
    {
        return $this->stock()->firstOrCreate(['medicine_id' => $this->id], ['qty' => 0]);
    }
}
