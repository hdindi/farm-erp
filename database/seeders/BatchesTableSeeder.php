<?php

namespace Database\Seeders;

use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BatchesTableSeeder extends Seeder
{
    public function run()
    {
        $batches = [
            [
                'batch_code' => 'LYR-2023-001',
                'bird_type_id' => 1, // Layer
                'breed_id' => 1, // Rhode Island Red
                'source_farm' => 'Poultry Genetics Kenya',
                'bird_age_days' => 1,
                'initial_population' => 500,
                'current_population' => 500,
                'date_received' => Carbon::now()->subDays(30),
                'hatch_date' => Carbon::now()->subDays(31),
                'expected_end_date' => Carbon::now()->addDays(500),
                'status' => 'active',
            ],
            [
                'batch_code' => 'BRL-2023-002',
                'bird_type_id' => 2, // Broiler
                'breed_id' => 2, // Cornish Cross
                'source_farm' => 'AgroFeed Ltd',
                'bird_age_days' => 1,
                'initial_population' => 1000,
                'current_population' => 1000,
                'date_received' => Carbon::now()->subDays(15),
                'hatch_date' => Carbon::now()->subDays(16),
                'expected_end_date' => Carbon::now()->addDays(70),
                'status' => 'active',
            ],
            [
                'batch_code' => 'LYR-2023-003',
                'bird_type_id' => 1, // Layer
                'breed_id' => 3, // Leghorn
                'source_farm' => 'Poultry Genetics Kenya',
                'bird_age_days' => 150,
                'initial_population' => 300,
                'current_population' => 290,
                'date_received' => Carbon::now()->subDays(160),
                'hatch_date' => Carbon::now()->subDays(180),
                'expected_end_date' => Carbon::now()->addDays(320),
                'status' => 'active',
            ],
        ];

        foreach ($batches as $batch) {
            Batch::create($batch);
        }
    }
}
