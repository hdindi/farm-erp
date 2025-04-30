<?php
// database/seeders/SalesUnitsTableSeeder.php
namespace Database\Seeders;

use App\Models\SalesUnit;
use Illuminate\Database\Seeder;

class SalesUnitsTableSeeder extends Seeder
{
    public function run()
    {
        $units = [
            ['name' => 'Piece', 'description' => 'Individual birds or eggs'],
            ['name' => 'Tray', 'description' => 'Standard egg tray (30 eggs)'],
            ['name' => 'Kilogram', 'description' => 'Sold by weight'],
            ['name' => 'Crate', 'description' => 'Standard bird crate'],
            ['name' => 'Dozen', 'description' => '12 eggs'],
        ];

        foreach ($units as $unit) {
            SalesUnit::create($unit);
        }
    }
}
