<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'clinic_id',
        'created_by',
        'appointment_date',
        'appointment_time',
        'end_time',
        'status',
        'reason',
        'notes',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
        'parent_appointment_id',
        'reminder_sent',
        'reminder_sent_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'recurring_end_date' => 'date',
        'is_recurring' => 'boolean',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
    ];

    /**
     * Get the patient for this appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for this appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the user who created this appointment.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parent appointment (for recurring).
     */
    public function parentAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'parent_appointment_id');
    }

    /**
     * Get child appointments (recurring instances).
     */
    public function recurringAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'parent_appointment_id');
    }

    /**
     * Scope for upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time');
    }

    /**
     * Scope for today's appointments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    /**
     * Scope for a specific doctor.
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Check if appointment is in the past.
     */
    public function isPast(): bool
    {
        return $this->appointment_date->lt(today()) || 
            ($this->appointment_date->eq(today()) && now()->format('H:i') > $this->appointment_time);
    }

    /**
     * Check if appointment is today.
     */
    public function isToday(): bool
    {
        return $this->appointment_date->isToday();
    }

    /**
     * Get formatted date and time.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->appointment_date->format('M d, Y') . ' at ' . 
            \Carbon\Carbon::parse($this->appointment_time)->format('h:i A');
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'primary',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'warning',
            default => 'secondary',
        };
    }
}
