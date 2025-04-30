<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\EggProduction;
use App\Models\PurchaseOrder;
use App\Models\SalesRecord;
use App\Models\AuditLog;
use App\Models\DailyRecord;
use App\Models\FeedRecord;
use App\Models\VaccineSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request) // Inject Request
    {
        // --- Validate Date Inputs ---
        $validated = $request->validate([
            'date_from' => 'nullable|date|before_or_equal:date_to',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        // --- Determine Date Range ---
        // Default to 'Today' if no range is provided
        $dateFrom = isset($validated['date_from']) ? Carbon::parse($validated['date_from'])->startOfDay() : Carbon::today();
        $dateTo = isset($validated['date_to']) ? Carbon::parse($validated['date_to'])->endOfDay() : Carbon::today();
        $isDateRange = $request->filled('date_from') || $request->filled('date_to'); // Check if user provided dates

        // --- Existing Non-Date Specific Data ---
        $activeBatchesCount = Batch::where('status', 'active')->count();
        $totalEggsProduced = EggProduction::sum('total_eggs'); // Lifetime total
        $pendingOrdersCount = PurchaseOrder::whereHas('status', function($query) {
            $query->where('name', '!=', 'Completed'); // Adjust if status name differs
        })->count();
        $latestActivities = AuditLog::with(['user'])
            ->latest('event_time')
            ->take(5)
            ->get();
        $batchStatusCounts = Batch::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // --- KPIs Filtered by Date Range ---
        $dailyRecordsInRangeQuery = DailyRecord::whereBetween('record_date', [$dateFrom, $dateTo]);

        $eggsInRange = EggProduction::whereHas('dailyRecord', fn($q) => $q->whereBetween('record_date', [$dateFrom, $dateTo]))->sum('total_eggs');
        $layersInRange = (clone $dailyRecordsInRangeQuery) // Clone the query builder
        ->whereHas('batch.birdType', fn($q) => $q->where('name', 'Layer'))
            ->where('alive_count', '>', 0)
            ->sum('alive_count'); // This sums alive count *within the range*, might need adjustment depending on desired KPI
        $layRateInRange = ($layersInRange > 0 && $dateFrom->isSameDay($dateTo)) // Calculate rate only if single day & layers exist
            ? ($eggsInRange / $layersInRange) * 100
            : null; // Or calculate average rate over the period if desired

        $feedInRange = FeedRecord::whereHas('dailyRecord', fn($q) => $q->whereBetween('record_date', [$dateFrom, $dateTo]))->sum('quantity_kg');

        $mortalityInRangeQuery = (clone $dailyRecordsInRangeQuery)->where('alive_count', '>', 0);
        $totalDeadInRange = $mortalityInRangeQuery->sum('dead_count');
        // Note: Calculating accurate mortality rate over a range is complex without daily start counts.
        // This calculates average daily rate within the period as an approximation.
        $averageDailyMortalityRate = $mortalityInRangeQuery->avg('mortality_rate'); // Assumes mortality_rate is stored daily

        // --- Upcoming/Overdue - Less affected by date filter, but keep logic ---
        $today = Carbon::today(); // Use today for these specifically
        $upcomingVaccinationsCount = VaccineSchedule::where('status', 'scheduled')
            ->whereBetween('date_due', [$today, $today->copy()->addDays(6)])
            ->count();
        $overdueVaccinationsCount = VaccineSchedule::where('status', 'scheduled')
            ->where('date_due', '<', $today)
            ->count();
        $overdueVaccinations = VaccineSchedule::with(['batch','vaccine'])
            ->where('status', 'scheduled')
            ->where('date_due', '<', $today)
            ->orderBy('date_due', 'asc')
            ->take(5)
            ->get();

        // --- Trend Data (Adjust range based on filter, default to 7 days if no filter) ---
        $trendStartDate = $isDateRange ? $dateFrom : Carbon::today()->subDays(6);
        $trendEndDate = $isDateRange ? $dateTo : Carbon::today();

        $eggProductionTrend = EggProduction::join('daily_records', 'egg_production.daily_record_id', '=', 'daily_records.id')
            ->whereBetween('daily_records.record_date', [$trendStartDate, $trendEndDate])
            ->select(DB::raw('DATE(daily_records.record_date) as date'), DB::raw('SUM(total_eggs) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date');
        $eggTrendLabels = $eggProductionTrend->keys()->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray();
        $eggTrendData = $eggProductionTrend->values()->toArray();

        $feedConsumptionTrend = FeedRecord::join('daily_records', 'feed_records.daily_record_id', '=', 'daily_records.id')
            ->whereBetween('daily_records.record_date', [$trendStartDate, $trendEndDate])
            ->select(DB::raw('DATE(daily_records.record_date) as date'), DB::raw('SUM(quantity_kg) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date');
        $feedTrendLabels = $feedConsumptionTrend->keys()->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray();
        $feedTrendData = $feedConsumptionTrend->values()->toArray();

        // --- Recent Sales (Not typically filtered by dashboard date range) ---
        $recentSales = SalesRecord::with(['salesPerson'])
            ->latest('sale_date')
            ->take(5)
            ->get();

        return view('home', compact(
        // Existing (mostly unaffected by date filter)
            'activeBatchesCount',
            'totalEggsProduced',
            'pendingOrdersCount',
            'recentSales',
            'batchStatusCounts',
            'latestActivities',
            // Filtered KPIs
            'eggsInRange', // Renamed
            'layRateInRange', // Renamed & logic adjusted
            'feedInRange', // Renamed
            'averageDailyMortalityRate', // Renamed & logic adjusted
            // Upcoming/Overdue (Based on Today)
            'upcomingVaccinationsCount',
            'overdueVaccinationsCount',
            'overdueVaccinations',
            // Filtered Trend Data
            'eggTrendLabels',
            'eggTrendData',
            'feedTrendLabels',
            'feedTrendData',
            // Pass dates back to view for filter inputs
            'dateFrom',
            'dateTo',
            'isDateRange' // Flag to indicate if a custom range is active
        ));
    }
}
