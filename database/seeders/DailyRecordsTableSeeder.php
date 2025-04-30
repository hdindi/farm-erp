<?php

namespace Database\Seeders;

use App\Models\DailyRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailyRecordsTableSeeder extends Seeder
{
    public function run()
    {
        // For batch 1 (Layers, 30 days old)
        for ($i = 1; $i <= 30; $i++) {
            $date = Carbon::now()->subDays(30 - $i);

            DailyRecord::create([
                'batch_id' => 1,
                'record_date' => $date,
                'stage_id' => $i <= 28 ? 1 : 2, // Starter for first 28 days, then Grower
                'day_in_stage' => $i <= 28 ? $i : $i - 28,
                'alive_count' => 500 - ($i * 1), // Losing about 1 bird per day
                'dead_count' => $i * 1,
                'culls_count' => $i % 5 == 0 ? 1 : 0, // Cull 1 bird every 5 days
                'mortality_rate' => ($i * 1) / 500 * 100,
                'average_weight_grams' => 50 + ($i * 15), // Growing about 15g per day
                'notes' => $i % 7 == 0 ? 'Weekly health check completed' : null,
            ]);
        }

        // For batch 2 (Broilers, 15 days old)
        for ($i = 1; $i <= 15; $i++) {
            $date = Carbon::now()->subDays(15 - $i);

            DailyRecord::create([
                'batch_id' => 2,
                'record_date' => $date,
                'stage_id' => 1, // Still in starter
                'day_in_stage' => $i,
                'alive_count' => 1000 - ($i * 2), // Losing about 2 birds per day
                'dead_count' => $i * 2,
                'culls_count' => $i % 3 == 0 ? 1 : 0, // Cull 1 bird every 3 days
                'mortality_rate' => ($i * 2) / 1000 * 100,
                'average_weight_grams' => 40 + ($i * 30), // Growing about 30g per day
                'notes' => $i % 5 == 0 ? 'Increased feed amount' : null,
            ]);
        }

        // For batch 3 (Layers, 160 days old)
        for ($i = 1; $i <= 30; $i++) {
            $date = Carbon::now()->subDays(30 - $i);

            DailyRecord::create([
                'batch_id' => 3,
                'record_date' => $date,
                'stage_id' => 4, // Laying stage
                'day_in_stage' => 130 + $i,
                'alive_count' => 290 - ($i * 0), // No mortality in this period
                'dead_count' => 0,
                'culls_count' => $i % 10 == 0 ? 1 : 0, // Cull 1 bird every 10 days
                'mortality_rate' => 0,
                'average_weight_grams' => 1900,
                'notes' => $i % 7 == 0 ? 'Egg production check' : null,
            ]);
        }
    }
}
