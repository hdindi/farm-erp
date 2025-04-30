<?php

// database/seeders/SalesPricesTableSeeder.php
namespace Database\Seeders;

use App\Models\Batch;
use App\Models\SalesPrice;
use App\Models\SalesUnit;
use Illuminate\Database\Seeder;

class SalesPricesTableSeeder extends Seeder
{
    public function run()
    {
        $units = SalesUnit::all();
        $batches = Batch::where('status', 'active')->get();

        // Egg prices
        $trayUnit = $units->where('name', 'Tray')->first();
        $dozenUnit = $units->where('name', 'Dozen')->first();

        SalesPrice::create([
            'sales_unit_id' => $trayUnit->id,
            'price' => 450.00,
            'item_type' => 'egg',
            'item_id' => null,
            'effective_date' => now()->subDays(30),
            'status' => 'active',
        ]);

        SalesPrice::create([
            'sales_unit_id' => $dozenUnit->id,
            'price' => 180.00,
            'item_type' => 'egg',
            'item_id' => null,
            'effective_date' => now()->subDays(30),
            'status' => 'active',
        ]);

        // Bird prices
        $pieceUnit = $units->where('name', 'Piece')->first();
        $kgUnit = $units->where('name', 'Kilogram')->first();

        foreach ($batches as $batch) {
            if ($batch->birdType->name === 'Broiler') {
                SalesPrice::create([
                    'sales_unit_id' => $pieceUnit->id,
                    'price' => 800.00,
                    'item_type' => 'bird',
                    'item_id' => $batch->id,
                    'effective_date' => now()->subDays(30),
                    'status' => 'active',
                ]);

                SalesPrice::create([
                    'sales_unit_id' => $kgUnit->id,
                    'price' => 350.00,
                    'item_type' => 'bird',
                    'item_id' => $batch->id,
                    'effective_date' => now()->subDays(30),
                    'status' => 'active',
                ]);
            }
        }
    }
}
