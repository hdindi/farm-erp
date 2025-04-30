<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function index()
    {
        $diseases = Disease::latest()->paginate(10);
        return view('diseases.index', compact('diseases'));
    }

    public function create()
    {
        return view('diseases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:diseases',
            'description' => 'nullable|string|max:1000',
        ]);

        Disease::create($validated);

        return redirect()->route('diseases.index')
            ->with('success', 'Disease created successfully.');
    }

    public function show(Disease $disease)
    {
        return view('diseases.show', compact('disease'));
    }

    public function edit(Disease $disease)
    {
        return view('diseases.edit', compact('disease'));
    }

    public function update(Request $request, Disease $disease)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:diseases,name,'.$disease->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $disease->update($validated);

        return redirect()->route('diseases.index')
            ->with('success', 'Disease updated successfully.');
    }

    public function destroy(Disease $disease)
    {
        $disease->delete();

        return redirect()->route('diseases.index')
            ->with('success', 'Disease deleted successfully.');
    }
}
