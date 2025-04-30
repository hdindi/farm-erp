<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use Illuminate\Http\Request;

class BreedController extends Controller
{
    public function index()
    {
        $breeds = Breed::latest()->paginate(10);
        return view('breeds.index', compact('breeds'));
    }

    public function create()
    {
        return view('breeds.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:breeds',
            'description' => 'nullable|string|max:1000',
        ]);

        Breed::create($validated);

        return redirect()->route('breeds.index')
            ->with('success', 'Breed created successfully.');
    }

    public function show(Breed $breed)
    {
        return view('breeds.show', compact('breed'));
    }

    public function edit(Breed $breed)
    {
        return view('breeds.edit', compact('breed'));
    }

    public function update(Request $request, Breed $breed)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:breeds,name,'.$breed->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $breed->update($validated);

        return redirect()->route('breeds.index')
            ->with('success', 'Breed updated successfully.');
    }

    public function destroy(Breed $breed)
    {
        $breed->delete();

        return redirect()->route('breeds.index')
            ->with('success', 'Breed deleted successfully.');
    }
}
