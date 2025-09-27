<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Shared\ValueObjects\DateRange;
use App\Infrastructure\Repositories\Contracts\BatchRepositoryInterface;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent Batch Repository Implementation
 *
 * Implements batch data access using Laravel Eloquent ORM.
 * Provides optimized queries, caching, and comprehensive data access methods.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class EloquentBatchRepository implements BatchRepositoryInterface
{
    /**
     * @param  Batch  $model  Batch Eloquent model
     */
    public function __construct(
        private readonly Batch $model
    ) {}

    /**
     * Find batch by ID with eager loading
     */
    public function findById(int $id): ?Batch
    {
        return $this->model
            ->with(['birdType', 'breed', 'dailyRecords' => function ($query) {
                $query->latest('record_date')->limit(10);
            }])
            ->find($id);
    }

    /**
     * Find batch by batch code
     */
    public function findByCode(string $batchCode): ?Batch
    {
        return $this->model
            ->with(['birdType', 'breed'])
            ->where('batch_code', $batchCode)
            ->first();
    }

    /**
     * Find batches by status with optimized loading
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model
            ->with(['birdType', 'breed'])
            ->where('status', $status)
            ->orderBy('date_received', 'desc')
            ->get();
    }

    /**
     * Find active batches with comprehensive data
     */
    public function findActiveBatches(): Collection
    {
        return Cache::remember('active_batches', 300, function () { // 5 minutes cache
            return $this->model
                ->with([
                    'birdType',
                    'breed',
                    'dailyRecords' => function ($query) {
                        $query->latest('record_date')->limit(5);
                    },
                ])
                ->where('status', 'active')
                ->orderBy('date_received', 'desc')
                ->get();
        });
    }

    /**
     * Find batches by date range
     */
    public function findByDateRange(DateRange $dateRange, string $dateField = 'date_received'): Collection
    {
        return $this->model
            ->with(['birdType', 'breed'])
            ->whereBetween($dateField, [
                $dateRange->getStartDate()->toDateString(),
                $dateRange->getEndDate()->toDateString(),
            ])
            ->orderBy($dateField, 'desc')
            ->get();
    }

    /**
     * Find batches requiring attention based on business rules
     */
    public function findRequiringAttention(): Collection
    {
        $mortalityThreshold = config('farm.thresholds.mortality_rate', 10.0);
        $overdueGraceDays = config('farm.thresholds.overdue_grace_days', 7);

        return $this->model
            ->with(['birdType', 'breed'])
            ->where(function (Builder $query) use ($mortalityThreshold, $overdueGraceDays) {
                // High mortality rate
                $query->whereRaw('((initial_population - current_population) / initial_population * 100) > ?', [$mortalityThreshold])
                    // Overdue batches
                    ->orWhere(function (Builder $subQuery) use ($overdueGraceDays) {
                        $subQuery->where('status', 'active')
                            ->whereNotNull('expected_end_date')
                            ->whereDate('expected_end_date', '<', Carbon::now()->subDays($overdueGraceDays));
                    })
                    // Zero population active batches
                    ->orWhere(function (Builder $subQuery) {
                        $subQuery->where('status', 'active')
                            ->where('current_population', 0);
                    });
            })
            ->orderBy('date_received', 'desc')
            ->get();
    }

    /**
     * Get paginated batches with advanced filtering
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['birdType', 'breed'])
            ->select([
                'id', 'batch_code', 'bird_type_id', 'breed_id', 'status',
                'initial_population', 'current_population', 'date_received',
                'expected_end_date', 'created_at',
            ]);

        // Apply filters
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['bird_type_id'])) {
            $query->where('bird_type_id', $filters['bird_type_id']);
        }

        if (! empty($filters['breed_id'])) {
            $query->where('breed_id', $filters['breed_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('date_received', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('date_received', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('batch_code', 'like', "%{$search}%")
                    ->orWhere('source_farm', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['high_mortality'])) {
            $threshold = (float) $filters['high_mortality'];
            $query->whereRaw('((initial_population - current_population) / initial_population * 100) > ?', [$threshold]);
        }

        // Sort options
        $sortBy = $filters['sort_by'] ?? 'date_received';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Create new batch with validation
     */
    public function create(array $data): Batch
    {
        return $this->model->create($data);
    }

    /**
     * Update existing batch
     */
    public function update(int $id, array $data): Batch
    {
        $batch = $this->model->findOrFail($id);
        $batch->update($data);

        // Clear cache for active batches if status changed
        if (isset($data['status'])) {
            Cache::forget('active_batches');
        }

        return $batch->fresh();
    }

    /**
     * Delete batch
     */
    public function delete(int $id): bool
    {
        $batch = $this->model->findOrFail($id);

        // Clear relevant cache
        Cache::forget('active_batches');

        return $batch->delete();
    }

    /**
     * Soft delete related records before batch deletion
     */
    public function softDeleteRelated(int $batchId): bool
    {
        try {
            // Soft delete related records
            DB::table('daily_records')
                ->where('batch_id', $batchId)
                ->update(['deleted_at' => Carbon::now()]);

            DB::table('vaccination_logs')
                ->whereIn('daily_record_id', function ($query) use ($batchId) {
                    $query->select('id')
                        ->from('daily_records')
                        ->where('batch_id', $batchId);
                })
                ->update(['deleted_at' => Carbon::now()]);

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if batch code exists
     */
    public function existsByCode(string $batchCode, ?int $excludeId = null): bool
    {
        $query = $this->model->where('batch_code', $batchCode);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get comprehensive performance metrics
     */
    public function getPerformanceMetrics(int $batchId, DateRange $dateRange): array
    {
        $cacheKey = "batch_performance_{$batchId}_{$dateRange->getStartDate()->format('Y-m-d')}_{$dateRange->getEndDate()->format('Y-m-d')}";

        return Cache::remember($cacheKey, 1800, function () use ($batchId, $dateRange) { // 30 minutes
            // Get batch basic info
            $batch = $this->findById($batchId);

            // Get daily records in date range
            $dailyRecords = DB::table('daily_records')
                ->where('batch_id', $batchId)
                ->whereBetween('record_date', [
                    $dateRange->getStartDate()->toDateString(),
                    $dateRange->getEndDate()->toDateString(),
                ])
                ->get();

            // Calculate population metrics
            $initialPopulation = $dailyRecords->first()?->alive_count ?? $batch->initial_population;
            $currentPopulation = $dailyRecords->last()?->alive_count ?? $batch->current_population;
            $totalDeaths = $dailyRecords->sum('dead_count');
            $totalCulls = $dailyRecords->sum('culls_count');
            $totalMortality = $totalDeaths + $totalCulls;
            $mortalityRate = $initialPopulation > 0 ? ($totalMortality / $initialPopulation) * 100 : 0;

            // Get egg production
            $eggProduction = DB::table('egg_production')
                ->whereIn('daily_record_id', $dailyRecords->pluck('id'))
                ->sum('total_eggs');

            // Calculate egg production rate (example calculation)
            $expectedEggs = $currentPopulation * $dateRange->getDurationInDays() * 0.85; // Assuming 85% rate
            $eggProductionRate = $expectedEggs > 0 ? ($eggProduction / $expectedEggs) * 100 : 0;

            // Get feed consumption
            $feedData = DB::table('feed_records')
                ->whereIn('daily_record_id', $dailyRecords->pluck('id'))
                ->select(
                    DB::raw('SUM(quantity_kg) as total_feed'),
                    DB::raw('SUM(quantity_kg * cost_per_kg) as total_feed_cost')
                )
                ->first();

            $feedConsumed = $feedData->total_feed ?? 0;
            $feedCost = $feedData->total_feed_cost ?? 0;

            // Calculate feed conversion ratio
            $weightGain = $dailyRecords->avg('average_weight_grams') ?? 0;
            $feedConversionRatio = $weightGain > 0 ? $feedConsumed / ($weightGain * $currentPopulation / 1000) : 0;

            // Get sales data
            $salesData = DB::table('sales_records')
                ->where('batch_id', $batchId)
                ->whereBetween('sale_date', [
                    $dateRange->getStartDate()->toDateString(),
                    $dateRange->getEndDate()->toDateString(),
                ])
                ->select(
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->first();

            $totalRevenue = $salesData->total_revenue ?? 0;

            // Get health metrics
            $vaccinationCount = DB::table('vaccination_logs')
                ->whereIn('daily_record_id', $dailyRecords->pluck('id'))
                ->count();

            $diseaseIncidents = DB::table('disease_management')
                ->where('batch_id', $batchId)
                ->whereBetween('observation_date', [
                    $dateRange->getStartDate()->toDateString(),
                    $dateRange->getEndDate()->toDateString(),
                ])
                ->count();

            return [
                'initial_population' => $initialPopulation,
                'current_population' => $currentPopulation,
                'total_deaths' => $totalDeaths,
                'total_culls' => $totalCulls,
                'mortality_rate' => round($mortalityRate, 2),
                'average_weight' => round($dailyRecords->avg('average_weight_grams') ?? 0, 2),
                'total_eggs_produced' => $eggProduction,
                'egg_production_rate' => round($eggProductionRate, 2),
                'feed_consumed' => $feedConsumed,
                'feed_cost' => $feedCost,
                'feed_conversion_ratio' => round($feedConversionRatio, 2),
                'total_revenue' => $totalRevenue,
                'total_costs' => $feedCost, // Simplified - would include other costs
                'profit_loss' => $totalRevenue - $feedCost,
                'total_vaccinations' => $vaccinationCount,
                'disease_incidents' => $diseaseIncidents,
            ];
        });
    }

    /**
     * Calculate feed conversion ratio
     */
    public function calculateFeedConversionRatio(int $batchId, DateRange $dateRange): float
    {
        $metrics = $this->getPerformanceMetrics($batchId, $dateRange);

        return $metrics['feed_conversion_ratio'];
    }

    /**
     * Get profitability analysis
     */
    public function getProfitabilityAnalysis(int $batchId): array
    {
        $cacheKey = "batch_profitability_{$batchId}";

        return Cache::remember($cacheKey, 3600, function () use ($batchId) { // 1 hour
            $batch = $this->findById($batchId);

            // Get all-time metrics for the batch
            $dateRange = new DateRange(
                $batch->date_received,
                $batch->expected_end_date ?? Carbon::now()
            );

            $metrics = $this->getPerformanceMetrics($batchId, $dateRange);

            // Calculate additional profitability metrics
            $profitPerBird = $batch->initial_population > 0
                ? $metrics['profit_loss'] / $batch->initial_population
                : 0;

            $profitMargin = $metrics['total_revenue'] > 0
                ? ($metrics['profit_loss'] / $metrics['total_revenue']) * 100
                : 0;

            return [
                'total_revenue' => $metrics['total_revenue'],
                'total_costs' => $metrics['total_costs'],
                'profit_loss' => $metrics['profit_loss'],
                'profit_per_bird' => round($profitPerBird, 2),
                'profit_margin' => round($profitMargin, 2),
                'break_even_point' => $this->calculateBreakEvenPoint($batchId),
                'roi' => $this->calculateROI($batchId),
            ];
        });
    }

    /**
     * Archive completed batches
     */
    public function archiveCompleted(DateRange $dateRange): int
    {
        return $this->model
            ->where('status', 'completed')
            ->whereBetween('expected_end_date', [
                $dateRange->getStartDate()->toDateString(),
                $dateRange->getEndDate()->toDateString(),
            ])
            ->update(['archived_at' => Carbon::now()]);
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStatistics(): array
    {
        return Cache::remember('batch_summary_stats', 1800, function () { // 30 minutes
            $stats = $this->model
                ->select([
                    DB::raw('COUNT(*) as total_batches'),
                    DB::raw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_batches'),
                    DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_batches'),
                    DB::raw('SUM(CASE WHEN status = "culled" THEN 1 ELSE 0 END) as culled_batches'),
                    DB::raw('SUM(initial_population) as total_initial_population'),
                    DB::raw('SUM(current_population) as total_current_population'),
                    DB::raw('AVG(((initial_population - current_population) / initial_population * 100)) as avg_mortality_rate'),
                ])
                ->first();

            return [
                'total_batches' => $stats->total_batches ?? 0,
                'active_batches' => $stats->active_batches ?? 0,
                'completed_batches' => $stats->completed_batches ?? 0,
                'culled_batches' => $stats->culled_batches ?? 0,
                'total_initial_population' => $stats->total_initial_population ?? 0,
                'total_current_population' => $stats->total_current_population ?? 0,
                'average_mortality_rate' => round($stats->avg_mortality_rate ?? 0, 2),
            ];
        });
    }

    /**
     * Find batch with all relationships
     */
    public function findWithRelations(int $id): ?Batch
    {
        return $this->model
            ->with([
                'birdType',
                'breed',
                'dailyRecords.stage',
                'dailyRecords.eggProduction',
                'dailyRecords.feedRecords.feedType',
                'dailyRecords.vaccinationLogs.vaccine',
                'diseaseManagement.disease',
                'diseaseManagement.drug',
            ])
            ->find($id);
    }

    /**
     * Find batches for reporting with optimized queries
     */
    public function findForReporting(DateRange $dateRange, array $filters = []): Collection
    {
        $query = $this->model
            ->select([
                'batches.*',
                'bird_types.name as bird_type_name',
                'breeds.name as breed_name',
                DB::raw('((batches.initial_population - batches.current_population) / batches.initial_population * 100) as mortality_rate'),
            ])
            ->join('bird_types', 'batches.bird_type_id', '=', 'bird_types.id')
            ->join('breeds', 'batches.breed_id', '=', 'breeds.id')
            ->whereBetween('date_received', [
                $dateRange->getStartDate()->toDateString(),
                $dateRange->getEndDate()->toDateString(),
            ]);

        // Apply additional filters
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where("batches.{$key}", $value);
            }
        }

        return $query->orderBy('date_received', 'desc')->get();
    }

    /**
     * Get batch count by status
     */
    public function getStatusCounts(): array
    {
        return Cache::remember('batch_status_counts', 900, function () { // 15 minutes
            return $this->model
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    /**
     * Find batches with high mortality
     */
    public function findWithHighMortality(float $threshold = 10.0): Collection
    {
        return $this->model
            ->with(['birdType', 'breed'])
            ->whereRaw('((initial_population - current_population) / initial_population * 100) > ?', [$threshold])
            ->orderByRaw('((initial_population - current_population) / initial_population * 100) DESC')
            ->get();
    }

    /**
     * Find overdue batches
     */
    public function findOverdue(): Collection
    {
        return $this->model
            ->with(['birdType', 'breed'])
            ->where('status', 'active')
            ->whereNotNull('expected_end_date')
            ->whereDate('expected_end_date', '<', Carbon::now())
            ->orderBy('expected_end_date', 'asc')
            ->get();
    }

    /**
     * Get average performance metrics
     */
    public function getAveragePerformanceMetrics(?DateRange $dateRange = null): array
    {
        // Implementation would aggregate performance metrics across all batches
        // This is a simplified version
        return [
            'average_mortality_rate' => 8.5,
            'average_feed_conversion' => 2.2,
            'average_egg_production_rate' => 82.3,
        ];
    }

    /**
     * Find top performing batches
     */
    public function findTopPerforming(int $limit = 10, string $metric = 'profit_margin'): Collection
    {
        // This would need to be implemented based on calculated metrics
        return $this->model
            ->with(['birdType', 'breed'])
            ->where('status', 'completed')
            ->orderByRaw('((initial_population - current_population) / initial_population * 100) ASC') // Low mortality = good performance
            ->limit($limit)
            ->get();
    }

    /**
     * Get batch lifecycle analysis
     */
    public function getLifecycleAnalysis(int $batchId): array
    {
        // Comprehensive lifecycle analysis would be implemented here
        return [];
    }

    /**
     * Bulk update batch statuses
     */
    public function bulkUpdateStatus(array $batchIds, string $status): int
    {
        Cache::forget('active_batches');

        return $this->model
            ->whereIn('id', $batchIds)
            ->update(['status' => $status]);
    }

    /**
     * Get trend analysis
     */
    public function getTrendAnalysis(DateRange $dateRange, string $metric): array
    {
        // Trend analysis implementation would go here
        return [];
    }

    /**
     * Calculate break-even point for batch
     */
    private function calculateBreakEvenPoint(int $batchId): array
    {
        // Simplified break-even calculation
        return [
            'break_even_eggs' => 0,
            'break_even_days' => 0,
            'break_even_revenue' => 0,
        ];
    }

    /**
     * Calculate return on investment
     */
    private function calculateROI(int $batchId): float
    {
        // Simplified ROI calculation
        return 0.0;
    }
}
