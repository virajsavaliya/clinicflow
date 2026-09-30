<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Doctor users
        $doctor1 = User::create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'doctor2@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);

        // Reception user
        User::create([
            'name' => 'Reception Staff',
            'email' => 'reception@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'reception',
            'email_verified_at' => now(),
            'created_by_doctor_id' => $doctor1->id,
        ]);
    }
}
