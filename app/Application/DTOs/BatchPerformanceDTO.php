<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use App\Domain\Shared\ValueObjects\DateRange;
use App\Domain\Shared\ValueObjects\Money;

/**
 * Batch Performance Data Transfer Object
 *
 * Contains comprehensive performance metrics for a batch over a specific period.
 * Used for analytics, reporting, and decision-making.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
final readonly class BatchPerformanceDTO
{
    /**
     * @param  int  $batchId  The batch identifier
     * @param  string  $batchCode  The batch code for reference
     * @param  DateRange  $period  The period for which metrics are calculated
     * @param  int  $initialPopulation  Starting population for the period
     * @param  int  $currentPopulation  Current population at end of period
     * @param  int  $totalDeaths  Total deaths during the period
     * @param  int  $totalCulls  Total culls during the period
     * @param  float  $mortalityRate  Mortality rate percentage
     * @param  float  $averageWeight  Average weight in grams
     * @param  int  $totalEggsProduced  Total eggs produced in period
     * @param  float  $eggProductionRate  Egg production rate percentage
     * @param  float  $feedConsumed  Total feed consumed in kg
     * @param  Money  $feedCost  Total cost of feed consumed
     * @param  float  $feedConversionRatio  Feed conversion ratio
     * @param  Money  $totalRevenue  Total revenue from sales
     * @param  Money  $totalCosts  Total costs (feed + other)
     * @param  Money  $profitLoss  Profit or loss for the period
     * @param  int  $totalVaccinations  Number of vaccinations administered
     * @param  int  $diseaseIncidents  Number of disease incidents
     * @param  array  $kpis  Additional key performance indicators
     */
    public function __construct(
        public int $batchId,
        public string $batchCode,
        public DateRange $period,
        public int $initialPopulation,
        public int $currentPopulation,
        public int $totalDeaths,
        public int $totalCulls,
        public float $mortalityRate,
        public float $averageWeight,
        public int $totalEggsProduced,
        public float $eggProductionRate,
        public float $feedConsumed,
        public Money $feedCost,
        public float $feedConversionRatio,
        public Money $totalRevenue,
        public Money $totalCosts,
        public Money $profitLoss,
        public int $totalVaccinations,
        public int $diseaseIncidents,
        public array $kpis = []
    ) {
        $this->validate();
    }

    /**
     * Create from performance calculation results
     */
    public static function create(
        int $batchId,
        string $batchCode,
        DateRange $period,
        array $performanceData
    ): self {
        return new self(
            batchId: $batchId,
            batchCode: $batchCode,
            period: $period,
            initialPopulation: $performanceData['initial_population'] ?? 0,
            currentPopulation: $performanceData['current_population'] ?? 0,
            totalDeaths: $performanceData['total_deaths'] ?? 0,
            totalCulls: $performanceData['total_culls'] ?? 0,
            mortalityRate: $performanceData['mortality_rate'] ?? 0.0,
            averageWeight: $performanceData['average_weight'] ?? 0.0,
            totalEggsProduced: $performanceData['total_eggs_produced'] ?? 0,
            eggProductionRate: $performanceData['egg_production_rate'] ?? 0.0,
            feedConsumed: $performanceData['feed_consumed'] ?? 0.0,
            feedCost: Money::fromString((string) ($performanceData['feed_cost'] ?? 0)),
            feedConversionRatio: $performanceData['feed_conversion_ratio'] ?? 0.0,
            totalRevenue: Money::fromString((string) ($performanceData['total_revenue'] ?? 0)),
            totalCosts: Money::fromString((string) ($performanceData['total_costs'] ?? 0)),
            profitLoss: Money::fromString((string) ($performanceData['profit_loss'] ?? 0)),
            totalVaccinations: $performanceData['total_vaccinations'] ?? 0,
            diseaseIncidents: $performanceData['disease_incidents'] ?? 0,
            kpis: $performanceData['kpis'] ?? []
        );
    }

    /**
     * Get survival rate percentage
     */
    public function getSurvivalRate(): float
    {
        return round(100 - $this->mortalityRate, 2);
    }

    /**
     * Get total mortality count
     */
    public function getTotalMortality(): int
    {
        return $this->totalDeaths + $this->totalCulls;
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMargin(): float
    {
        if ($this->totalRevenue->isZero()) {
            return 0.0;
        }

        $profitPercentage = ($this->profitLoss->getAmount() / $this->totalRevenue->getAmount()) * 100;

        return round($profitPercentage, 2);
    }

    /**
     * Get return on investment percentage
     */
    public function getReturnOnInvestment(): float
    {
        if ($this->totalCosts->isZero()) {
            return 0.0;
        }

        $roi = ($this->profitLoss->getAmount() / $this->totalCosts->getAmount()) * 100;

        return round($roi, 2);
    }

    /**
     * Get eggs per bird per day
     */
    public function getEggsPerBirdPerDay(): float
    {
        $averagePopulation = ($this->initialPopulation + $this->currentPopulation) / 2;
        $days = $this->period->getDurationInDays();

        if ($averagePopulation === 0 || $days === 0) {
            return 0.0;
        }

        return round($this->totalEggsProduced / ($averagePopulation * $days), 3);
    }

    /**
     * Get feed cost per bird
     */
    public function getFeedCostPerBird(): Money
    {
        $averagePopulation = ($this->initialPopulation + $this->currentPopulation) / 2;

        if ($averagePopulation === 0) {
            return new Money(0.0);
        }

        return $this->feedCost->divide($averagePopulation);
    }

    /**
     * Get revenue per bird
     */
    public function getRevenuePerBird(): Money
    {
        $averagePopulation = ($this->initialPopulation + $this->currentPopulation) / 2;

        if ($averagePopulation === 0) {
            return new Money(0.0);
        }

        return $this->totalRevenue->divide($averagePopulation);
    }

    /**
     * Get daily mortality rate
     */
    public function getDailyMortalityRate(): float
    {
        $days = $this->period->getDurationInDays();

        if ($days === 0) {
            return 0.0;
        }

        return round($this->mortalityRate / $days, 4);
    }

    /**
     * Check if performance is concerning based on thresholds
     */
    public function isConcerning(array $thresholds = []): bool
    {
        $defaultThresholds = [
            'max_mortality_rate' => 10.0,
            'min_egg_production_rate' => 80.0,
            'max_feed_conversion_ratio' => 2.5,
            'min_profit_margin' => 5.0,
        ];

        $thresholds = array_merge($defaultThresholds, $thresholds);

        return $this->mortalityRate > $thresholds['max_mortality_rate']
            || $this->eggProductionRate < $thresholds['min_egg_production_rate']
            || $this->feedConversionRatio > $thresholds['max_feed_conversion_ratio']
            || $this->getProfitMargin() < $thresholds['min_profit_margin'];
    }

    /**
     * Get performance grade (A, B, C, D, F)
     */
    public function getPerformanceGrade(): string
    {
        $score = 0;

        // Mortality rate (25 points max)
        if ($this->mortalityRate <= 2.0) {
            $score += 25;
        } elseif ($this->mortalityRate <= 5.0) {
            $score += 20;
        } elseif ($this->mortalityRate <= 8.0) {
            $score += 15;
        } elseif ($this->mortalityRate <= 12.0) {
            $score += 10;
        } else {
            $score += 5;
        }

        // Egg production rate (25 points max)
        if ($this->eggProductionRate >= 90.0) {
            $score += 25;
        } elseif ($this->eggProductionRate >= 85.0) {
            $score += 20;
        } elseif ($this->eggProductionRate >= 80.0) {
            $score += 15;
        } elseif ($this->eggProductionRate >= 70.0) {
            $score += 10;
        } else {
            $score += 5;
        }

        // Feed conversion ratio (25 points max)
        if ($this->feedConversionRatio <= 2.0) {
            $score += 25;
        } elseif ($this->feedConversionRatio <= 2.3) {
            $score += 20;
        } elseif ($this->feedConversionRatio <= 2.6) {
            $score += 15;
        } elseif ($this->feedConversionRatio <= 3.0) {
            $score += 10;
        } else {
            $score += 5;
        }

        // Profitability (25 points max)
        $profitMargin = $this->getProfitMargin();
        if ($profitMargin >= 20.0) {
            $score += 25;
        } elseif ($profitMargin >= 15.0) {
            $score += 20;
        } elseif ($profitMargin >= 10.0) {
            $score += 15;
        } elseif ($profitMargin >= 5.0) {
            $score += 10;
        } else {
            $score += 5;
        }

        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'F'
        };
    }

    /**
     * Get health score based on health indicators
     */
    public function getHealthScore(): float
    {
        $score = 100.0;

        // Deduct for mortality
        $score -= $this->mortalityRate;

        // Deduct for disease incidents
        if ($this->diseaseIncidents > 0) {
            $score -= ($this->diseaseIncidents * 5); // 5 points per incident
        }

        // Bonus for good vaccination compliance
        if ($this->totalVaccinations > 0) {
            $score += min(10, $this->totalVaccinations * 2); // Up to 10 bonus points
        }

        return max(0, min(100, round($score, 1)));
    }

    /**
     * Get recommendations based on performance
     */
    public function getRecommendations(): array
    {
        $recommendations = [];

        if ($this->mortalityRate > 10.0) {
            $recommendations[] = 'High mortality rate detected. Review housing conditions, biosecurity, and health management protocols.';
        }

        if ($this->eggProductionRate < 80.0) {
            $recommendations[] = 'Low egg production. Check nutrition, lighting, and stress factors.';
        }

        if ($this->feedConversionRatio > 2.5) {
            $recommendations[] = 'Poor feed conversion. Evaluate feed quality and feeding practices.';
        }

        if ($this->diseaseIncidents > 0) {
            $recommendations[] = 'Disease incidents detected. Strengthen biosecurity measures and vaccination protocols.';
        }

        if ($this->getProfitMargin() < 5.0) {
            $recommendations[] = 'Low profitability. Review cost structure and pricing strategies.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Performance is within acceptable ranges. Continue current management practices.';
        }

        return $recommendations;
    }

    /**
     * Convert to array for API responses
     */
    public function toArray(): array
    {
        return [
            'batch_id' => $this->batchId,
            'batch_code' => $this->batchCode,
            'period' => $this->period->toArray(),
            'population' => [
                'initial' => $this->initialPopulation,
                'current' => $this->currentPopulation,
                'total_deaths' => $this->totalDeaths,
                'total_culls' => $this->totalCulls,
                'mortality_rate' => $this->mortalityRate,
                'survival_rate' => $this->getSurvivalRate(),
            ],
            'production' => [
                'average_weight' => $this->averageWeight,
                'total_eggs_produced' => $this->totalEggsProduced,
                'egg_production_rate' => $this->eggProductionRate,
                'eggs_per_bird_per_day' => $this->getEggsPerBirdPerDay(),
            ],
            'feed' => [
                'consumed_kg' => $this->feedConsumed,
                'cost' => $this->feedCost->toArray(),
                'conversion_ratio' => $this->feedConversionRatio,
                'cost_per_bird' => $this->getFeedCostPerBird()->toArray(),
            ],
            'financial' => [
                'total_revenue' => $this->totalRevenue->toArray(),
                'total_costs' => $this->totalCosts->toArray(),
                'profit_loss' => $this->profitLoss->toArray(),
                'profit_margin' => $this->getProfitMargin(),
                'roi' => $this->getReturnOnInvestment(),
                'revenue_per_bird' => $this->getRevenuePerBird()->toArray(),
            ],
            'health' => [
                'total_vaccinations' => $this->totalVaccinations,
                'disease_incidents' => $this->diseaseIncidents,
                'health_score' => $this->getHealthScore(),
            ],
            'analytics' => [
                'performance_grade' => $this->getPerformanceGrade(),
                'is_concerning' => $this->isConcerning(),
                'daily_mortality_rate' => $this->getDailyMortalityRate(),
            ],
            'recommendations' => $this->getRecommendations(),
            'kpis' => $this->kpis,
        ];
    }

    /**
     * Validate DTO data
     *
     * @throws \InvalidArgumentException
     */
    private function validate(): void
    {
        if ($this->batchId <= 0) {
            throw new \InvalidArgumentException('Batch ID must be positive');
        }

        if (empty($this->batchCode)) {
            throw new \InvalidArgumentException('Batch code cannot be empty');
        }

        if ($this->initialPopulation < 0 || $this->currentPopulation < 0) {
            throw new \InvalidArgumentException('Population counts cannot be negative');
        }

        if ($this->totalDeaths < 0 || $this->totalCulls < 0) {
            throw new \InvalidArgumentException('Mortality counts cannot be negative');
        }

        if ($this->mortalityRate < 0 || $this->mortalityRate > 100) {
            throw new \InvalidArgumentException('Mortality rate must be between 0 and 100');
        }

        if ($this->feedConsumed < 0) {
            throw new \InvalidArgumentException('Feed consumed cannot be negative');
        }

        if ($this->totalVaccinations < 0 || $this->diseaseIncidents < 0) {
            throw new \InvalidArgumentException('Health metrics cannot be negative');
        }
    }
}
