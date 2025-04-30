<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderStatus;
use Illuminate\Http\Request;

class PurchaseOrderStatusController extends Controller
{
    public function index()
    {
        $statuses = PurchaseOrderStatus::latest()->paginate(10);
        return view('purchase-order-statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('purchase-order-statuses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:purchase_order_statuses',
            'description' => 'nullable|string|max:255',
        ]);

        PurchaseOrderStatus::create($validated);

        return redirect()->route('purchase-order-statuses.index')
            ->with('success', 'Purchase order status created successfully.');
    }

    public function show(PurchaseOrderStatus $purchaseOrderStatus)
    {
        return view('purchase-order-statuses.show', compact('purchaseOrderStatus'));
    }

    public function edit(PurchaseOrderStatus $purchaseOrderStatus)
    {
        return view('purchase-order-statuses.edit', compact('purchaseOrderStatus'));
    }

    public function update(Request $request, PurchaseOrderStatus $purchaseOrderStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:purchase_order_statuses,name,'.$purchaseOrderStatus->id,
            'description' => 'nullable|string|max:255',
        ]);

        $purchaseOrderStatus->update($validated);

        return redirect()->route('purchase-order-statuses.index')
            ->with('success', 'Purchase order status updated successfully.');
    }

    public function destroy(PurchaseOrderStatus $purchaseOrderStatus)
    {
        $purchaseOrderStatus->delete();

        return redirect()->route('purchase-order-statuses.index')
            ->with('success', 'Purchase order status deleted successfully.');
    }
}
