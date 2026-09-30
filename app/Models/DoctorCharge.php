<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_profile_id',
        'title',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the doctor profile this charge belongs to.
     */
    public function doctorProfile(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    /**
     * Get the associated doctor user.
     */
    public function doctor()
    {
        return $this->doctorProfile->user;
    }
}
