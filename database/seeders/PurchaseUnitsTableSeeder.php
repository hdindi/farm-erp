<?php
// database/seeders/PurchaseUnitsTableSeeder.php
namespace Database\Seeders;

use App\Models\PurchaseUnit;
use Illuminate\Database\Seeder;

class PurchaseUnitsTableSeeder extends Seeder
{
    public function run()
    {
        $units = [
            ['name' => 'Kilogram', 'description' => 'Measured by weight'],
            ['name' => 'Bag', 'description' => 'Standard 25kg or 50kg bags'],
            ['name' => 'Ton', 'description' => 'Metric ton (1000kg)'],
            ['name' => 'Liter', 'description' => 'For liquid feed additives'],
            ['name' => 'Carton', 'description' => 'Packaged cartons of feed'],
        ];

        foreach ($units as $unit) {
            PurchaseUnit::create($unit);
        }
    }
}
