<?php

// database/seeders/VaccinesTableSeeder.php
namespace Database\Seeders;

use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class VaccinesTableSeeder extends Seeder
{
    public function run()
    {
        $vaccines = [
            [
                'name' => 'Newcastle Disease Vaccine (Lasota)',
                'description' => 'Live vaccine for Newcastle disease',
                'manufacturer' => 'Kenya Veterinary Vaccines',
                'minimum_age_days' => 7,
                'booster_interval_days' => 28,
            ],
            [
                'name' => 'Infectious Bronchitis Vaccine',
                'description' => 'Live vaccine for IB protection',
                'manufacturer' => 'Intervet',
                'minimum_age_days' => 1,
                'booster_interval_days' => 21,
            ],
            [
                'name' => 'Gumboro Vaccine',
                'description' => 'Intermediate plus strain for IBD',
                'manufacturer' => 'Merial',
                'minimum_age_days' => 14,
                'booster_interval_days' => 21,
            ],
            [
                'name' => 'Fowl Pox Vaccine',
                'description' => 'Wing web stab application',
                'manufacturer' => 'Zoetis',
                'minimum_age_days' => 28,
                'booster_interval_days' => 90,
            ],
            [
                'name' => 'Marek\'s Disease Vaccine',
                'description' => 'Administered at hatchery',
                'manufacturer' => 'Ceva',
                'minimum_age_days' => 1,
                'booster_interval_days' => null,
            ],
        ];

        foreach ($vaccines as $vaccine) {
            Vaccine::create($vaccine);
        }
    }
}
