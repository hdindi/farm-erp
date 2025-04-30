<?php

namespace Database\Seeders;

use App\Models\DailyRecord;
use App\Models\EggProduction;
use Illuminate\Database\Seeder;

class EggProductionTableSeeder extends Seeder
{
    public function run()
    {
        // Only for batch 3 (laying hens) - ensure records exist
        $dailyRecords = DailyRecord::where('batch_id', 3)
            ->where('stage_id', 4) // Ensure it's the laying stage
            ->get();

        if ($dailyRecords->isEmpty()) {
            $this->command->info('No daily records found for batch 3 (laying hens). Skipping egg production seeding.');
            return;
        }

        foreach ($dailyRecords as $record) {
            try {
                $totalEggs = rand(200, 250); // 200-250 eggs per day from 290 hens
                $goodEggs = $totalEggs - rand(5, 15);
                $crackedEggs = rand(3, 8);
                $damagedEggs = rand(2, 7);

                EggProduction::create([
                    'daily_record_id' => $record->id,
                    'total_eggs' => $totalEggs,
                    'good_eggs' => $goodEggs,
                    'cracked_eggs' => $crackedEggs,
                    'damaged_eggs' => $damagedEggs,
                    'collection_time' => '09:00:00',
                    'notes' => rand(0, 1) ? 'Normal production' : 'Slightly lower than expected',
                ]);
            } catch (\Exception $e) {
                $this->command->error("Failed to create egg production for daily record {$record->id}: ".$e->getMessage());
            }
        }
    }
}
