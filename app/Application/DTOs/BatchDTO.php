<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use Carbon\Carbon;

/**
 * Batch Data Transfer Object
 *
 * Immutable data structure for transferring batch data between layers.
 * Ensures type safety and provides validation for batch-related operations.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
final readonly class BatchDTO
{
    /**
     * @param  string  $batchCode  Unique identifier for the batch
     * @param  int  $birdTypeId  Foreign key reference to bird type
     * @param  int  $breedId  Foreign key reference to breed
     * @param  string|null  $sourceFarm  Origin farm of the birds
     * @param  int  $birdAgeDays  Age of birds in days when received
     * @param  int  $initialPopulation  Initial number of birds in batch
     * @param  int  $currentPopulation  Current number of alive birds
     * @param  Carbon  $dateReceived  Date when batch was received
     * @param  Carbon|null  $hatchDate  Date when birds were hatched
     * @param  Carbon|null  $expectedEndDate  Expected date for batch completion
     * @param  string  $status  Current status (active, completed, culled)
     */
    public function __construct(
        public string $batchCode,
        public int $birdTypeId,
        public int $breedId,
        public ?string $sourceFarm,
        public int $birdAgeDays,
        public int $initialPopulation,
        public int $currentPopulation,
        public Carbon $dateReceived,
        public ?Carbon $hatchDate,
        public ?Carbon $expectedEndDate,
        public string $status = 'active'
    ) {
        $this->validate();
    }

    /**
     * Create from array data (typically from HTTP requests)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            batchCode: $data['batch_code'],
            birdTypeId: (int) $data['bird_type_id'],
            breedId: (int) $data['breed_id'],
            sourceFarm: $data['source_farm'] ?? null,
            birdAgeDays: (int) $data['bird_age_days'],
            initialPopulation: (int) $data['initial_population'],
            currentPopulation: (int) ($data['current_population'] ?? $data['initial_population']),
            dateReceived: Carbon::parse($data['date_received']),
            hatchDate: isset($data['hatch_date']) ? Carbon::parse($data['hatch_date']) : null,
            expectedEndDate: isset($data['expected_end_date']) ? Carbon::parse($data['expected_end_date']) : null,
            status: $data['status'] ?? 'active'
        );
    }

    /**
     * Create from Eloquent model
     */
    public static function fromModel(\App\Models\Batch $batch): self
    {
        return new self(
            batchCode: $batch->batch_code,
            birdTypeId: $batch->bird_type_id,
            breedId: $batch->breed_id,
            sourceFarm: $batch->source_farm,
            birdAgeDays: $batch->bird_age_days,
            initialPopulation: $batch->initial_population,
            currentPopulation: $batch->current_population,
            dateReceived: $batch->date_received,
            hatchDate: $batch->hatch_date,
            expectedEndDate: $batch->expected_end_date,
            status: $batch->status
        );
    }

    /**
     * Create for batch creation (without ID-dependent fields)
     */
    public static function forCreation(
        string $batchCode,
        int $birdTypeId,
        int $breedId,
        int $initialPopulation,
        Carbon $dateReceived,
        int $birdAgeDays = 0,
        ?string $sourceFarm = null,
        ?Carbon $hatchDate = null,
        ?Carbon $expectedEndDate = null
    ): self {
        return new self(
            batchCode: $batchCode,
            birdTypeId: $birdTypeId,
            breedId: $breedId,
            sourceFarm: $sourceFarm,
            birdAgeDays: $birdAgeDays,
            initialPopulation: $initialPopulation,
            currentPopulation: $initialPopulation, // Initially same as initial
            dateReceived: $dateReceived,
            hatchDate: $hatchDate,
            expectedEndDate: $expectedEndDate,
            status: 'active'
        );
    }

    /**
     * Create updated version with new current population
     */
    public function withUpdatedPopulation(int $newCurrentPopulation): self
    {
        return new self(
            batchCode: $this->batchCode,
            birdTypeId: $this->birdTypeId,
            breedId: $this->breedId,
            sourceFarm: $this->sourceFarm,
            birdAgeDays: $this->birdAgeDays,
            initialPopulation: $this->initialPopulation,
            currentPopulation: $newCurrentPopulation,
            dateReceived: $this->dateReceived,
            hatchDate: $this->hatchDate,
            expectedEndDate: $this->expectedEndDate,
            status: $this->status
        );
    }

    /**
     * Create updated version with new status
     */
    public function withStatus(string $newStatus): self
    {
        return new self(
            batchCode: $this->batchCode,
            birdTypeId: $this->birdTypeId,
            breedId: $this->breedId,
            sourceFarm: $this->sourceFarm,
            birdAgeDays: $this->birdAgeDays,
            initialPopulation: $this->initialPopulation,
            currentPopulation: $this->currentPopulation,
            dateReceived: $this->dateReceived,
            hatchDate: $this->hatchDate,
            expectedEndDate: $this->expectedEndDate,
            status: $newStatus
        );
    }

    /**
     * Convert to array for database operations
     */
    public function toArray(): array
    {
        return [
            'batch_code' => $this->batchCode,
            'bird_type_id' => $this->birdTypeId,
            'breed_id' => $this->breedId,
            'source_farm' => $this->sourceFarm,
            'bird_age_days' => $this->birdAgeDays,
            'initial_population' => $this->initialPopulation,
            'current_population' => $this->currentPopulation,
            'date_received' => $this->dateReceived->toDateString(),
            'hatch_date' => $this->hatchDate?->toDateString(),
            'expected_end_date' => $this->expectedEndDate?->toDateString(),
            'status' => $this->status,
        ];
    }

    /**
     * Calculate current age of batch in days
     */
    public function getCurrentAge(?Carbon $currentDate = null): int
    {
        $current = $currentDate ?? Carbon::now();

        if ($this->hatchDate) {
            return $this->hatchDate->diffInDays($current);
        }

        // If no hatch date, use received date plus initial age
        return $this->dateReceived->diffInDays($current) + $this->birdAgeDays;
    }

    /**
     * Calculate mortality rate
     */
    public function getMortalityRate(): float
    {
        if ($this->initialPopulation === 0) {
            return 0.0;
        }

        $mortality = $this->initialPopulation - $this->currentPopulation;

        return round(($mortality / $this->initialPopulation) * 100, 2);
    }

    /**
     * Get survival rate
     */
    public function getSurvivalRate(): float
    {
        return round(100 - $this->getMortalityRate(), 2);
    }

    /**
     * Check if batch is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if batch is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if batch is culled
     */
    public function isCulled(): bool
    {
        return $this->status === 'culled';
    }

    /**
     * Check if batch is overdue (past expected end date)
     */
    public function isOverdue(?Carbon $currentDate = null): bool
    {
        if (! $this->expectedEndDate) {
            return false;
        }

        $current = $currentDate ?? Carbon::now();

        return $current->isAfter($this->expectedEndDate) && $this->isActive();
    }

    /**
     * Get days until expected end date
     */
    public function getDaysUntilExpectedEnd(?Carbon $currentDate = null): ?int
    {
        if (! $this->expectedEndDate) {
            return null;
        }

        $current = $currentDate ?? Carbon::now();

        return $current->diffInDays($this->expectedEndDate, false);
    }

    /**
     * Check if mortality rate is concerning
     */
    public function hasConcerningMortality(float $threshold = 10.0): bool
    {
        return $this->getMortalityRate() > $threshold;
    }

    /**
     * Get batch lifecycle stage based on age
     */
    public function getLifecycleStage(): string
    {
        $age = $this->getCurrentAge();

        return match (true) {
            $age <= 7 => 'chick',
            $age <= 28 => 'young',
            $age <= 112 => 'grower', // 16 weeks
            $age <= 504 => 'layer',  // 72 weeks
            default => 'mature'
        };
    }

    /**
     * Validate DTO data
     *
     * @throws \InvalidArgumentException
     */
    private function validate(): void
    {
        if (empty($this->batchCode)) {
            throw new \InvalidArgumentException('Batch code cannot be empty');
        }

        if ($this->birdTypeId <= 0) {
            throw new \InvalidArgumentException('Bird type ID must be positive');
        }

        if ($this->breedId <= 0) {
            throw new \InvalidArgumentException('Breed ID must be positive');
        }

        if ($this->birdAgeDays < 0) {
            throw new \InvalidArgumentException('Bird age days cannot be negative');
        }

        if ($this->initialPopulation <= 0) {
            throw new \InvalidArgumentException('Initial population must be positive');
        }

        if ($this->currentPopulation < 0) {
            throw new \InvalidArgumentException('Current population cannot be negative');
        }

        if ($this->currentPopulation > $this->initialPopulation) {
            throw new \InvalidArgumentException('Current population cannot exceed initial population');
        }

        $validStatuses = ['active', 'completed', 'culled'];
        if (! in_array($this->status, $validStatuses, true)) {
            throw new \InvalidArgumentException('Invalid status: '.$this->status);
        }

        if ($this->hatchDate && $this->hatchDate->isFuture()) {
            throw new \InvalidArgumentException('Hatch date cannot be in the future');
        }

        if ($this->expectedEndDate && $this->expectedEndDate->isBefore($this->dateReceived)) {
            throw new \InvalidArgumentException('Expected end date cannot be before received date');
        }
    }
}
