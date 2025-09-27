<?php

declare(strict_types=1);

namespace App\Application\Services\Contracts;

use App\Application\DTOs\BatchDTO;
use App\Application\DTOs\BatchPerformanceDTO;
use App\Domain\Shared\ValueObjects\DateRange;
use App\Domain\Shared\ValueObjects\Population;
use App\Models\Batch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Batch Service Interface
 *
 * Defines the contract for batch management operations.
 * This interface abstracts the business logic for batch operations,
 * allowing for different implementations and easier testing.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
interface BatchServiceInterface
{
    /**
     * Create a new batch
     *
     * @param  BatchDTO  $batchDTO  Data transfer object containing batch information
     * @return Batch The created batch instance
     *
     * @throws \App\Domain\Exceptions\BatchCreationException
     * @throws \App\Domain\Exceptions\ValidationException
     */
    public function createBatch(BatchDTO $batchDTO): Batch;

    /**
     * Update an existing batch
     *
     * @param  int  $batchId  The batch ID to update
     * @param  BatchDTO  $batchDTO  Updated batch data
     * @return Batch The updated batch instance
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     * @throws \App\Domain\Exceptions\ValidationException
     */
    public function updateBatch(int $batchId, BatchDTO $batchDTO): Batch;

    /**
     * Get batch by ID
     *
     * @param  int  $batchId  The batch ID
     * @return Batch The batch instance
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     */
    public function getBatch(int $batchId): Batch;

    /**
     * Get batch by batch code
     *
     * @param  string  $batchCode  The unique batch code
     * @return Batch The batch instance
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     */
    public function getBatchByCode(string $batchCode): Batch;

    /**
     * Get all active batches
     *
     * @return Collection Collection of active batches
     */
    public function getActiveBatches(): Collection;

    /**
     * Get paginated batches with optional filtering
     *
     * @param  array  $filters  Filter criteria
     * @param  int  $perPage  Number of items per page
     * @return LengthAwarePaginator Paginated batch results
     */
    public function getPaginatedBatches(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Update batch population after daily record entry
     *
     * @param  int  $batchId  The batch ID
     * @param  Population  $population  New population data
     * @return Batch Updated batch instance
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     * @throws \App\Domain\Exceptions\InvalidPopulationException
     */
    public function updateBatchPopulation(int $batchId, Population $population): Batch;

    /**
     * Calculate batch age in days
     *
     * @param  int  $batchId  The batch ID
     * @return int Age in days
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     */
    public function calculateBatchAge(int $batchId): int;

    /**
     * Get batch performance metrics
     *
     * @param  int  $batchId  The batch ID
     * @param  DateRange|null  $dateRange  Optional date range filter
     * @return BatchPerformanceDTO Performance metrics
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     */
    public function getBatchPerformance(int $batchId, ?DateRange $dateRange = null): BatchPerformanceDTO;

    /**
     * Update batch status
     *
     * @param  int  $batchId  The batch ID
     * @param  string  $status  New status (active, completed, culled)
     * @return Batch Updated batch instance
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     * @throws \App\Domain\Exceptions\InvalidStatusTransitionException
     */
    public function updateBatchStatus(int $batchId, string $status): Batch;

    /**
     * Check if batch can be transitioned to new status
     *
     * @param  int  $batchId  The batch ID
     * @param  string  $newStatus  Target status
     * @return bool True if transition is allowed
     */
    public function canTransitionToStatus(int $batchId, string $newStatus): bool;

    /**
     * Get expected culling date for batch
     *
     * @param  int  $batchId  The batch ID
     * @return \Carbon\Carbon Expected culling date
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     */
    public function getExpectedCullingDate(int $batchId): \Carbon\Carbon;

    /**
     * Get batches by status
     *
     * @param  string  $status  Batch status
     * @return Collection Collection of batches with specified status
     */
    public function getBatchesByStatus(string $status): Collection;

    /**
     * Get batches by date range
     *
     * @param  DateRange  $dateRange  Date range to filter by
     * @param  string  $dateField  Field to filter on (date_received, hatch_date, expected_end_date)
     * @return Collection Collection of batches within date range
     */
    public function getBatchesByDateRange(DateRange $dateRange, string $dateField = 'date_received'): Collection;

    /**
     * Archive completed batches
     *
     * @param  DateRange  $dateRange  Date range for batches to archive
     * @return int Number of batches archived
     */
    public function archiveCompletedBatches(DateRange $dateRange): int;

    /**
     * Get batch summary statistics
     *
     * @return array Summary statistics
     */
    public function getBatchSummaryStatistics(): array;

    /**
     * Validate batch business rules
     *
     * @param  BatchDTO  $batchDTO  Batch data to validate
     * @return array Array of validation errors (empty if valid)
     */
    public function validateBatchBusinessRules(BatchDTO $batchDTO): array;

    /**
     * Get batches requiring attention (high mortality, overdue, etc.)
     *
     * @return Collection Collection of batches requiring attention
     */
    public function getBatchesRequiringAttention(): Collection;

    /**
     * Calculate feed conversion ratio for batch
     *
     * @param  int  $batchId  The batch ID
     * @param  DateRange|null  $dateRange  Optional date range
     * @return float Feed conversion ratio
     */
    public function calculateFeedConversionRatio(int $batchId, ?DateRange $dateRange = null): float;

    /**
     * Get batch profitability analysis
     *
     * @param  int  $batchId  The batch ID
     * @return array Profitability metrics
     */
    public function getBatchProfitabilityAnalysis(int $batchId): array;

    /**
     * Delete batch and related records
     *
     * @param  int  $batchId  The batch ID
     * @return bool True if successfully deleted
     *
     * @throws \App\Domain\Exceptions\BatchNotFoundException
     * @throws \App\Domain\Exceptions\BatchCannotBeDeletedException
     */
    public function deleteBatch(int $batchId): bool;
}
