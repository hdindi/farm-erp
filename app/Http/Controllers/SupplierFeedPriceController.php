<?php

namespace App\Http\Controllers;

use App\Models\FeedType;
use App\Models\PurchaseUnit;
use App\Models\Supplier;
use App\Models\SupplierFeedPrice;
use Illuminate\Http\Request;

class SupplierFeedPriceController extends Controller
{
    public function index()
    {
        $supplierFeedPrices = SupplierFeedPrice::with(['supplier', 'feedType', 'purchaseUnit'])
            ->latest()
            ->paginate(10);

        return view('supplier-feed-prices.index', compact('supplierFeedPrices'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $feedTypes = FeedType::all();
        $purchaseUnits = PurchaseUnit::all();

        return view('supplier-feed-prices.create', compact('suppliers', 'feedTypes', 'purchaseUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'feed_type_id' => 'required|exists:feed_types,id',
            'purchase_unit_id' => 'required|exists:purchase_units,id',
            'supplier_price' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        SupplierFeedPrice::create($validated);

        return redirect()->route('supplier-feed-prices.index')
            ->with('success', 'Supplier feed price created successfully.');
    }

    public function show(SupplierFeedPrice $supplierFeedPrice)
    {
        $supplierFeedPrice->load(['supplier', 'feedType', 'purchaseUnit']);
        return view('supplier-feed-prices.show', compact('supplierFeedPrice'));
    }

    public function edit(SupplierFeedPrice $supplierFeedPrice)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $feedTypes = FeedType::all();
        $purchaseUnits = PurchaseUnit::all();

        return view('supplier-feed-prices.edit', compact('supplierFeedPrice', 'suppliers', 'feedTypes', 'purchaseUnits'));
    }

    public function update(Request $request, SupplierFeedPrice $supplierFeedPrice)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'feed_type_id' => 'required|exists:feed_types,id',
            'purchase_unit_id' => 'required|exists:purchase_units,id',
            'supplier_price' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $supplierFeedPrice->update($validated);

        return redirect()->route('supplier-feed-prices.index')
            ->with('success', 'Supplier feed price updated successfully.');
    }

    public function destroy(SupplierFeedPrice $supplierFeedPrice)
    {
        $supplierFeedPrice->delete();

        return redirect()->route('supplier-feed-prices.index')
            ->with('success', 'Supplier feed price deleted successfully.');
    }
}
