<?php

namespace App\Http\Controllers;

use App\Models\PurchaseUnit;
use Illuminate\Http\Request;

class PurchaseUnitController extends Controller
{
    public function index()
    {
        $units = PurchaseUnit::latest()->paginate(10);
        return view('purchase-units.index', compact('units'));
    }

    public function create()
    {
        return view('purchase-units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:purchase_units',
            'description' => 'nullable|string|max:255',
        ]);

        PurchaseUnit::create($validated);

        return redirect()->route('purchase-units.index')
            ->with('success', 'Purchase unit created successfully.');
    }

    public function show(PurchaseUnit $purchaseUnit)
    {
        return view('purchase-units.show', compact('purchaseUnit'));
    }

    public function edit(PurchaseUnit $purchaseUnit)
    {
        return view('purchase-units.edit', compact('purchaseUnit'));
    }

    public function update(Request $request, PurchaseUnit $purchaseUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:purchase_units,name,'.$purchaseUnit->id,
            'description' => 'nullable|string|max:255',
        ]);

        $purchaseUnit->update($validated);

        return redirect()->route('purchase-units.index')
            ->with('success', 'Purchase unit updated successfully.');
    }

    public function destroy(PurchaseUnit $purchaseUnit)
    {
        $purchaseUnit->delete();

        return redirect()->route('purchase-units.index')
            ->with('success', 'Purchase unit deleted successfully.');
    }
}
