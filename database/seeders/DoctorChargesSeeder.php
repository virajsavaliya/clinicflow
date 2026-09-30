<?php

namespace Database\Seeders;

use App\Models\DoctorCharge;
use App\Models\DoctorProfile;
use Illuminate\Database\Seeder;

class DoctorChargesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Charges for first doctor
        $doctor1Profile = DoctorProfile::whereHas('user', function ($q) {
            $q->where('email', 'doctor@clinic.test');
        })->first();

        if ($doctor1Profile) {
            DoctorCharge::create([
                'doctor_profile_id' => $doctor1Profile->id,
                'title' => 'Checkup',
                'price' => 80.00,
                'is_active' => true,
            ]);

            DoctorCharge::create([
                'doctor_profile_id' => $doctor1Profile->id,
                'title' => 'Follow-up Consultation',
                'price' => 50.00,
                'is_active' => true,
            ]);

            DoctorCharge::create([
                'doctor_profile_id' => $doctor1Profile->id,
                'title' => 'Lab Test Review',
                'price' => 40.00,
                'is_active' => true,
            ]);
        }

        // Charges for second doctor
        $doctor2Profile = DoctorProfile::whereHas('user', function ($q) {
            $q->where('email', 'doctor2@clinic.test');
        })->first();

        if ($doctor2Profile) {
            DoctorCharge::create([
                'doctor_profile_id' => $doctor2Profile->id,
                'title' => 'General Checkup',
                'price' => 100.00,
                'is_active' => true,
            ]);

            DoctorCharge::create([
                'doctor_profile_id' => $doctor2Profile->id,
                'title' => 'Health Screening',
                'price' => 150.00,
                'is_active' => true,
            ]);

            DoctorCharge::create([
                'doctor_profile_id' => $doctor2Profile->id,
                'title' => 'Prescription Refill',
                'price' => 30.00,
                'is_active' => true,
            ]);
        }
    }
}
