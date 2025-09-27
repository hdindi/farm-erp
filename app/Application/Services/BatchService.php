<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\BatchDTO;
use App\Application\DTOs\BatchPerformanceDTO;
use App\Application\Services\Contracts\BatchServiceInterface;
use App\Domain\Exceptions\BatchNotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Domain\Shared\ValueObjects\DateRange;
use App\Domain\Shared\ValueObjects\Population;
use App\Infrastructure\Repositories\Contracts\BatchRepositoryInterface;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Batch Service Implementation
 *
 * Handles all business logic related to batch management.
 * Coordinates between the domain layer and infrastructure layer.
 * Implements comprehensive batch lifecycle management.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class BatchService implements BatchServiceInterface
{
    /**
     * @param  BatchRepositoryInterface  $batchRepository  Repository for batch data access
     */
    public function __construct(
        private readonly BatchRepositoryInterface $batchRepository
    ) {}

    /**
     * Create a new batch with full validation and business rule checking
     */
    public function createBatch(BatchDTO $batchDTO): Batch
    {
        Log::info('Creating new batch', ['batch_code' => $batchDTO->batchCode]);

        // Validate business rules
        $validationErrors = $this->validateBatchBusinessRules($batchDTO);
        if (! empty($validationErrors)) {
            throw new ValidationException('Batch creation validation failed', $validationErrors);
        }

        try {
            DB::beginTransaction();

            // Check for duplicate batch code
            if ($this->batchRepository->existsByCode($batchDTO->batchCode)) {
                throw new ValidationException('Batch code already exists', ['batch_code' => ['The batch code must be unique']]);
            }

            // Calculate expected end date if not provided
            $batchData = $batchDTO->toArray();
            if (! $batchDTO->expectedEndDate) {
                $batchData['expected_end_date'] = $this->calculateExpectedEndDate($batchDTO)->toDateString();
            }

            $batch = $this->batchRepository->create($batchData);

            // Trigger batch created event
            event(new \App\Application\Events\BatchCreated($batch));

            DB::commit();

            Log::info('Batch created successfully', [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
            ]);

            return $batch;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create batch', [
                'batch_code' => $batchDTO->batchCode,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing batch
     */
    public function updateBatch(int $batchId, BatchDTO $batchDTO): Batch
    {
        Log::info('Updating batch', ['batch_id' => $batchId]);

        $batch = $this->getBatch($batchId);

        // Validate business rules for update
        $validationErrors = $this->validateBatchBusinessRules($batchDTO);
        if (! empty($validationErrors)) {
            throw new ValidationException('Batch update validation failed', $validationErrors);
        }

        try {
            DB::beginTransaction();

            // Check for duplicate batch code (excluding current batch)
            if ($batchDTO->batchCode !== $batch->batch_code &&
                $this->batchRepository->existsByCode($batchDTO->batchCode)) {
                throw new ValidationException('Batch code already exists', ['batch_code' => ['The batch code must be unique']]);
            }

            $updatedBatch = $this->batchRepository->update($batchId, $batchDTO->toArray());

            // Trigger batch updated event
            event(new \App\Application\Events\BatchUpdated($updatedBatch, $batch));

            DB::commit();

            Log::info('Batch updated successfully', ['batch_id' => $batchId]);

            return $updatedBatch;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update batch', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get batch by ID
     */
    public function getBatch(int $batchId): Batch
    {
        $batch = $this->batchRepository->findById($batchId);

        if (! $batch) {
            throw new BatchNotFoundException("Batch with ID {$batchId} not found");
        }

        return $batch;
    }

    /**
     * Get batch by batch code
     */
    public function getBatchByCode(string $batchCode): Batch
    {
        $batch = $this->batchRepository->findByCode($batchCode);

        if (! $batch) {
            throw new BatchNotFoundException("Batch with code {$batchCode} not found");
        }

        return $batch;
    }

    /**
     * Get all active batches
     */
    public function getActiveBatches(): Collection
    {
        return $this->batchRepository->findByStatus('active');
    }

    /**
     * Get paginated batches with filtering
     */
    public function getPaginatedBatches(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->batchRepository->getPaginated($filters, $perPage);
    }

    /**
     * Update batch population after daily record
     */
    public function updateBatchPopulation(int $batchId, Population $population): Batch
    {
        Log::info('Updating batch population', [
            'batch_id' => $batchId,
            'new_population' => $population->getAliveCount(),
        ]);

        $batch = $this->getBatch($batchId);

        try {
            DB::beginTransaction();

            $updatedBatch = $this->batchRepository->update($batchId, [
                'current_population' => $population->getAliveCount(),
            ]);

            // Trigger population updated event
            event(new \App\Application\Events\BatchPopulationUpdated($updatedBatch, $population));

            // Check if batch needs status update based on population
            if ($population->getAliveCount() === 0 && $batch->status === 'active') {
                $this->updateBatchStatus($batchId, 'completed');
            }

            DB::commit();

            Log::info('Batch population updated successfully', ['batch_id' => $batchId]);

            return $updatedBatch;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update batch population', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calculate batch age in days
     */
    public function calculateBatchAge(int $batchId): int
    {
        $batch = $this->getBatch($batchId);

        if ($batch->hatch_date) {
            return $batch->hatch_date->diffInDays(Carbon::now());
        }

        // If no hatch date, use received date plus initial age
        return $batch->date_received->diffInDays(Carbon::now()) + $batch->bird_age_days;
    }

    /**
     * Get comprehensive batch performance metrics
     */
    public function getBatchPerformance(int $batchId, ?DateRange $dateRange = null): BatchPerformanceDTO
    {
        $batch = $this->getBatch($batchId);

        // Default to batch lifetime if no date range specified
        if (! $dateRange) {
            $dateRange = new DateRange(
                $batch->date_received,
                $batch->expected_end_date ?? Carbon::now()
            );
        }

        // Get performance data from repository
        $performanceData = $this->batchRepository->getPerformanceMetrics($batchId, $dateRange);

        return BatchPerformanceDTO::create($batchId, $batch->batch_code, $dateRange, $performanceData);
    }

    /**
     * Update batch status with validation
     */
    public function updateBatchStatus(int $batchId, string $status): Batch
    {
        Log::info('Updating batch status', [
            'batch_id' => $batchId,
            'new_status' => $status,
        ]);

        if (! $this->canTransitionToStatus($batchId, $status)) {
            throw new ValidationException("Cannot transition batch to status: {$status}");
        }

        try {
            DB::beginTransaction();

            $batch = $this->batchRepository->update($batchId, ['status' => $status]);

            // Trigger status change event
            event(new \App\Application\Events\BatchStatusChanged($batch, $status));

            DB::commit();

            Log::info('Batch status updated successfully', [
                'batch_id' => $batchId,
                'status' => $status,
            ]);

            return $batch;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update batch status', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if batch can transition to new status
     */
    public function canTransitionToStatus(int $batchId, string $newStatus): bool
    {
        $batch = $this->getBatch($batchId);
        $currentStatus = $batch->status;

        // Define valid transitions
        $validTransitions = [
            'active' => ['completed', 'culled'],
            'completed' => [], // Final state
            'culled' => [], // Final state
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? [], true);
    }

    /**
     * Get expected culling date
     */
    public function getExpectedCullingDate(int $batchId): Carbon
    {
        $batch = $this->getBatch($batchId);

        if ($batch->expected_end_date) {
            return $batch->expected_end_date;
        }

        // Calculate based on batch lifecycle
        return $this->calculateExpectedEndDate(BatchDTO::fromModel($batch));
    }

    /**
     * Get batches by status
     */
    public function getBatchesByStatus(string $status): Collection
    {
        return $this->batchRepository->findByStatus($status);
    }

    /**
     * Get batches by date range
     */
    public function getBatchesByDateRange(DateRange $dateRange, string $dateField = 'date_received'): Collection
    {
        return $this->batchRepository->findByDateRange($dateRange, $dateField);
    }

    /**
     * Archive completed batches
     */
    public function archiveCompletedBatches(DateRange $dateRange): int
    {
        Log::info('Archiving completed batches', [
            'date_range' => $dateRange->format(),
        ]);

        return $this->batchRepository->archiveCompleted($dateRange);
    }

    /**
     * Get batch summary statistics
     */
    public function getBatchSummaryStatistics(): array
    {
        return $this->batchRepository->getSummaryStatistics();
    }

    /**
     * Validate batch business rules
     */
    public function validateBatchBusinessRules(BatchDTO $batchDTO): array
    {
        $errors = [];

        // Check population limits
        if ($batchDTO->initialPopulation > 10000) {
            $errors['initial_population'] = ['Initial population cannot exceed 10,000 birds'];
        }

        // Check age constraints
        if ($batchDTO->birdAgeDays > 700) { // ~2 years
            $errors['bird_age_days'] = ['Bird age seems unrealistic'];
        }

        // Check date logic
        if ($batchDTO->hatchDate && $batchDTO->expectedEndDate) {
            $ageAtEnd = $batchDTO->hatchDate->diffInDays($batchDTO->expectedEndDate);
            if ($ageAtEnd > 1000) { // ~2.7 years
                $errors['expected_end_date'] = ['Expected end date results in unrealistic batch lifespan'];
            }
        }

        // Check status transitions
        $validStatuses = ['active', 'completed', 'culled'];
        if (! in_array($batchDTO->status, $validStatuses, true)) {
            $errors['status'] = ['Invalid batch status'];
        }

        return $errors;
    }

    /**
     * Get batches requiring attention
     */
    public function getBatchesRequiringAttention(): Collection
    {
        return $this->batchRepository->findRequiringAttention();
    }

    /**
     * Calculate feed conversion ratio
     */
    public function calculateFeedConversionRatio(int $batchId, ?DateRange $dateRange = null): float
    {
        $batch = $this->getBatch($batchId);

        if (! $dateRange) {
            $dateRange = new DateRange(
                $batch->date_received,
                Carbon::now()
            );
        }

        return $this->batchRepository->calculateFeedConversionRatio($batchId, $dateRange);
    }

    /**
     * Get batch profitability analysis
     */
    public function getBatchProfitabilityAnalysis(int $batchId): array
    {
        return $this->batchRepository->getProfitabilityAnalysis($batchId);
    }

    /**
     * Delete batch and related records
     */
    public function deleteBatch(int $batchId): bool
    {
        Log::warning('Attempting to delete batch', ['batch_id' => $batchId]);

        $batch = $this->getBatch($batchId);

        // Check if batch can be deleted
        if ($batch->status === 'active' && $batch->current_population > 0) {
            throw new \App\Domain\Exceptions\BatchCannotBeDeletedException(
                'Cannot delete active batch with living birds'
            );
        }

        try {
            DB::beginTransaction();

            // Soft delete related records first
            $this->batchRepository->softDeleteRelated($batchId);

            // Delete the batch
            $deleted = $this->batchRepository->delete($batchId);

            // Trigger batch deleted event
            event(new \App\Application\Events\BatchDeleted($batch));

            DB::commit();

            Log::info('Batch deleted successfully', ['batch_id' => $batchId]);

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete batch', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calculate expected end date based on batch characteristics
     */
    private function calculateExpectedEndDate(BatchDTO $batchDTO): Carbon
    {
        $baseDate = $batchDTO->hatchDate ?? $batchDTO->dateReceived;

        // Default lifecycle: 72 weeks for layers
        $defaultLifespanDays = 504; // 72 weeks * 7 days

        // Adjust based on bird type and breed if needed
        // This could be configurable or based on database settings

        return $baseDate->copy()->addDays($defaultLifespanDays);
    }
}
