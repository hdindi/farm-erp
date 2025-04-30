<?php

namespace Database\Seeders;

use App\Models\DailyRecord;
use App\Models\FeedRecord;
use Illuminate\Database\Seeder;

class FeedRecordsTableSeeder extends Seeder
{
    public function run()
    {
        // For all batches
        $dailyRecords = DailyRecord::all();

        foreach ($dailyRecords as $record) {
            $feedTypeId = 1; // Default to starter mash

            if ($record->batch_id == 1) { // Layers
                if ($record->day_in_stage > 28) {
                    $feedTypeId = 2; // Grower mash after 28 days
                }
            } elseif ($record->batch_id == 2) { // Broilers
                $feedTypeId = 1; // Always starter mash for first 15 days
            } elseif ($record->batch_id == 3) { // Mature layers
                $feedTypeId = 4; // Layer mash
            }

            $quantity = 0.1 * $record->alive_count; // 100g per bird per day

            FeedRecord::create([
                'daily_record_id' => $record->id,
                'feed_type_id' => $feedTypeId,
                'quantity_kg' => $quantity,
                'cost_per_kg' => $feedTypeId == 1 ? 50 : ($feedTypeId == 2 ? 45 : ($feedTypeId == 4 ? 55 : 60)),
                'feeding_time' => '08:00:00',
                'notes' => 'Morning feeding',
            ]);

            // Add second feeding for the day
            FeedRecord::create([
                'daily_record_id' => $record->id,
                'feed_type_id' => $feedTypeId,
                'quantity_kg' => $quantity * 0.8, // 80% of morning amount
                'cost_per_kg' => $feedTypeId == 1 ? 50 : ($feedTypeId == 2 ? 45 : ($feedTypeId == 4 ? 55 : 60)),
                'feeding_time' => '16:00:00',
                'notes' => 'Afternoon feeding',
            ]);
        }
    }
}
