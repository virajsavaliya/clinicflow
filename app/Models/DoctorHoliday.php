<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'date',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the doctor for this holiday.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
