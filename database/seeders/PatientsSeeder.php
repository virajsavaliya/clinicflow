<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'name' => 'John Doe',
                'phone' => '9876543210',
                'age' => 45,
                'gender' => 'male',
                'address' => '123 Main Street, City',
            ],
            [
                'name' => 'Jane Smith',
                'phone' => '9876543211',
                'age' => 38,
                'gender' => 'female',
                'address' => '456 Oak Avenue, Town',
            ],
            [
                'name' => 'Michael Johnson',
                'phone' => '9876543212',
                'age' => 55,
                'gender' => 'male',
                'address' => '789 Pine Road, Village',
            ],
            [
                'name' => 'Emily Brown',
                'phone' => '9876543213',
                'age' => 28,
                'gender' => 'female',
                'address' => '321 Elm Street, City',
            ],
            [
                'name' => 'Robert Wilson',
                'phone' => '9876543214',
                'age' => 62,
                'gender' => 'male',
                'address' => '654 Maple Drive, Town',
            ],
        ];

        foreach ($patients as $patient) {
            Patient::create($patient);
        }
    }
}
