<?php

namespace App\Http\Controllers;

use App\Models\SalesUnit;
use Illuminate\Http\Request;

class SalesUnitController extends Controller
{
    public function index()
    {
        $units = SalesUnit::latest()->paginate(10);
        return view('sales-units.index', compact('units'));
    }

    public function create()
    {
        return view('sales-units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:sales_units',
            'description' => 'nullable|string|max:255',
        ]);

        SalesUnit::create($validated);

        return redirect()->route('sales-units.index')
            ->with('success', 'Sales unit created successfully.');
    }

    public function show(SalesUnit $salesUnit)
    {
        return view('sales-units.show', compact('salesUnit'));
    }

    public function edit(SalesUnit $salesUnit)
    {
        return view('sales-units.edit', compact('salesUnit'));
    }

    public function update(Request $request, SalesUnit $salesUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:sales_units,name,'.$salesUnit->id,
            'description' => 'nullable|string|max:255',
        ]);

        $salesUnit->update($validated);

        return redirect()->route('sales-units.index')
            ->with('success', 'Sales unit updated successfully.');
    }

    public function destroy(SalesUnit $salesUnit)
    {
        $salesUnit->delete();

        return redirect()->route('sales-units.index')
            ->with('success', 'Sales unit deleted successfully.');
    }
}
