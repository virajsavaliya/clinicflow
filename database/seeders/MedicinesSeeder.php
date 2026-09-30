<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            ['name' => 'Crocin Advance 500mg (Paracetamol)', 'barcode' => '890123400001', 'unit_price' => 15.00, 'reorder_level' => 50],
            ['name' => 'Dolo 650mg (Paracetamol)', 'barcode' => '890123400002', 'unit_price' => 30.00, 'reorder_level' => 50],
            ['name' => 'Combiflam (Ibu + Para)', 'barcode' => '890123400003', 'unit_price' => 35.00, 'reorder_level' => 40],
            ['name' => 'Augmentin 625 Duo (Amoxicillin/Clav)', 'barcode' => '890123400004', 'unit_price' => 200.00, 'reorder_level' => 20],
            ['name' => 'Azithral 500mg (Azithromycin)', 'barcode' => '890123400005', 'unit_price' => 120.00, 'reorder_level' => 20],
            ['name' => 'Pantocid DSR (Pantoprazole + Domperidone)', 'barcode' => '890123400006', 'unit_price' => 110.00, 'reorder_level' => 30],
            ['name' => 'Aciloc 150mg (Ranitidine)', 'barcode' => '890123400007', 'unit_price' => 38.00, 'reorder_level' => 40],
            ['name' => 'O2 (Ofloxacin + Ornidazole)', 'barcode' => '890123400008', 'unit_price' => 150.00, 'reorder_level' => 30],
            ['name' => 'Metrogyl 400 (Metronidazole)', 'barcode' => '890123400009', 'unit_price' => 22.00, 'reorder_level' => 40],
            ['name' => 'Allegra 120mg (Fexofenadine)', 'barcode' => '890123400010', 'unit_price' => 180.00, 'reorder_level' => 20],
            ['name' => 'Okacet (Cetirizine 10mg)', 'barcode' => '890123400011', 'unit_price' => 18.00, 'reorder_level' => 50],
            ['name' => 'Eldoper (Loperamide 2mg)', 'barcode' => '890123400012', 'unit_price' => 25.00, 'reorder_level' => 30],
            ['name' => 'Domstal (Domperidone 10mg)', 'barcode' => '890123400013', 'unit_price' => 30.00, 'reorder_level' => 40],
            ['name' => 'Spasmo-Proxyvon Plus', 'barcode' => '890123400014', 'unit_price' => 45.00, 'reorder_level' => 30],
            ['name' => 'Voveran SR 100 (Diclofenac)', 'barcode' => '890123400015', 'unit_price' => 95.00, 'reorder_level' => 25],
            ['name' => 'Zerodol-SP', 'barcode' => '890123400016', 'unit_price' => 115.00, 'reorder_level' => 30],
            ['name' => 'Electral ORS Powder', 'barcode' => '890123400017', 'unit_price' => 21.00, 'reorder_level' => 100],
            ['name' => 'Corex / Ascoril LS Syrup', 'barcode' => '890123400018', 'unit_price' => 110.00, 'reorder_level' => 20],
            ['name' => 'Betadine Ointment 5%', 'barcode' => '890123400019', 'unit_price' => 90.00, 'reorder_level' => 15],
            ['name' => 'Soframycin Skin Cream', 'barcode' => '890123400020', 'unit_price' => 45.00, 'reorder_level' => 15]
        ];

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\MedicineStock::truncate();
        \App\Models\Medicine::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($medicines as $med) {
            $m = \App\Models\Medicine::create($med);
            \App\Models\MedicineStock::create([
                'medicine_id' => $m->id,
                'batch_number' => 'BATCH-' . rand(1000, 9999),
                'qty' => rand(50, 200),
                'expiry_date' => now()->addMonths(rand(6, 24)),
            ]);
        }
    }
}
