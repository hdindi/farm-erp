<?php

namespace App\Http\Controllers;

use App\Application\DTOs\BatchDTO;
use App\Application\Services\Contracts\BatchServiceInterface;
use App\Domain\Exceptions\BatchNotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Models\DailyRecord;
use App\Models\FeedRecord;
use App\Models\Stage;
use App\Presentation\Http\Requests\StoreBatchRequest;
use App\Presentation\Http\Requests\UpdateBatchRequest;
use App\Presentation\Http\Resources\BatchResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function __construct(
        private readonly BatchServiceInterface $batchService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'bird_type_id', 'breed_id', 'search']);
        $batches = $this->batchService->getPaginatedBatches($filters);

        if ($request->expectsJson()) {
            return BatchResource::collection($batches);
        }

        return view('batches.index', compact('batches'));
    }

    public function store(StoreBatchRequest $request)
    {
        try {
            $batchDTO = BatchDTO::fromArray($request->getValidatedData());
            $batch = $this->batchService->createBatch($batchDTO);

            if ($request->expectsJson()) {
                return new BatchResource($batch);
            }

            return redirect()->route('batches.show', $batch)
                ->with('success', 'Batch created successfully.');

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json($e->toArray(), $e->getHttpStatusCode());
            }

            return redirect()->back()
                ->withErrors($e->getErrors())
                ->withInput();
        }
    }

    public function show(int $id, Request $request)
    {
        try {
            $batch = $this->batchService->getBatch($id);

            if ($request->expectsJson()) {
                return new BatchResource($batch);
            }

            return view('batches.show', compact('batch'));

        } catch (BatchNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json($e->toArray(), $e->getHttpStatusCode());
            }

            abort(404, $e->getUserMessage());
        }
    }

    public function update(UpdateBatchRequest $request, int $id)
    {
        try {
            $batchDTO = BatchDTO::fromArray($request->getValidatedData());
            $batch = $this->batchService->updateBatch($id, $batchDTO);

            if ($request->expectsJson()) {
                return new BatchResource($batch);
            }

            return redirect()->route('batches.show', $batch)
                ->with('success', 'Batch updated successfully.');

        } catch (BatchNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json($e->toArray(), $e->getHttpStatusCode());
            }

            abort(404, $e->getUserMessage());
        }
    }

    public function create()
    {
        $birdTypes = \App\Models\BirdType::all();
        $breeds = \App\Models\Breed::all();
        return view('batches.create', compact('birdTypes', 'breeds'));
    }

    public function edit(int $id)
    {
        try {
            $batch = $this->batchService->getBatch($id);
            $birdTypes = \App\Models\BirdType::all();
            $breeds = \App\Models\Breed::all();
            return view('batches.edit', compact('batch', 'birdTypes', 'breeds'));
        } catch (BatchNotFoundException $e) {
            abort(404, $e->getUserMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->batchService->deleteBatch($id);
            return redirect()->route('batches.index')
                ->with('success', 'Batch deleted successfully.');
        } catch (BatchNotFoundException $e) {
            return redirect()->route('batches.index')
                ->with('error', $e->getUserMessage());
        }
    }

    public function getBatchDetails(\App\Models\Batch $batch)
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
            'age_in_days' => $ageInDays,
            'date_received' => $dateReceived->format('Y-m-d'),
            'initial_population' => $batch->initial_population,
            'bird_week' => $ageInWeeks,
            'expected_culling_date' => $expectedCullingDate,
            // ✅ NEW DATA being sent to the frontend
            'current_population' => $batch->current_population,
            'last_record_date' => $lastRecord ? Carbon::parse($lastRecord->record_date)->format('Y-m-d') : 'None',
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
