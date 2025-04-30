<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\SalesPrice;
use App\Models\SalesUnit;
use Illuminate\Http\Request;

class SalesPriceController extends Controller
{
    public function index()
    {
        $salesPrices = SalesPrice::with(['salesUnit'])
            ->latest()
            ->paginate(10);

        return view('sales-prices.index', compact('salesPrices'));
    }

    public function create()
    {
        $salesUnits = SalesUnit::all();
        $batches = Batch::where('status', 'active')->get();

        return view('sales-prices.create', compact('salesUnits', 'batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_unit_id' => 'required|exists:sales_units,id',
            'price' => 'required|numeric|min:0',
            'item_type' => 'required|in:egg,bird,manure',
            'item_id' => 'nullable|required_if:item_type,bird|exists:batches,id',
            'effective_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        SalesPrice::create($validated);

        return redirect()->route('sales-prices.index')
            ->with('success', 'Sales price created successfully.');
    }

    public function show(SalesPrice $salesPrice)
    {
        $salesPrice->load(['salesUnit']);
        if ($salesPrice->item_type === 'bird') {
            $salesPrice->load(['batch']);
        }
        return view('sales-prices.show', compact('salesPrice'));
    }

    public function edit(SalesPrice $salesPrice)
    {
        $salesUnits = SalesUnit::all();
        $batches = Batch::where('status', 'active')->get();

        return view('sales-prices.edit', compact('salesPrice', 'salesUnits', 'batches'));
    }

    public function update(Request $request, SalesPrice $salesPrice)
    {
        $validated = $request->validate([
            'sales_unit_id' => 'required|exists:sales_units,id',
            'price' => 'required|numeric|min:0',
            'item_type' => 'required|in:egg,bird,manure',
            'item_id' => 'nullable|required_if:item_type,bird|exists:batches,id',
            'effective_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $salesPrice->update($validated);

        return redirect()->route('sales-prices.index')
            ->with('success', 'Sales price updated successfully.');
    }

    public function destroy(SalesPrice $salesPrice)
    {
        $salesPrice->delete();

        return redirect()->route('sales-prices.index')
            ->with('success', 'Sales price deleted successfully.');
    }
}
