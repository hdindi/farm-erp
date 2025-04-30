<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    public function index()
    {
        $drugs = Drug::latest()->paginate(10);
        return view('drugs.index', compact('drugs'));
    }

    public function create()
    {
        return view('drugs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:drugs',
            'description' => 'nullable|string|max:1000',
        ]);

        Drug::create($validated);

        return redirect()->route('drugs.index')
            ->with('success', 'Drug created successfully.');
    }

    public function show(Drug $drug)
    {
        return view('drugs.show', compact('drug'));
    }

    public function edit(Drug $drug)
    {
        return view('drugs.edit', compact('drug'));
    }

    public function update(Request $request, Drug $drug)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:drugs,name,'.$drug->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $drug->update($validated);

        return redirect()->route('drugs.index')
            ->with('success', 'Drug updated successfully.');
    }

    public function destroy(Drug $drug)
    {
        $drug->delete();

        return redirect()->route('drugs.index')
            ->with('success', 'Drug deleted successfully.');
    }
}
