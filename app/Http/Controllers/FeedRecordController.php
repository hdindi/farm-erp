<?php

namespace App\Http\Controllers;

use App\Models\DailyRecord;
use App\Models\FeedRecord;
use App\Models\FeedType;
use Illuminate\Http\Request;

class FeedRecordController extends Controller
{
    public function index()
    {
        $feedRecords = FeedRecord::with(['dailyRecord.batch', 'feedType'])
            ->latest()
            ->paginate(10);

        return view('feed-records.index', compact('feedRecords'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch daily records where a feed record hasn't been added yet for simplicity,
        // or just fetch recent ones.
        $dailyRecords = DailyRecord::with('batch')
            ->orderBy('record_date', 'desc')
            ->limit(100) // Limit the dropdown size for performance
            ->get();

        $feedTypes = FeedType::all();

        return view('feed-records.create', compact('dailyRecords', 'feedTypes'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'daily_record_id' => 'required|exists:daily_records,id',
            'feed_type_id' => 'required|exists:feed_types,id',
            'quantity_kg' => 'required|numeric|min:0.01',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'feeding_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        FeedRecord::create($validated);

        return redirect()->route('feed-records.index')
            ->with('success', 'Feed record created successfully.');
    }

    public function show(FeedRecord $feedRecord)
    {
        $feedRecord->load(['dailyRecord.batch', 'feedType']);
        return view('feed-records.show', compact('feedRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeedRecord $feedRecord)
    {
        $dailyRecords = DailyRecord::with('batch')
            ->orderBy('record_date', 'desc')
            ->limit(100)
            ->get();

        $feedTypes = FeedType::all();

        return view('feed-records.edit', compact('feedRecord', 'dailyRecords', 'feedTypes'));
    }

    public function update(Request $request, FeedRecord $feedRecord)
    {
        $validated = $request->validate([
            'daily_record_id' => 'required|exists:daily_records,id',
            'feed_type_id' => 'required|exists:feed_types,id',
            'quantity_kg' => 'required|numeric|min:0.01',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'feeding_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $feedRecord->update($validated);

        return redirect()->route('feed-records.index')
            ->with('success', 'Feed record updated successfully.');
    }

    public function destroy(FeedRecord $feedRecord)
    {
        $feedRecord->delete();

        return redirect()->route('feed-records.index')
            ->with('success', 'Feed record deleted successfully.');
    }
}
