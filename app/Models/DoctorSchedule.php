<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'max_patients',
        'is_active',
    ];

    protected $casts = [
        'slot_duration' => 'integer',
        'max_patients' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the doctor for this schedule.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get all available time slots for this schedule.
     */
    public function getTimeSlots(): array
    {
        $slots = [];
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($this->slot_duration);
        }
        
        return $slots;
    }

    /**
     * Get day name capitalized.
     */
    public function getDayNameAttribute(): string
    {
        return ucfirst($this->day_of_week);
    }
}
