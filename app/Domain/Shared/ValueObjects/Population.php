<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

/**
 * Population Value Object
 *
 * Manages bird population counts and mortality calculations.
 * Ensures business rules around population management are enforced.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
final class Population
{
    /**
     * @param  int  $aliveCount  Current number of alive birds
     * @param  int  $deadCount  Number of birds that died
     * @param  int  $cullsCount  Number of birds culled
     */
    public function __construct(
        private readonly int $aliveCount,
        private readonly int $deadCount = 0,
        private readonly int $cullsCount = 0
    ) {
        if ($aliveCount < 0) {
            throw new \InvalidArgumentException('Alive count cannot be negative');
        }

        if ($deadCount < 0) {
            throw new \InvalidArgumentException('Dead count cannot be negative');
        }

        if ($cullsCount < 0) {
            throw new \InvalidArgumentException('Culls count cannot be negative');
        }
    }

    /**
     * Create population from total count (all alive initially)
     */
    public static function fromTotalCount(int $totalCount): self
    {
        if ($totalCount < 0) {
            throw new \InvalidArgumentException('Total count cannot be negative');
        }

        return new self($totalCount, 0, 0);
    }

    /**
     * Get the alive count
     */
    public function getAliveCount(): int
    {
        return $this->aliveCount;
    }

    /**
     * Get the dead count
     */
    public function getDeadCount(): int
    {
        return $this->deadCount;
    }

    /**
     * Get the culls count
     */
    public function getCullsCount(): int
    {
        return $this->cullsCount;
    }

    /**
     * Get total mortality (dead + culls)
     */
    public function getTotalMortality(): int
    {
        return $this->deadCount + $this->cullsCount;
    }

    /**
     * Get the original population size
     */
    public function getOriginalPopulation(): int
    {
        return $this->aliveCount + $this->deadCount + $this->cullsCount;
    }

    /**
     * Calculate mortality rate as percentage
     *
     * @return float Mortality rate (0-100)
     */
    public function getMortalityRate(): float
    {
        $originalPopulation = $this->getOriginalPopulation();

        if ($originalPopulation === 0) {
            return 0.0;
        }

        return round(($this->getTotalMortality() / $originalPopulation) * 100, 2);
    }

    /**
     * Calculate survival rate as percentage
     *
     * @return float Survival rate (0-100)
     */
    public function getSurvivalRate(): float
    {
        return round(100 - $this->getMortalityRate(), 2);
    }

    /**
     * Check if mortality rate is within acceptable limits
     *
     * @param  float  $maxAcceptableRate  Maximum acceptable mortality rate
     */
    public function isWithinAcceptableMortality(float $maxAcceptableRate = 5.0): bool
    {
        return $this->getMortalityRate() <= $maxAcceptableRate;
    }

    /**
     * Add mortality to current population
     *
     * @param  int  $deadCount  Additional dead birds
     * @param  int  $cullsCount  Additional culled birds
     */
    public function addMortality(int $deadCount = 0, int $cullsCount = 0): self
    {
        if ($deadCount < 0 || $cullsCount < 0) {
            throw new \InvalidArgumentException('Mortality counts cannot be negative');
        }

        $totalMortality = $deadCount + $cullsCount;

        if ($totalMortality > $this->aliveCount) {
            throw new \InvalidArgumentException(
                "Total mortality ({$totalMortality}) cannot exceed alive count ({$this->aliveCount})"
            );
        }

        return new self(
            $this->aliveCount - $totalMortality,
            $this->deadCount + $deadCount,
            $this->cullsCount + $cullsCount
        );
    }

    /**
     * Update alive count directly (for batch transfers, etc.)
     */
    public function updateAliveCount(int $newAliveCount): self
    {
        if ($newAliveCount < 0) {
            throw new \InvalidArgumentException('New alive count cannot be negative');
        }

        return new self($newAliveCount, $this->deadCount, $this->cullsCount);
    }

    /**
     * Check if population is empty
     */
    public function isEmpty(): bool
    {
        return $this->aliveCount === 0;
    }

    /**
     * Check if there has been any mortality
     */
    public function hasMortality(): bool
    {
        return $this->getTotalMortality() > 0;
    }

    /**
     * Get population density (birds per square meter)
     *
     * @param  float  $areaSquareMeters  Available area in square meters
     */
    public function getDensity(float $areaSquareMeters): float
    {
        if ($areaSquareMeters <= 0) {
            throw new \InvalidArgumentException('Area must be positive');
        }

        return round($this->aliveCount / $areaSquareMeters, 2);
    }

    /**
     * Check if population density is within recommended limits
     *
     * @param  float  $areaSquareMeters  Available area
     * @param  float  $maxDensity  Maximum recommended density (birds/m²)
     */
    public function isDensityAcceptable(float $areaSquareMeters, float $maxDensity = 8.0): bool
    {
        return $this->getDensity($areaSquareMeters) <= $maxDensity;
    }

    /**
     * Format population for display
     */
    public function format(): string
    {
        $mortality = $this->getTotalMortality();
        $rate = $this->getMortalityRate();

        return "Alive: {$this->aliveCount}, Mortality: {$mortality} ({$rate}%)";
    }

    /**
     * Convert to string representation
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * Convert to array for serialization
     */
    public function toArray(): array
    {
        return [
            'alive_count' => $this->aliveCount,
            'dead_count' => $this->deadCount,
            'culls_count' => $this->cullsCount,
            'total_mortality' => $this->getTotalMortality(),
            'mortality_rate' => $this->getMortalityRate(),
            'survival_rate' => $this->getSurvivalRate(),
            'original_population' => $this->getOriginalPopulation(),
        ];
    }

    /**
     * Create Population from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['alive_count'] ?? 0,
            $data['dead_count'] ?? 0,
            $data['culls_count'] ?? 0
        );
    }

    /**
     * Check if two populations are equal
     */
    public function equals(Population $other): bool
    {
        return $this->aliveCount === $other->aliveCount
            && $this->deadCount === $other->deadCount
            && $this->cullsCount === $other->cullsCount;
    }
}
