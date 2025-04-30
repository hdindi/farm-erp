<?php

namespace App\Http\Controllers;

use App\Models\SalesPrice;
use App\Models\SalesRecord;
use App\Models\SalesTeam;
use Illuminate\Http\Request;

class SalesRecordController extends Controller
{
    public function index()
    {
        $salesRecords = SalesRecord::with(['salesPerson', 'salesPrice'])
            ->latest()
            ->paginate(10);

        return view('sales-records.index', compact('salesRecords'));
    }

    public function create()
    {
        $salesPeople = SalesTeam::where('is_active', true)->get();
        $salesPrices = SalesPrice::where('status', 'active')->get();

        return view('sales-records.create', compact('salesPeople', 'salesPrices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_person_id' => 'required|exists:sales_teams,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'sales_price_id' => 'required|exists:sales_prices,id',
            'quantity' => 'required|numeric|min:0.01',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        SalesRecord::create($validated);

        return redirect()->route('sales-records.index')
            ->with('success', 'Sales record created successfully.');
    }

    public function show(SalesRecord $salesRecord)
    {
        $salesRecord->load(['salesPerson', 'salesPrice']);
        return view('sales-records.show', compact('salesRecord'));
    }

    public function edit(SalesRecord $salesRecord)
    {
        $salesPeople = SalesTeam::where('is_active', true)->get();
        $salesPrices = SalesPrice::where('status', 'active')->get();

        return view('sales-records.edit', compact('salesRecord', 'salesPeople', 'salesPrices'));
    }

    public function update(Request $request, SalesRecord $salesRecord)
    {
        $validated = $request->validate([
            'sales_person_id' => 'required|exists:sales_teams,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'sales_price_id' => 'required|exists:sales_prices,id',
            'quantity' => 'required|numeric|min:0.01',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $salesRecord->update($validated);

        return redirect()->route('sales-records.index')
            ->with('success', 'Sales record updated successfully.');
    }

    public function destroy(SalesRecord $salesRecord)
    {
        $salesRecord->delete();

        return redirect()->route('sales-records.index')
            ->with('success', 'Sales record deleted successfully.');
    }
}
