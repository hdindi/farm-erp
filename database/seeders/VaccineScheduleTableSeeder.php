<?php

namespace Database\Seeders;

use App\Models\VaccineSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VaccineScheduleTableSeeder extends Seeder
{
    public function run()
    {
        // For batch 1 (Layers)
        VaccineSchedule::create([
            'batch_id' => 1,
            'vaccine_id' => 1, // Newcastle
            'date_due' => Carbon::now()->subDays(23), // 7 days after arrival
            'status' => 'administered',
            'administered_date' => Carbon::now()->subDays(23),
            'vaccination_log_id' => 1,
        ]);

        VaccineSchedule::create([
            'batch_id' => 1,
            'vaccine_id' => 2, // Gumboro
            'date_due' => Carbon::now()->subDays(16), // 14 days after arrival
            'status' => 'administered',
            'administered_date' => Carbon::now()->subDays(16),
            'vaccination_log_id' => 2,
        ]);

        VaccineSchedule::create([
            'batch_id' => 1,
            'vaccine_id' => 1, // Newcastle booster
            'date_due' => Carbon::now()->addDays(5), // 28 days after first
            'status' => 'scheduled',
        ]);

        // For batch 2 (Broilers)
        VaccineSchedule::create([
            'batch_id' => 2,
            'vaccine_id' => 1, // Newcastle
            'date_due' => Carbon::now()->subDays(8), // 7 days after arrival
            'status' => 'administered',
            'administered_date' => Carbon::now()->subDays(8),
            'vaccination_log_id' => 3,
        ]);
    }
}
