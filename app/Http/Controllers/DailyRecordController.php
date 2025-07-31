<?php

namespace App\Http\Controllers;

use App\Models\DailyRecord;
use App\Models\Batch;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import Rule for unique validation

class DailyRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dailyRecords = DailyRecord::with(['batch', 'stage'])->latest('record_date')->paginate(15); // Example pagination
        return view('daily-records.index', compact('dailyRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch active batches and all stages for the dropdowns
        $batches = Batch::where('status', 'active')->orderBy('batch_code')->get();
        $stages = Stage::orderBy('min_age_days')->get();


        // This creates the clean data structure needed for the JavaScript.
        $stagesData = $stages->mapWithKeys(function ($stage) {
            return [(string)$stage->id => [
                'min_age_days' => (int)$stage->min_age_days,
                'max_age_days' => (int)$stage->max_age_days,
            ]];
        })->all(); // Use ->all() to get a clean PHP array

        // Pass the clean array to the view
        // This is to ensure the JavaScript can access the stage data easily.


        return view('daily-records.create', [
            'batches' => $batches,
            'stages' => $stages,
            'stagesData' => $stagesData, // Pass the clean array
        ]);


        //return view('daily-records.create', compact('batches', 'stages', 'stagesData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // --- VALIDATION ---
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            // Add Unique Rule: Check uniqueness on the 'daily_records' table
            // for the combination of 'record_date' and 'batch_id'.
            'record_date' => [
                'required',
                'date',
                Rule::unique('daily_records')->where(function ($query) use ($request) {
                    return $query->where('batch_id', $request->batch_id)
                        ->where('record_date', $request->record_date);
                }),
            ],
            'stage_id' => 'required|exists:stages,id',
            'day_in_stage' => 'required|integer|min:1',
            'alive_count' => 'required|integer|min:0',
            'dead_count' => 'required|integer|min:0',
            'culls_count' => 'required|integer|min:0',
            'average_weight_grams' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000',
        ], [
            // Custom error message for the unique rule
            'record_date.unique' => 'A daily record already exists for this batch on this date.',
        ]);

        // --- Calculate Mortality Rate ---
        // Fetch the initial population for the selected batch
        $batch = Batch::find($validated['batch_id']);
        $initialPopulation = $batch ? $batch->initial_population : 0; // Handle case where batch might not be found (though validation should prevent)

        // Calculate start of day population (approximation)
        // Note: This assumes the 'alive_count' is the END of day count.
        // A more accurate calculation might need the previous day's alive count.
        $startOfDayPopulation = $validated['alive_count'] + $validated['dead_count'] + $validated['culls_count'];

        // Calculate mortality rate (avoid division by zero)
        $mortalityRate = ($startOfDayPopulation > 0)
            ? (($validated['dead_count'] + $validated['culls_count']) / $startOfDayPopulation) * 100
            : 0;

        // Add mortality rate to the validated data
        $validated['mortality_rate'] = round($mortalityRate, 2); // Round to 2 decimal places

        // --- Update Batch Current Population ---
        // It's generally better to calculate current population dynamically
        // or update it via a separate process/job after saving the daily record.
        // Directly updating here can lead to race conditions or inconsistencies
        // if multiple updates happen.
        // However, if you need to update it here:
        if ($batch) {
            $batch->current_population = $validated['alive_count'];
            $batch->save(); // Save the updated batch population
        }

        // --- Create Daily Record ---
        DailyRecord::create($validated);

        return redirect()->route('daily-records.index')
            ->with('success', 'Daily record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyRecord $dailyRecord)
    {
        // Eager load related data for the show view
        $dailyRecord->load(['batch', 'stage', 'feedRecords.feedType', 'eggProduction', 'vaccinationLogs.vaccine']);
        return view('daily-records.show', compact('dailyRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyRecord $dailyRecord)
    {
        $batches = Batch::orderBy('batch_code')->get(); // Get all batches for dropdown (might need filtering)
        $stages = Stage::orderBy('min_age_days')->get();
        return view('daily-records.edit', compact('dailyRecord', 'batches', 'stages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DailyRecord $dailyRecord)
    {
        // --- VALIDATION ---
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            // Add Unique Rule for update, ignoring the current record's ID
            'record_date' => [
                'required',
                'date',
                Rule::unique('daily_records')->where(function ($query) use ($request) {
                    return $query->where('batch_id', $request->batch_id)
                        ->where('record_date', $request->record_date);
                })->ignore($dailyRecord->id), // Ignore the current record ID
            ],
            'stage_id' => 'required|exists:stages,id',
            'day_in_stage' => 'required|integer|min:1',
            'alive_count' => 'required|integer|min:0',
            'dead_count' => 'required|integer|min:0',
            'culls_count' => 'required|integer|min:0',
            'average_weight_grams' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000',
        ], [
            'record_date.unique' => 'A daily record already exists for this batch on this date.',
        ]);

        // --- Recalculate Mortality Rate ---
        $startOfDayPopulation = $validated['alive_count'] + $validated['dead_count'] + $validated['culls_count'];
        $mortalityRate = ($startOfDayPopulation > 0)
            ? (($validated['dead_count'] + $validated['culls_count']) / $startOfDayPopulation) * 100
            : 0;
        $validated['mortality_rate'] = round($mortalityRate, 2);

        // --- Update Batch Current Population (if needed) ---
        // Be cautious with this logic, consider recalculating based on all daily records for the batch instead.
        $batch = Batch::find($validated['batch_id']);
        if ($batch && $batch->id == $dailyRecord->batch_id) { // Ensure it's the same batch
            // Only update if this is the *latest* record for the batch
            $latestRecordDate = DailyRecord::where('batch_id', $batch->id)->max('record_date');
            if ($validated['record_date'] == $latestRecordDate) {
                $batch->current_population = $validated['alive_count'];
                $batch->save();
            }
            // Else, a recalculation job/process might be better
        }


        // --- Update Daily Record ---
        $dailyRecord->update($validated);

        return redirect()->route('daily-records.show', $dailyRecord->id) // Redirect to show view after update
        ->with('success', 'Daily record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyRecord $dailyRecord)
    {
        try {
            // Note: Related records (feed, egg, vax) might be deleted automatically
            // if foreign keys have ON DELETE CASCADE. Check your migrations.
            $dailyRecord->delete();

            // Optional: Recalculate batch current population after delete
             $batch = $dailyRecord->batch;
             if ($batch) {
                 $latestRecord = DailyRecord::where('batch_id', $batch->id)->latest('record_date')->first();
                 $batch->current_population = $latestRecord ? $latestRecord->alive_count : $batch->initial_population;
                 $batch->save();
             }


            return redirect()->route('daily-records.index')
                ->with('success', 'Daily record deleted successfully.');
        } catch (\Exception $e) {
            // Log error
            Log::error("Error deleting daily record ID {$dailyRecord->id}: " . $e->getMessage());
            return redirect()->route('daily-records.index')
                ->with('error', 'Failed to delete daily record. Please check logs.');
        }
    }
}
