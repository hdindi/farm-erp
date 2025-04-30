<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\VaccinationLog;
use App\Models\Vaccine;
use App\Models\VaccineSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VaccineScheduleController extends Controller
{
    public function index()
    {
        $vaccineSchedules = VaccineSchedule::with(['batch', 'vaccine', 'vaccinationLog'])
            ->latest()
            ->paginate(10);

        return view('vaccine-schedule.index', compact('vaccineSchedules'));
    }

    public function create()
    {
        $batches = Batch::where('status', 'active')->get();
        $vaccines = Vaccine::all();
        $vaccinationLogs = VaccinationLog::all();
        return view('vaccine-schedule.create', compact('batches', 'vaccines', 'vaccinationLogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'date_due' => 'required|date',
            'status' => 'required|in:administered,scheduled,missed',
            'administered_date' => 'nullable|date|required_if:status,administered',
            'vaccination_log_id' => 'nullable|exists:vaccination_logs,id|required_if:status,administered',
        ]);

        try {
            DB::beginTransaction();

            $schedule = VaccineSchedule::create($validated);

            // If status is administered, update the vaccination log
            if ($validated['status'] === 'administered' && isset($validated['vaccination_log_id'])) {
                $log = VaccinationLog::find($validated['vaccination_log_id']);
                if ($log) {
                    $log->update(['next_due_date' => $validated['date_due']]);
                }
            }

            DB::commit();

            return redirect()->route('vaccine-schedule.index')
                ->with('success', 'Vaccine schedule created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create vaccine schedule: ' . $e->getMessage());
        }
    }

    public function show(VaccineSchedule $vaccineSchedule)
    {
        return view('vaccine-schedule.show', compact('vaccineSchedule'));
    }

    public function edit(VaccineSchedule $vaccineSchedule)
    {
        $batches = Batch::where('status', 'active')->get();
        $vaccines = Vaccine::all();
        $vaccinationLogs = VaccinationLog::all();
        return view('vaccine-schedule.edit', compact('vaccineSchedule', 'batches', 'vaccines', 'vaccinationLogs'));
    }

    public function update(Request $request, VaccineSchedule $vaccineSchedule)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'date_due' => 'required|date',
            'status' => 'required|in:administered,scheduled,missed',
            'administered_date' => 'nullable|date|required_if:status,administered',
            'vaccination_log_id' => 'nullable|exists:vaccination_logs,id|required_if:status,administered',
        ]);

        try {
            DB::beginTransaction();

            $vaccineSchedule->update($validated);

            // If status is administered, update the vaccination log
            if ($validated['status'] === 'administered' && isset($validated['vaccination_log_id'])) {
                $log = VaccinationLog::find($validated['vaccination_log_id']);
                if ($log) {
                    $log->update(['next_due_date' => $validated['date_due']]);
                }
            }

            DB::commit();

            return redirect()->route('vaccine-schedule.index')
                ->with('success', 'Vaccine schedule updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update vaccine schedule: ' . $e->getMessage());
        }
    }

    public function destroy(VaccineSchedule $vaccineSchedule)
    {
        try {
            $vaccineSchedule->delete();
            return redirect()->route('vaccine-schedule.index')
                ->with('success', 'Vaccine schedule deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete vaccine schedule: ' . $e->getMessage());
        }
    }

    public function markAdministered(Request $request, VaccineSchedule $vaccineSchedule)
    {
        $validated = $request->validate([
            'administered_date' => 'required|date',
            'vaccination_log_id' => 'required|exists:vaccination_logs,id',
        ]);

        try {
            DB::beginTransaction();

            $vaccineSchedule->update([
                'status' => 'administered',
                'administered_date' => $validated['administered_date'],
                'vaccination_log_id' => $validated['vaccination_log_id'],
            ]);

            // Update the vaccination log with next due date
            $log = VaccinationLog::find($validated['vaccination_log_id']);
            if ($log) {
                $log->update(['next_due_date' => $vaccineSchedule->date_due]);
            }

            DB::commit();

            return redirect()->route('vaccine-schedule.index')
                ->with('success', 'Vaccine schedule marked as administered successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark as administered: ' . $e->getMessage());
        }
    }



    // Add these methods to your VaccineScheduleController

    /**
     * Display a timetable view of all vaccine schedules grouped by batch
     */
    public function timetable()
    {
        // Get all upcoming vaccine schedules grouped by batch
        $batches = Batch::with(['vaccineSchedules' => function($query) {
            $query->where('date_due', '>=', now())
                ->orderBy('date_due')
                ->with('vaccine');
        }])
            ->whereHas('vaccineSchedules', function($query) {
                $query->where('date_due', '>=', now());
            })
            ->where('status', 'active')
            ->get();

        return view('vaccine-schedule.timetable', compact('batches'));
    }

    /**
     * Display a calendar view of all vaccine schedules
     */
    public function calendar()
    {
        // Get all vaccine schedules for the calendar
        $events = VaccineSchedule::with(['batch', 'vaccine'])
            ->where('date_due', '>=', now()->subMonths(1))
            ->get()
            ->map(function ($schedule) {
                return [
                    'title' => $schedule->vaccine->name . ' - ' . $schedule->batch->batch_code,
                    'start' => $schedule->date_due,
                    'end' => $schedule->date_due,
                    'url' => route('vaccine-schedule.show', $schedule->id),
                    'color' => $this->getStatusColor($schedule->status),
                    'extendedProps' => [
                        'status' => $schedule->status,
                        'batch' => $schedule->batch->batch_code,
                        'vaccine' => $schedule->vaccine->name,
                    ],
                ];
            });

        return view('vaccine-schedule.calendar', compact('events'));
    }

    /**
     * Display a dashboard overview of vaccine schedules
     */
    public function dashboard()
    {
        // Upcoming vaccines (next 7 days)
        $upcoming = VaccineSchedule::with(['batch', 'vaccine'])
            ->whereBetween('date_due', [now(), now()->addDays(7)])
            ->orderBy('date_due')
            ->get();

        // Overdue vaccines
        $overdue = VaccineSchedule::with(['batch', 'vaccine'])
            ->where('date_due', '<', now())
            ->where('status', '!=', 'administered')
            ->orderBy('date_due')
            ->get();

        // Recent administrations (last 7 days)
        $recent = VaccineSchedule::with(['batch', 'vaccine'])
            ->where('status', 'administered')
            ->where('administered_date', '>=', now()->subDays(7))
            ->orderBy('administered_date', 'desc')
            ->get();

        return view('vaccine-schedule.dashboard', compact('upcoming', 'overdue', 'recent'));
    }

    /**
     * Helper function to get color based on status
     */
    private function getStatusColor($status)
    {
        return match ($status) {
            'administered' => '#28a745', // green
            'missed' => '#dc3545',       // red
            default => '#17a2b8',        // blue (for scheduled)
        };
    }


}
