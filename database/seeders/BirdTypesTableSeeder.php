<?php
// database/seeders/BirdTypesTableSeeder.php
namespace Database\Seeders;

use App\Models\BirdType;
use Illuminate\Database\Seeder;

class BirdTypesTableSeeder extends Seeder
{
    public function run()
    {
        $birdTypes = [
            [
                'name' => 'Broiler',
                'description' => 'Chickens bred specifically for meat production',
                'egg_production_cycle' => null,
            ],
            [
                'name' => 'Layer',
                'description' => 'Chickens bred for egg production',
                'egg_production_cycle' => 365,
            ],
            [
                'name' => 'Dual Purpose',
                'description' => 'Chickens suitable for both meat and egg production',
                'egg_production_cycle' => 300,
            ],
            [
                'name' => 'Turkey',
                'description' => 'Large birds primarily raised for meat',
                'egg_production_cycle' => null,
            ],
            [
                'name' => 'Quail',
                'description' => 'Small birds raised for both meat and eggs',
                'egg_production_cycle' => 240,
            ],
        ];

        foreach ($birdTypes as $birdType) {
            BirdType::create($birdType);
        }
    }
}
