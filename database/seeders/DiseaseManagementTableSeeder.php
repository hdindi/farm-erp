<?php

namespace Database\Seeders;

use App\Models\DiseaseManagement;
use Carbon\Carbon; // Ensure Carbon is imported
use Illuminate\Database\Seeder;
// Removed App\Models\Batch import as it's not directly used here
// use App\Models\Batch;


// Renamed class to follow Laravel convention (Optional, but good practice)
// class DiseaseManagementTableSeeder extends Seeder
class DiseaseManagementTableSeeder extends Seeder // Standard naming convention
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void // Added return type hint
    {
        // Define treatment start dates to avoid repetition
        $treatmentStartDate1 = Carbon::now()->subDays(10); // Approx April 17th, 2025
        $treatmentStartDate2 = Carbon::now()->subDays(5);  // Approx April 22nd, 2025

        // For batch 1 (Layers)
        DiseaseManagement::create([
            'batch_id' => 1,
            'disease_id' => 3, // Assumes ID 3 is Coccidiosis
            'drug_id' => 3,    // Assumes ID 3 is Sulfadimethoxine
            'observation_date' => $treatmentStartDate1->toDateString(), // <-- ADDED THIS LINE
            'affected_count' => 5,
            'treatment_start_date' => $treatmentStartDate1->toDateString(),
            'treatment_end_date' => Carbon::now()->subDays(5)->toDateString(), // Approx April 22nd, 2025
            'notes' => 'Mild outbreak, responded well to treatment',
            // created_at, updated_at are handled automatically
        ]);

        // For batch 2 (Broilers)
        DiseaseManagement::create([
            'batch_id' => 2,
            'disease_id' => 1, // Assumes ID 1 is Newcastle
            'drug_id' => 1,    // Assumes ID 1 is Amoxicillin
            'observation_date' => $treatmentStartDate2->toDateString(), // <-- ADDED THIS LINE
            'affected_count' => 10,
            'treatment_start_date' => $treatmentStartDate2->toDateString(),
            'treatment_end_date' => Carbon::now()->subDays(1)->toDateString(), // Approx April 26th, 2025
            'notes' => 'Preventive treatment after neighbor farm outbreak',
            // created_at, updated_at are handled automatically
        ]);

        // You can add more records here if needed
    }
}
