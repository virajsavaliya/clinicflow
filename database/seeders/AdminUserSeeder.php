<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        User::updateOrCreate(
            ['email' => 'admin@clinic.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@clinic.com',
                'password' => Hash::make('admin@clinic.com'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        echo "✓ Admin user created: admin@clinic.com / admin@clinic.com\n";
    }
}
