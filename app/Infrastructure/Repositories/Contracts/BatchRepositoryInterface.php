<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Contracts;

use App\Domain\Shared\ValueObjects\DateRange;
use App\Models\Batch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Batch Repository Interface
 *
 * Defines the contract for batch data access operations.
 * Abstracts the data layer to allow for different implementations
 * and easier testing with repository pattern.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
interface BatchRepositoryInterface
{
    /**
     * Find batch by ID
     *
     * @param  int  $id  Batch ID
     * @return Batch|null Batch instance or null if not found
     */
    public function findById(int $id): ?Batch;

    /**
     * Find batch by batch code
     *
     * @param  string  $batchCode  Unique batch code
     * @return Batch|null Batch instance or null if not found
     */
    public function findByCode(string $batchCode): ?Batch;

    /**
     * Find batches by status
     *
     * @param  string  $status  Batch status
     * @return Collection Collection of batches
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find active batches with eager loaded relationships
     *
     * @return Collection Collection of active batches
     */
    public function findActiveBatches(): Collection;

    /**
     * Find batches by date range
     *
     * @param  DateRange  $dateRange  Date range to filter by
     * @param  string  $dateField  Date field to filter on
     * @return Collection Collection of batches
     */
    public function findByDateRange(DateRange $dateRange, string $dateField = 'date_received'): Collection;

    /**
     * Find batches requiring attention (high mortality, overdue, etc.)
     *
     * @return Collection Collection of batches requiring attention
     */
    public function findRequiringAttention(): Collection;

    /**
     * Get paginated batches with optional filtering
     *
     * @param  array  $filters  Filter criteria
     * @param  int  $perPage  Items per page
     * @return LengthAwarePaginator Paginated results
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create new batch
     *
     * @param  array  $data  Batch data
     * @return Batch Created batch instance
     */
    public function create(array $data): Batch;

    /**
     * Update existing batch
     *
     * @param  int  $id  Batch ID
     * @param  array  $data  Updated data
     * @return Batch Updated batch instance
     */
    public function update(int $id, array $data): Batch;

    /**
     * Delete batch
     *
     * @param  int  $id  Batch ID
     * @return bool True if deleted successfully
     */
    public function delete(int $id): bool;

    /**
     * Soft delete related records
     *
     * @param  int  $batchId  Batch ID
     * @return bool True if successful
     */
    public function softDeleteRelated(int $batchId): bool;

    /**
     * Check if batch code exists
     *
     * @param  string  $batchCode  Batch code to check
     * @param  int|null  $excludeId  ID to exclude from check
     * @return bool True if exists
     */
    public function existsByCode(string $batchCode, ?int $excludeId = null): bool;

    /**
     * Get batch performance metrics
     *
     * @param  int  $batchId  Batch ID
     * @param  DateRange  $dateRange  Date range for metrics
     * @return array Performance metrics data
     */
    public function getPerformanceMetrics(int $batchId, DateRange $dateRange): array;

    /**
     * Calculate feed conversion ratio for batch
     *
     * @param  int  $batchId  Batch ID
     * @param  DateRange  $dateRange  Date range for calculation
     * @return float Feed conversion ratio
     */
    public function calculateFeedConversionRatio(int $batchId, DateRange $dateRange): float;

    /**
     * Get profitability analysis for batch
     *
     * @param  int  $batchId  Batch ID
     * @return array Profitability metrics
     */
    public function getProfitabilityAnalysis(int $batchId): array;

    /**
     * Archive completed batches older than date range
     *
     * @param  DateRange  $dateRange  Batches to archive
     * @return int Number of batches archived
     */
    public function archiveCompleted(DateRange $dateRange): int;

    /**
     * Get summary statistics for all batches
     *
     * @return array Summary statistics
     */
    public function getSummaryStatistics(): array;

    /**
     * Get batch with all related data
     *
     * @param  int  $id  Batch ID
     * @return Batch|null Batch with relationships loaded
     */
    public function findWithRelations(int $id): ?Batch;

    /**
     * Get batches for reporting with optimized queries
     *
     * @param  DateRange  $dateRange  Date range filter
     * @param  array  $filters  Additional filters
     * @return Collection Optimized batch data for reporting
     */
    public function findForReporting(DateRange $dateRange, array $filters = []): Collection;

    /**
     * Get batch count by status
     *
     * @return array Status counts
     */
    public function getStatusCounts(): array;

    /**
     * Find batches with high mortality
     *
     * @param  float  $threshold  Mortality threshold percentage
     * @return Collection Batches with high mortality
     */
    public function findWithHighMortality(float $threshold = 10.0): Collection;

    /**
     * Find overdue batches
     *
     * @return Collection Overdue batches
     */
    public function findOverdue(): Collection;

    /**
     * Get average performance metrics across all batches
     *
     * @param  DateRange|null  $dateRange  Optional date range filter
     * @return array Average performance metrics
     */
    public function getAveragePerformanceMetrics(?DateRange $dateRange = null): array;

    /**
     * Find top performing batches
     *
     * @param  int  $limit  Number of batches to return
     * @param  string  $metric  Metric to sort by (mortality_rate, profit_margin, etc.)
     * @return Collection Top performing batches
     */
    public function findTopPerforming(int $limit = 10, string $metric = 'profit_margin'): Collection;

    /**
     * Get batch lifecycle analysis
     *
     * @param  int  $batchId  Batch ID
     * @return array Lifecycle analysis data
     */
    public function getLifecycleAnalysis(int $batchId): array;

    /**
     * Bulk update batch statuses
     *
     * @param  array  $batchIds  Array of batch IDs
     * @param  string  $status  New status
     * @return int Number of updated batches
     */
    public function bulkUpdateStatus(array $batchIds, string $status): int;

    /**
     * Get batch trend analysis
     *
     * @param  DateRange  $dateRange  Date range for analysis
     * @param  string  $metric  Metric to analyze
     * @return array Trend analysis data
     */
    public function getTrendAnalysis(DateRange $dateRange, string $metric): array;
}
