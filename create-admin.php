<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Clear existing admin users with this email
User::where('email', 'admin@clinic.com')->delete();

// Create new admin user
$admin = User::create([
    'name' => 'System Administrator',
    'email' => 'admin@clinic.com',
    'password' => Hash::make('admin@clinic.com'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);

echo "✓ Admin user created successfully!" . PHP_EOL;
echo "  Email: admin@clinic.com" . PHP_EOL;
echo "  Password: admin@clinic.com" . PHP_EOL;
echo "  Role: admin" . PHP_EOL;
