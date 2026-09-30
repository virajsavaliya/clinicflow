<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'birthdate',
        'age',
        'gender',
        'address',
    ];

    protected $casts = [
        'age' => 'integer',
        'birthdate' => 'date',
    ];

    protected $appends = ['calculated_age'];

    /**
     * Calculate age from birthdate.
     * Returns the calculated age based on birthdate, or the stored age if no birthdate.
     */
    public function getCalculatedAgeAttribute(): ?int
    {
        if ($this->birthdate) {
            return Carbon::parse($this->birthdate)->age;
        }
        return $this->age;
    }

    /**
     * Automatically update age when birthdate is set.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($patient) {
            if ($patient->birthdate) {
                $patient->age = Carbon::parse($patient->birthdate)->age;
            }
        });
    }

    /**
     * Get all visits for this patient.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get all appointments for this patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(\App\Models\Appointment::class);
    }

    /**
     * Get the last visit for this patient.
     */
    public function lastVisit()
    {
        return $this->visits()->latest('created_at')->first();
    }
}
