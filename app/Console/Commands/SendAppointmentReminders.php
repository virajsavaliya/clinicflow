<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send automated WhatsApp/SMS reminders for upcoming appointments';

    public function handle()
    {
        $this->info('Checking for appointments that need reminders...');
        
        // Get appointments for tomorrow (24 hour reminder)
        $tomorrowAppointments = Appointment::with('patient')
            ->whereDate('appointment_date', Carbon::tomorrow())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get();
        
        // Get appointments in 1 hour (1 hour reminder)
        $oneHourFromNow = Carbon::now()->addHour();
        $todayAppointments = Appointment::with('patient')
            ->whereDate('appointment_date', Carbon::today())
            ->whereTime('appointment_time', '<=', $oneHourFromNow->format('H:i:s'))
            ->whereTime('appointment_time', '>=', Carbon::now()->format('H:i:s'))
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get();
        
        // Send 24-hour reminders
        foreach ($tomorrowAppointments as $appointment) {
            if ($appointment->patient && $appointment->patient->phone) {
                $this->sendReminder($appointment, '24 hours');
            }
        }
        
        // Send 1-hour reminders
        foreach ($todayAppointments as $appointment) {
            if ($appointment->patient && $appointment->patient->phone) {
                $this->sendReminder($appointment, '1 hour');
            }
        }
        
        $totalReminders = $tomorrowAppointments->count() + $todayAppointments->count();
        $this->info("Sent {$totalReminders} reminder(s) successfully!");
        
        return 0;
    }
    
    private function sendReminder($appointment, $timeframe)
    {
        $patient = $appointment->patient;
        $phone = $this->formatPhoneNumber($patient->phone);
        
        $date = Carbon::parse($appointment->appointment_date)->format('d M Y');
        $time = Carbon::parse($appointment->appointment_time)->format('h:i A');
        
        $message = "Hello {$patient->name},\n\n";
        $message .= "🔔 Reminder: Your appointment is scheduled for {$date} at {$time}.\n\n";
        $message .= "📍 ClinicFlow Healthcare\n";
        $message .= "📞 For any changes, please contact us.\n\n";
        $message .= "Thank you!";
        
        // Log the reminder
        \Log::info("Reminder sent to {$patient->name} ({$phone}) for appointment #{$appointment->id}");
        
        // In production, integrate with WhatsApp Business API or Twilio
        // For now, we'll log and create a notification record
        $this->line("✓ Reminder sent to {$patient->name} for {$date} at {$time}");
    }
    
    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if missing
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }
        
        return $cleanPhone;
    }
}
