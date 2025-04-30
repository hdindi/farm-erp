<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\User;
use App\Models\FeedType; // Added
use App\Models\Disease;  // Added
use App\Models\Vaccine;  // Added
use App\Models\VDailyEggSummary;
use App\Models\VFarmKpi;
use App\Models\VSalesBySalesperson;
use App\Models\VwBatchSummary;
use App\Models\VwBatchDailyPerformance; // Added
use App\Models\VwBatchFeedConsumption; // Added
use App\Models\VwBatchDiseaseManagement; // Added
use App\Models\VwBatchVaccinationDetails; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // If needed for complex aggregations not in views

class ReportController extends Controller
{
    /**
     * Display the Farm KPIs report.
     */
    public function farmKpis()
    {
        $kpis = VFarmKpi::first();
        return view('reports.farm-kpis', compact('kpis'));
    }

    /**
     * Display the Batch Summary report.
     */
    public function batchSummary(Request $request)
    {
        // Start query on the view model
        $query = VwBatchSummary::query()
            ->select('vw_batch_summary.*', 'batches.id as batch_id') // Select all from view + batch ID
            ->join('batches', 'vw_batch_summary.batch_code', '=', 'batches.batch_code'); // Join based on batch_code to get ID

        // Apply filters based on request input
        if ($request->filled('status') && in_array($request->status, ['active', 'completed', 'culled'])) {
            $query->where('vw_batch_summary.status', $request->status); // Specify table/view for status
        }

        // Get filtered data, ordered by batch_code
        $summaries = $query->orderBy('vw_batch_summary.batch_code')->get(); // Specify table/view for batch_code

        return view('reports.batch-summary', compact('summaries'));
    }

    /**
     * Display the Daily Egg Summary report.
     */
    public function dailyEggSummary(Request $request)
    {
        // Validate optional date filters
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        // Start query on the view model
        $query = VDailyEggSummary::query();

        // Apply date filters based on request input
        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        } else if (!$request->filled('date_from')) { // Apply default only if no dates are provided
            // Default to last 30 days if no date range given
            $query->where('record_date', '>=', Carbon::today()->subDays(29));
        }
        // If only date_from is provided, it will filter from that date onwards.
        // If only date_to is provided, it will filter up to that date.

        // Get filtered data, ordered by date for consistency
        $eggSummaries = $query->orderBy('record_date', 'asc')->get();

        // Prepare data specifically for the chart
        $chartLabels = [];
        $chartEggData = [];
        // Ensure we have summaries before trying to create chart data
        if ($eggSummaries->isNotEmpty()) {
            // Pluck total eggs, keyed by the record_date
            $chartData = $eggSummaries->pluck('total_eggs_collected', 'record_date');

            // Map the keys (dates) to the desired label format (e.g., "Apr 28")
            $chartLabels = $chartData->keys()
                ->map(fn($date) => Carbon::parse($date)->format('M d'))
                ->toArray();
            // Get the corresponding egg count values
            $chartEggData = $chartData->values()->toArray();
        }

