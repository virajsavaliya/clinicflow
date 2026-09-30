<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\DoctorProfile;

$doctor = User::where('role', 'doctor')->first();

if ($doctor) {
    if (!$doctor->doctorProfile) {
        DoctorProfile::create([
            'user_id' => $doctor->id,
            'clinic_name' => 'ClinicFlow Medical Center',
            'clinic_address' => '123 Main Street, City',
            'clinic_phone' => '1234567890',
            'default_doctor_fee' => 500.00,
        ]);
        echo "✅ Doctor profile created successfully for: {$doctor->name}\n";
    } else {
        echo "ℹ️  Doctor profile already exists\n";
    }
} else {
    echo "❌ No doctor user found\n";
}
