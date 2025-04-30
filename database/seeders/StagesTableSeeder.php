<?php
// database/seeders/StagesTableSeeder.php
namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;

class StagesTableSeeder extends Seeder
{
    public function run()
    {
        $stages = [
            [
                'name' => 'Starter',
                'description' => 'Day 1 to 10 for broilers, Day 1 to 6 weeks for layers',
                'min_age_days' => 0,
                'max_age_days' => 10,
                'target_weight_grams' => 250,
            ],
            [
                'name' => 'Grower',
                'description' => 'Day 11 to 24 for broilers, 6-12 weeks for layers',
                'min_age_days' => 11,
                'max_age_days' => 24,
                'target_weight_grams' => 1200,
            ],
            [
                'name' => 'Finisher',
                'description' => 'Day 25 to slaughter for broilers, 12-18 weeks for layers',
                'min_age_days' => 25,
                'max_age_days' => 42,
                'target_weight_grams' => 2500,
            ],
            [
                'name' => 'Layer Pre-peak',
                'description' => '18-22 weeks for layers preparing for egg production',
                'min_age_days' => 126,
                'max_age_days' => 154,
                'target_weight_grams' => 1700,
            ],
            [
                'name' => 'Layer Peak',
                'description' => '22-45 weeks for layers in peak production',
                'min_age_days' => 154,
                'max_age_days' => 315,
                'target_weight_grams' => 1800,
            ],
            [
                'name' => 'Layer Post-peak',
                'description' => '45+ weeks for layers in declining production',
                'min_age_days' => 315,
                'max_age_days' => 500,
                'target_weight_grams' => 1850,
            ],
        ];

        foreach ($stages as $stage) {
            Stage::create($stage);
        }
    }
}
