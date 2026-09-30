<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'license_number',
        'phone',
        'clinic_name',
        'clinic_logo',
        'default_doctor_fee',
    ];

    protected $casts = [
        'default_doctor_fee' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     * NOTE: clinic_logo_url is intentionally NOT appended here because asset()
     * requires an HTTP request context and crashes in queue/console/tests.
     * Access it explicitly via $model->clinic_logo_url when needed.
     */
    protected $appends = [];

    /**
     * Get the user this profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all charges for this doctor.
     */
    public function charges(): HasMany
    {
        return $this->hasMany(DoctorCharge::class);
    }

    /**
     * Get active charges for this doctor.
     */
    public function activeCharges(): HasMany
    {
        return $this->charges()->where('is_active', true);
    }

    /**
     * Get the clinic logo URL.
     * Safe to call in any context (web, console, queue).
     */
    public function getClinicLogoUrl(): ?string
    {
        if (!$this->clinic_logo) {
            return null;
        }
        try {
            return asset('storage/' . $this->clinic_logo);
        } catch (\Throwable $e) {
            return '/storage/' . $this->clinic_logo;
        }
    }

    /**
     * Get the clinic logo URL attribute.
     */
    public function getClinicLogoUrlAttribute(): ?string
    {
        return $this->getClinicLogoUrl();
    }
}
