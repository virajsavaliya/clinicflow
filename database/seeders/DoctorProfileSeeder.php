<?php

namespace Database\Seeders;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create profile for first doctor
        $doctor1 = User::where('email', 'doctor@clinic.test')->first();
        if ($doctor1) {
            DoctorProfile::create([
                'user_id' => $doctor1->id,
                'clinic_name' => 'Dr. John\'s Medical Clinic',
                'clinic_logo' => null,
                'default_doctor_fee' => 100.00,
            ]);
        }

        // Create profile for second doctor
        $doctor2 = User::where('email', 'doctor2@clinic.test')->first();
        if ($doctor2) {
            DoctorProfile::create([
                'user_id' => $doctor2->id,
                'clinic_name' => 'Sarah\'s Health Center',
                'clinic_logo' => null,
                'default_doctor_fee' => 120.00,
            ]);
        }
    }
}
