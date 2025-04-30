<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BirdType;
use App\Models\Breed;
use Illuminate\Http\Request;

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
}
