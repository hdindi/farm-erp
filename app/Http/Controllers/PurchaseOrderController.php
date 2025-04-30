<?php

namespace App\Http\Controllers;

use App\Models\FeedType;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderStatus;
use App\Models\PurchaseUnit;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'feedType', 'purchaseUnit', 'status'])
            ->latest()
            ->paginate(10);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $feedTypes = FeedType::all();
        $purchaseUnits = PurchaseUnit::all();
        $statuses = PurchaseOrderStatus::all();

        return view('purchase-orders.create', compact('suppliers', 'feedTypes', 'purchaseUnits', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_no' => 'required|string|max:25|unique:purchase_orders',
            'supplier_id' => 'required|exists:suppliers,id',
            'feed_type_id' => 'required|exists:feed_types,id',
            'purchase_unit_id' => 'required|exists:purchase_units,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'actual_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'purchase_order_status_id' => 'required|exists:purchase_order_statuses,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        PurchaseOrder::create($validated);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'feedType', 'purchaseUnit', 'status']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $feedTypes = FeedType::all();
        $purchaseUnits = PurchaseUnit::all();
        $statuses = PurchaseOrderStatus::all();

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'feedTypes', 'purchaseUnits', 'statuses'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'purchase_order_no' => 'required|string|max:25|unique:purchase_orders,purchase_order_no,'.$purchaseOrder->id,
            'supplier_id' => 'required|exists:suppliers,id',
            'feed_type_id' => 'required|exists:feed_types,id',
            'purchase_unit_id' => 'required|exists:purchase_units,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'actual_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'purchase_order_status_id' => 'required|exists:purchase_order_statuses,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $purchaseOrder->update($validated);

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }
}
