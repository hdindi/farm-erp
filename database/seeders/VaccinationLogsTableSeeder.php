<?php

namespace Database\Seeders;

use App\Models\DailyRecord;
use App\Models\VaccinationLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VaccinationLogsTableSeeder extends Seeder
{
    public function run()
    {
        // For batch 1 (Layers)
        $day7Record = DailyRecord::where('batch_id', 1)->where('day_in_stage', 7)->first();
        VaccinationLog::create([
            'daily_record_id' => $day7Record->id,
            'vaccine_id' => 1, // Newcastle
            'birds_vaccinated' => $day7Record->alive_count,
            'administered_by' => 'Dr. James',
            'next_due_date' => Carbon::parse($day7Record->record_date)->addDays(28),
            'notes' => 'First vaccination for the batch',
        ]);

        $day14Record = DailyRecord::where('batch_id', 1)->where('day_in_stage', 14)->first();
        VaccinationLog::create([
            'daily_record_id' => $day14Record->id,
            'vaccine_id' => 2, // Gumboro
            'birds_vaccinated' => $day14Record->alive_count,
            'administered_by' => 'Dr. James',
            'next_due_date' => Carbon::parse($day14Record->record_date)->addDays(21),
            'notes' => 'Gumboro vaccination',
        ]);

        // For batch 2 (Broilers)
        $day7Record = DailyRecord::where('batch_id', 2)->where('day_in_stage', 7)->first();
        VaccinationLog::create([
            'daily_record_id' => $day7Record->id,
            'vaccine_id' => 1, // Newcastle
            'birds_vaccinated' => $day7Record->alive_count,
            'administered_by' => 'Dr. James',
            'next_due_date' => Carbon::parse($day7Record->record_date)->addDays(28),
            'notes' => 'First vaccination for broilers',
        ]);
    }
}
