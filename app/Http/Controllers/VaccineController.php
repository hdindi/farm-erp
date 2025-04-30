<?php

namespace App\Http\Controllers;

use App\Models\Vaccine;
use Illuminate\Http\Request;

class VaccineController extends Controller
{
    public function index()
    {
        $vaccines = Vaccine::latest()->paginate(10);
        return view('vaccines.index', compact('vaccines'));
    }

    public function create()
    {
        return view('vaccines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:vaccines',
            'description' => 'nullable|string|max:1000',
            'manufacturer' => 'nullable|string|max:255',
            'minimum_age_days' => 'nullable|integer|min:0',
            'booster_interval_days' => 'nullable|integer|min:0',
        ]);

        Vaccine::create($validated);

        return redirect()->route('vaccines.index')
            ->with('success', 'Vaccine created successfully.');
    }

    public function show(Vaccine $vaccine)
    {
        return view('vaccines.show', compact('vaccine'));
    }

    public function edit(Vaccine $vaccine)
    {
        return view('vaccines.edit', compact('vaccine'));
    }

    public function update(Request $request, Vaccine $vaccine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:vaccines,name,'.$vaccine->id,
            'description' => 'nullable|string|max:1000',
            'manufacturer' => 'nullable|string|max:255',
            'minimum_age_days' => 'nullable|integer|min:0',
            'booster_interval_days' => 'nullable|integer|min:0',
        ]);

        $vaccine->update($validated);

        return redirect()->route('vaccines.index')
            ->with('success', 'Vaccine updated successfully.');
    }

    public function destroy(Vaccine $vaccine)
    {
        $vaccine->delete();

        return redirect()->route('vaccines.index')
            ->with('success', 'Vaccine deleted successfully.');
    }
}
