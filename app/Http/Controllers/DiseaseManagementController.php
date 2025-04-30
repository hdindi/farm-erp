<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Disease;
use App\Models\DiseaseManagement;
use App\Models\Drug;
use Illuminate\Http\Request;

class DiseaseManagementController extends Controller
{
    public function index()
    {
        $diseaseManagements = DiseaseManagement::with(['batch', 'disease', 'drug'])
            ->latest()
            ->paginate(10);

        return view('disease-management.index', compact('diseaseManagements'));
    }

    public function create()
    {
        $batches = Batch::where('status', 'active')->get();
        $diseases = Disease::all();
        $drugs = Drug::all();
        return view('disease-management.create', compact('batches', 'diseases', 'drugs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'disease_id' => 'required|exists:diseases,id',
            'drug_id' => 'nullable|exists:drugs,id',
            'observation_date' => 'required|date',
            'affected_count' => 'nullable|integer|min:0',
            'treatment_start_date' => 'nullable|date',
            'treatment_end_date' => 'nullable|date|after_or_equal:treatment_start_date',
            'notes' => 'nullable|string',
        ]);

        DiseaseManagement::create($validated);

        return redirect()->route('disease-management.index')
            ->with('success', 'Disease management record created successfully.');
    }

    public function show(DiseaseManagement $diseaseManagement)
    {
        $diseaseManagement->load(['batch', 'disease', 'drug']);
        return view('disease-management.show', compact('diseaseManagement'));
    }

    public function edit(DiseaseManagement $diseaseManagement)
    {
        $batches = Batch::where('status', 'active')->get();
        $diseases = Disease::all();
        $drugs = Drug::all();
        return view('disease-management.edit', compact('diseaseManagement', 'batches', 'diseases', 'drugs'));
    }

    public function update(Request $request, DiseaseManagement $diseaseManagement)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'disease_id' => 'required|exists:diseases,id',
            'drug_id' => 'nullable|exists:drugs,id',
            'observation_date' => 'required|date',
            'affected_count' => 'nullable|integer|min:0',
            'treatment_start_date' => 'nullable|date',
            'treatment_end_date' => 'nullable|date|after_or_equal:treatment_start_date',
            'notes' => 'nullable|string',
        ]);

        $diseaseManagement->update($validated);

        return redirect()->route('disease-management.index')
            ->with('success', 'Disease management record updated successfully.');
    }

    public function destroy(DiseaseManagement $diseaseManagement)
    {
        $diseaseManagement->delete();

        return redirect()->route('disease-management.index')
            ->with('success', 'Disease management record deleted successfully.');
    }
}
