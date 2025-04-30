<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use Illuminate\Http\Request;

class StageController extends Controller
{
    public function index()
    {
        $stages = Stage::latest()->paginate(10);
        return view('stages.index', compact('stages'));
    }

    public function create()
    {
        return view('stages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:stages',
            'description' => 'nullable|string|max:1000',
            'min_age_days' => 'required|integer|min:0',
            'max_age_days' => 'required|integer|min:0|gt:min_age_days',
            'target_weight_grams' => 'nullable|integer|min:0',
        ]);

        Stage::create($validated);

        return redirect()->route('stages.index')
            ->with('success', 'Stage created successfully.');
    }

    public function show(Stage $stage)
    {
        return view('stages.show', compact('stage'));
    }

    public function edit(Stage $stage)
    {
        return view('stages.edit', compact('stage'));
    }

    public function update(Request $request, Stage $stage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:stages,name,'.$stage->id,
            'description' => 'nullable|string|max:1000',
            'min_age_days' => 'required|integer|min:0',
            'max_age_days' => 'required|integer|min:0|gt:min_age_days',
            'target_weight_grams' => 'nullable|integer|min:0',
        ]);

        $stage->update($validated);

        return redirect()->route('stages.index')
            ->with('success', 'Stage updated successfully.');
    }

    public function destroy(Stage $stage)
    {
        $stage->delete();

        return redirect()->route('stages.index')
            ->with('success', 'Stage deleted successfully.');
    }
}
