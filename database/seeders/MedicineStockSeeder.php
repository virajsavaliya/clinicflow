<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MedicineStock;
use Illuminate\Database\Seeder;

class MedicineStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all medicines and create stock entries
        $medicines = Medicine::all();

        foreach ($medicines as $medicine) {
            MedicineStock::create([
                'medicine_id' => $medicine->id,
                'qty' => rand(30, 200),
            ]);
        }
    }
}