        // Pass both the full summary data (for the table) and
        // the prepared chart data to the view
        return view('reports.daily-egg-summary', compact(
            'eggSummaries',
            'chartLabels',
            'chartEggData'
        ));
    }
    /**
     * Display the Sales by Salesperson report.
     */
    public function salesBySalesperson(Request $request)
    {
        $salesData = VSalesBySalesperson::orderBy('total_sales_amount', 'desc')->get();
        return view('reports.sales-by-salesperson', compact('salesData'));
    }

    /**
     * Display the Batch Performance report (Based on vw_batch_daily_performance).
     */
    public function batchPerformance(Request $request)
    {
        $request->validate([
            'batch_id' => 'nullable|exists:batches,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = VwBatchDailyPerformance::query();

        if ($request->filled('batch_id')) {
            // Find the batch code to filter the view
            $batchCode = Batch::find($request->batch_id)?->batch_code;
            if ($batchCode) {
                $query->where('batch_code', $batchCode);
            }
        }
        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }

        $performanceData = $query->orderBy('record_date', 'asc')->get();
        $batches = Batch::where('status', 'active')->orderBy('batch_code')->get();

        // Prepare chart data only if a single batch is selected
        $chartLabels = [];
        $chartMortalityData = [];
        if ($request->filled('batch_id') && $performanceData->isNotEmpty()) {
            $chartData = $performanceData->pluck('daily_mortality_rate_percent', 'record_date');
            $chartLabels = $chartData->keys()->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray();
            $chartMortalityData = $chartData->values()->toArray();
        }

        return view('reports.batch-performance', compact(
            'performanceData',
            'batches',
            'chartLabels',
            'chartMortalityData'
        ));
    }

    /**
     * Display the Feed Consumption report (Based on vw_batch_feed_consumption).
     */
    public function feedConsumptionReport(Request $request)
    {
        $request->validate([
            'batch_id' => 'nullable|exists:batches,id',
            'feed_type_id' => 'nullable|exists:feed_types,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = VwBatchFeedConsumption::query();

        if ($request->filled('batch_id')) {
            $batchCode = Batch::find($request->batch_id)?->batch_code;
            if ($batchCode) {
                $query->where('batch_code', $batchCode);
            }
        }
        if ($request->filled('feed_type_id')) {
            $feedTypeName = FeedType::find($request->feed_type_id)?->name;
            if ($feedTypeName) {
                $query->where('feed_type', $feedTypeName);
            }
        }
        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }

        $feedData = $query->orderBy('record_date', 'asc')->get();

        // Data for filters
        $batches = Batch::where('status', 'active')->orderBy('batch_code')->get();
        $feedTypes = FeedType::orderBy('name')->get();

        // Prepare chart data (e.g., total consumption per day)
        $chartData = $feedData->groupBy(function($item) {
            return Carbon::parse($item->record_date)->format('Y-m-d');
        })->map(function ($group) {
            return $group->sum('quantity_kg');
        });

        $chartLabels = $chartData->keys()->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray();
        $chartFeedData = $chartData->values()->toArray();


        return view('reports.feed-consumption', compact(
            'feedData',
            'batches',
            'feedTypes',
            'chartLabels',
            'chartFeedData'
        ));
    }

    /**
     * Display the Disease Management report (Based on vw_batch_disease_management).
     */
    public function diseaseManagementReport(Request $request)
    {
        $request->validate([
            'batch_id' => 'nullable|exists:batches,id',
            'disease_id' => 'nullable|exists:diseases,id',
            'date_from' => 'nullable|date', // Filter by observation_date
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = VwBatchDiseaseManagement::query();

        if ($request->filled('batch_id')) {
            $batchCode = Batch::find($request->batch_id)?->batch_code;
            if ($batchCode) {
                $query->where('batch_code', $batchCode);
            }
        }
        if ($request->filled('disease_id')) {
            $diseaseName = Disease::find($request->disease_id)?->name;
            if ($diseaseName) {
                $query->where('disease_name', $diseaseName);
            }
        }
        if ($request->filled('date_from')) {
            $query->where('observation_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('observation_date', '<=', $request->date_to);
        }

        $diseaseData = $query->orderBy('observation_date', 'desc')->get();

        // Data for filters
        $batches = Batch::where('status', 'active')->orderBy('batch_code')->get();
        $diseases = Disease::orderBy('name')->get();

        return view('reports.disease-management-report', compact(
            'diseaseData',
            'batches',
            'diseases'
        ));
    }

    /**
     * Display the Vaccination report (Based on vw_batch_vaccination_details).
     */
    public function vaccinationReport(Request $request)
    {
        $request->validate([
            'batch_id' => 'nullable|exists:batches,id',
            'vaccine_id' => 'nullable|exists:vaccines,id',
            'date_from' => 'nullable|date', // Filter by record_date (from daily record)
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = VwBatchVaccinationDetails::query();

        if ($request->filled('batch_id')) {
            $batchCode = Batch::find($request->batch_id)?->batch_code;
            if ($batchCode) {
                $query->where('batch_code', $batchCode);
            }
        }
        if ($request->filled('vaccine_id')) {
            $vaccineName = Vaccine::find($request->vaccine_id)?->name;
            if ($vaccineName) {
                $query->where('vaccine_name', $vaccineName);
            }
        }
        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }

        $vaccinationData = $query->orderBy('record_date', 'desc')->get();

        // Data for filters
        $batches = Batch::where('status', 'active')->orderBy('batch_code')->get();
        $vaccines = Vaccine::orderBy('name')->get();

        return view('reports.vaccination-report', compact(
            'vaccinationData',
            'batches',
            'vaccines'
        ));
    }

}
