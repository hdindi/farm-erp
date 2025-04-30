<?php

namespace App\Http\Controllers;

use App\Models\DailyRecord;
use App\Models\VaccinationLog;
use App\Models\Vaccine;
use Illuminate\Http\Request;

class VaccinationLogController extends Controller
{
    public function index()
    {
        $vaccinationLogs = VaccinationLog::with(['dailyRecord.batch', 'vaccine'])
            ->latest()
            ->paginate(10);

        return view('vaccination-logs.index', compact('vaccinationLogs'));
    }

    public function create()
    {
        $dailyRecords = DailyRecord::with('batch')
            ->whereHas('batch', function($query) {
                $query->where('status', 'active');
            })
            ->get();

        $vaccines = Vaccine::all();
        return view('vaccination-logs.create', compact('dailyRecords', 'vaccines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'daily_record_id' => 'required|exists:daily_records,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'birds_vaccinated' => 'required|integer|min:1',
            'administered_by' => 'nullable|string|max:100',
            'next_due_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate that birds vaccinated doesn't exceed alive count
        $dailyRecord = DailyRecord::find($validated['daily_record_id']);
        if ($validated['birds_vaccinated'] > $dailyRecord->alive_count) {
            return back()->withErrors([
                'birds_vaccinated' => 'Number of birds vaccinated cannot exceed alive count.'
            ])->withInput();
        }

        VaccinationLog::create($validated);

        return redirect()->route('vaccination-logs.index')
            ->with('success', 'Vaccination log created successfully.');
    }

    public function show(VaccinationLog $vaccinationLog)
    {
        $vaccinationLog->load(['dailyRecord.batch', 'vaccine']);
        return view('vaccination-logs.show', compact('vaccinationLog'));
    }

    public function edit(VaccinationLog $vaccinationLog)
    {
        $dailyRecords = DailyRecord::with('batch')
            ->whereHas('batch', function($query) {
                $query->where('status', 'active');
            })
            ->get();

        $vaccines = Vaccine::all();
        return view('vaccination-logs.edit', compact('vaccinationLog', 'dailyRecords', 'vaccines'));
    }

    public function update(Request $request, VaccinationLog $vaccinationLog)
    {
        $validated = $request->validate([
            'daily_record_id' => 'required|exists:daily_records,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'birds_vaccinated' => 'required|integer|min:1',
            'administered_by' => 'nullable|string|max:100',
            'next_due_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate that birds vaccinated doesn't exceed alive count
        $dailyRecord = DailyRecord::find($validated['daily_record_id']);
        if ($validated['birds_vaccinated'] > $dailyRecord->alive_count) {
            return back()->withErrors([
                'birds_vaccinated' => 'Number of birds vaccinated cannot exceed alive count.'
            ])->withInput();
        }

        $vaccinationLog->update($validated);

        return redirect()->route('vaccination-logs.index')
            ->with('success', 'Vaccination log updated successfully.');
    }

    public function destroy(VaccinationLog $vaccinationLog)
    {
        $vaccinationLog->delete();

        return redirect()->route('vaccination-logs.index')
            ->with('success', 'Vaccination log deleted successfully.');
    }
}
