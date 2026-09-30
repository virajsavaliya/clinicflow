<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\DoctorHoliday;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display appointments calendar/list for doctor.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        $date = $request->get('date', today()->toDateString());
        $view = $request->get('view', 'day'); // day, week, month

        $query = Appointment::with('patient', 'doctor')
            ->where('doctor_id', $doctorId)
            ->where('status', '!=', 'cancelled');

        if ($view === 'day') {
            $query->whereDate('appointment_date', $date);
        }
        elseif ($view === 'week') {
            $startOfWeek = Carbon::parse($date)->startOfWeek();
            $endOfWeek = Carbon::parse($date)->endOfWeek();
            $query->whereBetween('appointment_date', [$startOfWeek, $endOfWeek]);
        }
        elseif ($view === 'month') {
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();
            $query->whereBetween('appointment_date', [$startOfMonth, $endOfMonth]);
        }

        $appointments = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Get today's stats
        $todayStats = [
            'total' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->count(),
            'confirmed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'confirmed')->count(),
            'completed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'completed')->count(),
            'pending' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'scheduled')->count(),
        ];

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        $clinicId = $user->isDoctor() ? ($user->clinic_id ?? 1) : ($user->doctor->clinic_id ?? 1);
        $patients = Patient::orderBy('name')->get();

        return view('doctor.appointments', compact(
            'appointments',
            'date',
            'view',
            'todayStats',
            'upcomingAppointments',
            'patients'
        ));
    }

    /**
     * Display appointments calendar/list for reception.
     */
    public function receptionIndex(Request $request)
    {
        $user = auth()->user();
        $doctorId = $user->created_by_doctor_id;

        if (!$doctorId) {
            return back()->with('error', 'You are not linked to a doctor.');
        }

        $date = $request->get('date', today()->toDateString());
        $view = $request->get('view', 'day'); // day, week, month

        $query = Appointment::with('patient', 'doctor')
            ->where('doctor_id', $doctorId)
            ->where('status', '!=', 'cancelled');

        if ($view === 'day') {
            $query->whereDate('appointment_date', $date);
        }
        elseif ($view === 'week') {
            $startOfWeek = Carbon::parse($date)->startOfWeek();
            $endOfWeek = Carbon::parse($date)->endOfWeek();
            $query->whereBetween('appointment_date', [$startOfWeek, $endOfWeek]);
        }
        elseif ($view === 'month') {
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();
            $query->whereBetween('appointment_date', [$startOfMonth, $endOfMonth]);
        }

        $appointments = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Get today's stats
        $todayStats = [
            'total' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->count(),
            'confirmed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'confirmed')->count(),
            'completed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'completed')->count(),
            'pending' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'scheduled')->count(),
        ];

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        return view('reception.appointments', compact(
            'appointments',
            'date',
            'view',
            'todayStats',
            'upcomingAppointments'
        ));
    }

    /**
     * Display calendar view for reception.
     */
    public function receptionCalendar(Request $request)
    {
        $user = auth()->user();
        $doctorId = $user->created_by_doctor_id;

        if (!$doctorId) {
            return back()->with('error', 'You are not linked to a doctor.');
        }

        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Get start and end of month with some padding for calendar display
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth()->subDays(7);
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->addDays(7);

        $appointments = Appointment::with('patient', 'doctor')
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Get today's stats
        $todayStats = [
            'total' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->count(),
            'confirmed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'confirmed')->count(),
            'completed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'completed')->count(),
            'pending' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'scheduled')->count(),
        ];

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::with('patient', 'doctor')
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'appointments' => $appointments
            ]);
        }

        return view('reception.appointments-calendar', compact('appointments', 'todayStats', 'upcomingAppointments'));
    }

    /**
     * Display calendar view for doctor.
     */
    public function doctorCalendar(Request $request)
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Get start and end of month with some padding for calendar display
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth()->subDays(7);
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->addDays(7);

        $appointments = Appointment::with('patient', 'doctor')
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Get today's stats
        $todayStats = [
            'total' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->count(),
            'confirmed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'confirmed')->count(),
            'completed' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'completed')->count(),
            'pending' => Appointment::where('doctor_id', $doctorId)->whereDate('appointment_date', today())->where('status', 'scheduled')->count(),
        ];

        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::with('patient', 'doctor')
            ->where('doctor_id', $doctorId)
            ->whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'appointments' => $appointments
            ]);
        }
        $patients = Patient::orderBy('name')->get();

        return view('doctor.appointments-calendar', compact('appointments', 'todayStats', 'upcomingAppointments', 'patients'));
    }

    /**
     * Store a new appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'nullable|boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,biweekly,monthly',
            'recurring_end_date' => 'nullable|date|after:appointment_date',
        ]);

        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        // Get clinic_id from doctor
        $doctor = User::find($doctorId);
        $clinicId = $doctor->clinic_id ?? 1; // Default to 1 if not set

        // Check if slot is available
        if (!$this->isSlotAvailable($doctorId, $validated['appointment_date'], $validated['appointment_time'])) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is not available. Please choose another time.',
            ], 422);
        }

        // Create the appointment
        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $doctorId,
            'created_by' => $user->id,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'reason' => $validated['reason'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_frequency' => $validated['recurring_frequency'] ?? null,
            'recurring_end_date' => $validated['recurring_end_date'] ?? null,
            'status' => 'scheduled',
        ]);

        // Create recurring appointments if needed
        if ($appointment->is_recurring && $appointment->recurring_frequency && $appointment->recurring_end_date) {
            $this->createRecurringAppointments($appointment);
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment scheduled successfully',
            'appointment' => $appointment->load('patient'),
        ]);
    }

    /**
     * Get appointment details by ID.
     */
    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);

        // Check authorization
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        if ($appointment->doctor_id !== $doctorId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($appointment);
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no_show',
        ]);

        $appointment = Appointment::findOrFail($id);

        // Check authorization
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        if ($appointment->doctor_id !== $doctorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $appointment->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated',
            'appointment' => $appointment->load('patient'),
        ]);
    }

    /**
     * Reschedule an appointment.
     */
    public function reschedule(Request $request, $id)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        $appointment = Appointment::findOrFail($id);

        // Check authorization
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        if ($appointment->doctor_id !== $doctorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if new slot is available (skip if it's the same appointment)
        $isSlotTaken = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('id', '!=', $id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->whereTime('appointment_time', $validated['appointment_time'])
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->exists();

        if ($isSlotTaken) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is not available. Please choose another time.',
            ], 422);
        }

        // Update appointment date, time, and reset status to scheduled if it was cancelled
        $updateData = [
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
        ];

        // If appointment was cancelled, reset it to scheduled when rescheduling
        if ($appointment->status === 'cancelled') {
            $updateData['status'] = 'scheduled';
        }

        $appointment->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully',
            'appointment' => $appointment->load('patient'),
        ]);
    }

    /**
     * Cancel an appointment.
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        if ($appointment->doctor_id !== $doctorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully',
        ]);
    }

    /**
     * Get available time slots for a date.
     */
    public function getAvailableSlots(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'doctor_id' => 'nullable|exists:users,id',
            'exclude_id' => 'nullable|integer|exists:appointments,id',
        ]);

        $user = auth()->user();
        $doctorId = $validated['doctor_id'] ?? ($user->isDoctor() ? $user->id : $user->created_by_doctor_id);
        $date = Carbon::parse($validated['date']);
        $excludeId = $validated['exclude_id'] ?? null;

        // Check if it's a holiday
        $isHoliday = DoctorHoliday::where('doctor_id', $doctorId)
            ->whereDate('date', $date)
            ->exists();

        if ($isHoliday) {
            return response()->json([
                'success' => true,
                'available' => false,
                'message' => 'Doctor is not available on this date',
                'slots' => [],
            ]);
        }

        // Get schedule for this day
        $dayOfWeek = strtolower($date->format('l'));
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => true,
                'available' => false,
                'message' => 'Doctor is not available on ' . ucfirst($dayOfWeek) . 's',
                'slots' => [],
            ]);
        }

        // Generate all slots
        $allSlots = $schedule->getTimeSlots();

        // Get booked slots for this date (excluding the appointment being rescheduled)
        $bookedQuery = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['scheduled', 'confirmed']);

        if ($excludeId) {
            $bookedQuery->where('id', '!=', $excludeId);
        }

        $bookedSlots = $bookedQuery->pluck('appointment_time')
            ->map(fn($time) => Carbon::parse($time)->format('H:i'))
            ->toArray();

        // Filter out booked slots and past slots (if today)
        $availableSlots = collect($allSlots)->filter(function ($slot) use ($date, $bookedSlots) {
            // Already booked
            if (in_array($slot, $bookedSlots)) {
                return false;
            }

            // If today, filter out past times
            if ($date->isToday()) {
                return Carbon::parse($slot)->gt(now());
            }

            return true;
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'available' => true,
            'slots' => $availableSlots,
            'schedule' => [
                'start' => $schedule->start_time,
                'end' => $schedule->end_time,
                'duration' => $schedule->slot_duration,
            ],
        ]);
    }

    /**
     * Check if a slot is available.
     */
    private function isSlotAvailable($doctorId, $date, $time): bool
    {
        $date = Carbon::parse($date);

        // Check holiday
        if (DoctorHoliday::where('doctor_id', $doctorId)->whereDate('date', $date)->exists()) {
            return false;
        }

        // Check schedule
        $dayOfWeek = strtolower($date->format('l'));
        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return false;
        }

        // Check if time is within schedule
        $slotTime = Carbon::parse($time);
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        if ($slotTime->lt($startTime) || $slotTime->gte($endTime)) {
            return false;
        }

        // Check if slot is already booked
        $exists = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereTime('appointment_time', $time)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->exists();

        return !$exists;
    }

    /**
     * Create recurring appointments.
     */
    private function createRecurringAppointments(Appointment $parent): void
    {
        $currentDate = Carbon::parse($parent->appointment_date);
        $endDate = Carbon::parse($parent->recurring_end_date);

        $interval = match ($parent->recurring_frequency) {
                'daily' => '1 day',
                'weekly' => '1 week',
                'biweekly' => '2 weeks',
                'monthly' => '1 month',
                default => null,
            };

        if (!$interval)
            return;

        while ($currentDate->lt($endDate)) {
            $currentDate->add($interval);

            if ($currentDate->gt($endDate))
                break;

            // Check if slot is available
            if (!$this->isSlotAvailable($parent->doctor_id, $currentDate->toDateString(), $parent->appointment_time)) {
                continue;
            }

            Appointment::create([
                'patient_id' => $parent->patient_id,
                'doctor_id' => $parent->doctor_id,
                'created_by' => $parent->created_by,
                'appointment_date' => $currentDate->toDateString(),
                'appointment_time' => $parent->appointment_time,
                'reason' => $parent->reason,
                'notes' => $parent->notes,
                'is_recurring' => false,
                'parent_appointment_id' => $parent->id,
                'status' => 'scheduled',
            ]);
        }
    }

    /**
     * Manage doctor schedule.
     */
    public function schedule()
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        $schedules = DoctorSchedule::where('doctor_id', $doctorId)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get();

        $holidays = DoctorHoliday::where('doctor_id', $doctorId)
            ->where('date', '>=', today())
            ->orderBy('date')
            ->get();

        return view('doctor.schedule', compact('schedules', 'holidays'));
    }

    /**
     * Update doctor schedule.
     */
    public function updateSchedule(Request $request)
    {
        $validated = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.start_time' => 'nullable|date_format:H:i',
            'schedules.*.end_time' => 'nullable|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.slot_duration' => 'nullable|integer|min:5|max:120',
            'schedules.*.is_active' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        foreach ($validated['schedules'] as $scheduleData) {
            DoctorSchedule::updateOrCreate(
            [
                'doctor_id' => $doctorId,
                'day_of_week' => $scheduleData['day_of_week'],
            ],
            [
                'start_time' => $scheduleData['start_time'] ?? '09:00',
                'end_time' => $scheduleData['end_time'] ?? '17:00',
                'slot_duration' => $scheduleData['slot_duration'] ?? 15,
                'is_active' => $scheduleData['is_active'] ?? false,
            ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
        ]);
    }

    /**
     * Add a holiday.
     */
    public function addHoliday(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        DoctorHoliday::updateOrCreate(
        [
            'doctor_id' => $doctorId,
            'date' => $validated['date'],
        ],
        [
            'reason' => $validated['reason'] ?? 'Holiday',
        ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Holiday added successfully',
        ]);
    }

    /**
     * Remove a holiday.
     */
    public function removeHoliday($id)
    {
        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        DoctorHoliday::where('id', $id)
            ->where('doctor_id', $doctorId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Holiday removed successfully',
        ]);
    }

    /**
     * Convert appointment to walk-in visit.
     */
    public function convertToVisit($id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);

        $user = auth()->user();
        $doctorId = $user->isDoctor() ? $user->id : $user->created_by_doctor_id;

        if ($appointment->doctor_id !== $doctorId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Get next queue number
        $maxQueueNumber = \App\Models\Visit::whereDate('created_at', today())
            ->max('queue_number') ?? 0;

        // Create visit
        $visit = \App\Models\Visit::create([
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'queue_number' => $maxQueueNumber + 1,
            'status' => 'waiting',
        ]);

        // Mark appointment as completed
        $appointment->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Patient added to queue',
            'visit' => $visit,
        ]);
    }
}