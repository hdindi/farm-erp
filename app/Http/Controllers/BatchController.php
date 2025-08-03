<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BirdType;
use App\Models\Breed;
use App\Models\Stage; // Import the Stage model
use App\Models\DailyRecord;
use App\Models\FeedRecord; // Import the FeedRecord model
use Illuminate\Http\Request;
use Carbon\Carbon; // Make sure to import Carbon

/**
 * Get batch details including its current age in days.
 *
 * @param  \App\Models\Batch  $batch
 * @return \Illuminate\Http\JsonResponse
 */

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with(['birdType', 'breed'])->latest()->paginate(10);
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $birdTypes = BirdType::all();
        $breeds = Breed::all();
        return view('batches.create', compact('birdTypes', 'breeds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_code' => 'required|string|max:20|unique:batches',
            'bird_type_id' => 'required|exists:bird_types,id',
            'breed_id' => 'required|exists:breeds,id',
            'source_farm' => 'nullable|string|max:255',
            'bird_age_days' => 'required|integer|min:0',
            'initial_population' => 'required|integer|min:1',
            'date_received' => 'required|date',
            'hatch_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date|after_or_equal:date_received',
        ]);

        $validated['current_population'] = $validated['initial_population'];
        $validated['status'] = 'active';

        Batch::create($validated);

        return redirect()->route('batches.index')
            ->with('success', 'Batch created successfully.');
    }

    public function show(Batch $batch)
    {
        $batch->load(['birdType', 'breed', 'dailyRecords']);
        return view('batches.show', compact('batch'));
    }

    public function edit(Batch $batch)
    {
        $birdTypes = BirdType::all();
        $breeds = Breed::all();
        return view('batches.edit', compact('batch', 'birdTypes', 'breeds'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'batch_code' => 'required|string|max:20|unique:batches,batch_code,'.$batch->id,
            'bird_type_id' => 'required|exists:bird_types,id',
            'breed_id' => 'required|exists:breeds,id',
            'source_farm' => 'nullable|string|max:255',
            'bird_age_days' => 'required|integer|min:0',
            'current_population' => 'required|integer|min:0',
            'date_received' => 'required|date',
            'hatch_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date|after_or_equal:date_received',
            'status' => 'required|in:active,completed,culled',
        ]);

        $batch->update($validated);

        return redirect()->route('batches.index')
            ->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect()->route('batches.index')
            ->with('success', 'Batch deleted successfully.');
    }



    public function getBatchDetails(Batch $batch)
    {
        $hatchDate = Carbon::parse($batch->hatch_date);
        $dateReceived = Carbon::parse($batch->date_received);

        // 1. Bird's biological age in days and weeks
        $ageInDays = $hatchDate->diffInDays(Carbon::now());
        $ageInWeeks = floor($ageInDays / 7);

        // 2. Expected culling date (24 months after date_received)
        $expectedCullingDate = $dateReceived->copy()->addMonths(24)->format('Y-m-d');

        // ✅ NEW: Find the most recent daily record for this batch
        $lastRecord = DailyRecord::where('batch_id', $batch->id)
            ->orderBy('record_date', 'desc')
            ->first();

        return response()->json([
            'age_in_days'         => $ageInDays,
            'date_received'       => $dateReceived->format('Y-m-d'),
            'initial_population'  => $batch->initial_population,
            'bird_week'           => $ageInWeeks,
            'expected_culling_date' => $expectedCullingDate,
            // ✅ NEW DATA being sent to the frontend
            'current_population'  => $batch->current_population,
            'last_record_date'    => $lastRecord ? Carbon::parse($lastRecord->record_date)->format('Y-m-d') : 'None',
        ]);
    }




    public function getFeedDataForDailyRecord(DailyRecord $dailyRecord)
    {
        $batch = $dailyRecord->batch;
        $hatchDate = Carbon::parse($batch->hatch_date);

        // Calculate bird's age on the specific record_date
        $ageInDays = $hatchDate->diffInDays(Carbon::parse($dailyRecord->record_date));

        // Find the stage for that specific age
        $stage = Stage::where('min_age_days', '<=', $ageInDays)
            ->where('max_age_days', '>=', $ageInDays)
            ->first();

        // Calculate total feed already given on that day
        $feedGivenToday = FeedRecord::where('daily_record_id', $dailyRecord->id)->sum('quantity_kg');

        return response()->json([
            'bird_count' => $dailyRecord->alive_count,
            'age_in_days' => $ageInDays,
            'stage_name' => $stage ? $stage->name : 'N/A',
            // Example: Recommended feed per bird in grams (you can make this more dynamic)
            'recommended_feed_grams' => $stage ? $stage->recommended_feed_grams : 120,
            'feed_given_today_kg' => round($feedGivenToday, 2),
        ]);
    }




}
