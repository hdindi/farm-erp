<?php

namespace App\Http\Controllers;

use App\Models\DailyRecord;
use App\Models\EggProduction;
use Illuminate\Http\Request;

class EggProductionController extends Controller
{
    public function index()
    {
        $eggProductions = EggProduction::with(['dailyRecord.batch'])
            ->latest()
            ->paginate(10);

        return view('egg-production.index', compact('eggProductions'));
    }

    public function create()
    {
        $dailyRecords = DailyRecord::with('batch')
            ->whereHas('batch.birdType', function($query) {
                $query->where('name', 'Layer');
            })
            ->whereHas('batch', function($query) {
                $query->where('status', 'active')
                    ->where('bird_age_days', '>=', 126); // Layers older than 18 weeks
            })
            ->get();

        return view('egg-production.create', compact('dailyRecords'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'daily_record_id' => 'required|exists:daily_records,id',
            'total_eggs' => 'required|integer|min:0',
            'good_eggs' => 'required|integer|min:0|lte:total_eggs',
            'cracked_eggs' => 'required|integer|min:0|lte:total_eggs',
            'damaged_eggs' => 'required|integer|min:0|lte:total_eggs',
            'collection_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate that the sum of good, cracked, and damaged eggs equals total eggs
        $sum = $validated['good_eggs'] + $validated['cracked_eggs'] + $validated['damaged_eggs'];
        if ($sum != $validated['total_eggs']) {
            return back()->withErrors([
                'total_eggs' => 'The sum of good, cracked, and damaged eggs must equal the total eggs.'
            ])->withInput();
        }

        EggProduction::create($validated);

        return redirect()->route('egg-production.index')
            ->with('success', 'Egg production record created successfully.');
    }

    public function show(EggProduction $eggProduction)
    {
        $eggProduction->load(['dailyRecord.batch']);
        return view('egg-production.show', compact('eggProduction'));
    }

    public function edit(EggProduction $eggProduction)
    {
        $dailyRecords = DailyRecord::with('batch')
            ->whereHas('batch.birdType', function($query) {
                $query->where('name', 'Layer');
            })
            ->whereHas('batch', function($query) {
                $query->where('status', 'active')
                    ->where('bird_age_days', '>=', 126); // Layers older than 18 weeks
            })
            ->get();

        return view('egg-production.edit', compact('eggProduction', 'dailyRecords'));
    }

    public function update(Request $request, EggProduction $eggProduction)
    {
        $validated = $request->validate([
            'daily_record_id' => 'required|exists:daily_records,id',
            'total_eggs' => 'required|integer|min:0',
            'good_eggs' => 'required|integer|min:0|lte:total_eggs',
            'cracked_eggs' => 'required|integer|min:0|lte:total_eggs',
            'damaged_eggs' => 'required|integer|min:0|lte:total_eggs',
            'collection_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate that the sum of good, cracked, and damaged eggs equals total eggs
        $sum = $validated['good_eggs'] + $validated['cracked_eggs'] + $validated['damaged_eggs'];
        if ($sum != $validated['total_eggs']) {
            return back()->withErrors([
                'total_eggs' => 'The sum of good, cracked, and damaged eggs must equal the total eggs.'
            ])->withInput();
        }

        $eggProduction->update($validated);

        return redirect()->route('egg-production.index')
            ->with('success', 'Egg production record updated successfully.');
    }

    public function destroy(EggProduction $eggProduction)
    {
        $eggProduction->delete();

        return redirect()->route('egg-production.index')
            ->with('success', 'Egg production record deleted successfully.');
    }
}
