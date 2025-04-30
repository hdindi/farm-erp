<?php

namespace App\Http\Controllers;

use App\Models\BirdType;
use Illuminate\Http\Request;

class BirdTypeController extends Controller
{
    public function index()
    {
        $birdTypes = BirdType::latest()->paginate(10);
        return view('bird-types.index', compact('birdTypes'));
    }

    public function create()
    {
        return view('bird-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:bird_types',
            'description' => 'nullable|string|max:1000',
            'egg_production_cycle' => 'nullable|integer|min:0',
        ]);

        BirdType::create($validated);

        return redirect()->route('bird-types.index')
            ->with('success', 'Bird type created successfully.');
    }

    public function show(BirdType $birdType)
    {
        return view('bird-types.show', compact('birdType'));
    }

    public function edit(BirdType $birdType)
    {
        return view('bird-types.edit', compact('birdType'));
    }

    public function update(Request $request, BirdType $birdType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:bird_types,name,'.$birdType->id,
            'description' => 'nullable|string|max:1000',
            'egg_production_cycle' => 'nullable|integer|min:0',
        ]);

        $birdType->update($validated);

        return redirect()->route('bird-types.index')
            ->with('success', 'Bird type updated successfully.');
    }

    public function destroy(BirdType $birdType)
    {
        $birdType->delete();

        return redirect()->route('bird-types.index')
            ->with('success', 'Bird type deleted successfully.');
    }
}
