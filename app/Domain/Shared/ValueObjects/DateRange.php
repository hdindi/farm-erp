<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Carbon\Carbon;

/**
 * DateRange Value Object
 *
 * Represents a period between two dates with various utility methods.
 * Immutable object for date range operations.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
final class DateRange
{
    private readonly Carbon $startDate;

    private readonly Carbon $endDate;

    /**
     * @param  Carbon|string  $startDate  Start date of the range
     * @param  Carbon|string  $endDate  End date of the range
     */
    public function __construct(Carbon|string $startDate, Carbon|string $endDate)
    {
        $this->startDate = $startDate instanceof Carbon ? $startDate->clone() : Carbon::parse($startDate);
        $this->endDate = $endDate instanceof Carbon ? $endDate->clone() : Carbon::parse($endDate);

        if ($this->startDate->isAfter($this->endDate)) {
            throw new \InvalidArgumentException('Start date must be before or equal to end date');
        }
    }

    /**
     * Create DateRange for current month
     */
    public static function currentMonth(): self
    {
        $now = Carbon::now();

        return new self(
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth()
        );
    }

    /**
     * Create DateRange for current week
     */
    public static function currentWeek(): self
    {
        $now = Carbon::now();

        return new self(
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek()
        );
    }

    /**
     * Create DateRange for today
     */
    public static function today(): self
    {
        $now = Carbon::now();

        return new self(
            $now->copy()->startOfDay(),
            $now->copy()->endOfDay()
        );
    }

    /**
     * Create DateRange for last N days
     */
    public static function lastDays(int $days): self
    {
        $now = Carbon::now();

        return new self(
            $now->copy()->subDays($days),
            $now
        );
    }

    /**
     * Create DateRange from batch lifecycle (hatch to expected end)
     */
    public static function fromBatchLifecycle(Carbon $hatchDate, int $lifespanDays): self
    {
        return new self(
            $hatchDate,
            $hatchDate->copy()->addDays($lifespanDays)
        );
    }

    /**
     * Get the start date
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate->clone();
    }

    /**
     * Get the end date
     */
    public function getEndDate(): Carbon
    {
        return $this->endDate->clone();
    }

    /**
     * Get duration in days
     */
    public function getDurationInDays(): int
    {
        return $this->startDate->diffInDays($this->endDate) + 1; // +1 to include both start and end dates
    }

    /**
     * Get duration in weeks
     */
    public function getDurationInWeeks(): float
    {
        return round($this->getDurationInDays() / 7, 2);
    }

    /**
     * Check if a date falls within this range
     */
    public function contains(Carbon|string $date): bool
    {
        $checkDate = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $checkDate->between($this->startDate, $this->endDate);
    }

    /**
     * Check if this range overlaps with another range
     */
    public function overlaps(DateRange $other): bool
    {
        return $this->startDate->lte($other->endDate) && $this->endDate->gte($other->startDate);
    }

    /**
     * Check if this range completely contains another range
     */
    public function encompasses(DateRange $other): bool
    {
        return $this->startDate->lte($other->startDate) && $this->endDate->gte($other->endDate);
    }

    /**
     * Get the overlap with another date range
     */
    public function getOverlap(DateRange $other): ?DateRange
    {
        if (! $this->overlaps($other)) {
            return null;
        }

        $overlapStart = $this->startDate->max($other->startDate);
        $overlapEnd = $this->endDate->min($other->endDate);

        return new self($overlapStart, $overlapEnd);
    }

    /**
     * Extend the range by adding days to the end
     */
    public function extendByDays(int $days): self
    {
        if ($days < 0) {
            throw new \InvalidArgumentException('Days to extend must be non-negative');
        }

        return new self(
            $this->startDate,
            $this->endDate->copy()->addDays($days)
        );
    }

    /**
     * Shrink the range by removing days from the end
     */
    public function shrinkByDays(int $days): self
    {
        if ($days < 0) {
            throw new \InvalidArgumentException('Days to shrink must be non-negative');
        }

        $newEndDate = $this->endDate->copy()->subDays($days);

        if ($newEndDate->isBefore($this->startDate)) {
            throw new \InvalidArgumentException('Cannot shrink range below start date');
        }

        return new self($this->startDate, $newEndDate);
    }

    /**
     * Split the range into smaller periods
     *
     * @param  int  $periodDays  Size of each period in days
     * @return DateRange[] Array of date ranges
     */
    public function splitIntoPeriods(int $periodDays): array
    {
        if ($periodDays <= 0) {
            throw new \InvalidArgumentException('Period days must be positive');
        }

        $periods = [];
        $currentStart = $this->startDate->clone();

        while ($currentStart->lte($this->endDate)) {
            $currentEnd = $currentStart->copy()->addDays($periodDays - 1);

            if ($currentEnd->gt($this->endDate)) {
                $currentEnd = $this->endDate->clone();
            }

            $periods[] = new self($currentStart, $currentEnd);
            $currentStart->addDays($periodDays);
        }

        return $periods;
    }

    /**
     * Get all dates within the range
     *
     * @return Carbon[] Array of Carbon instances for each date
     */
    public function getAllDates(): array
    {
        $dates = [];
        $current = $this->startDate->clone();

        while ($current->lte($this->endDate)) {
            $dates[] = $current->clone();
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Check if the range is in the future
     */
    public function isFuture(): bool
    {
        return $this->startDate->isFuture();
    }

    /**
     * Check if the range is in the past
     */
    public function isPast(): bool
    {
        return $this->endDate->isPast();
    }

    /**
     * Check if the range is current (contains today)
     */
    public function isCurrent(): bool
    {
        return $this->contains(Carbon::now());
    }

    /**
     * Format the range for display
     */
    public function format(string $dateFormat = 'Y-m-d'): string
    {
        return $this->startDate->format($dateFormat).' to '.$this->endDate->format($dateFormat);
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
            'start_date' => $this->startDate->toDateString(),
            'end_date' => $this->endDate->toDateString(),
            'duration_days' => $this->getDurationInDays(),
            'duration_weeks' => $this->getDurationInWeeks(),
        ];
    }

    /**
     * Create DateRange from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['start_date'],
            $data['end_date']
        );
    }

    /**
     * Check if two ranges are equal
     */
    public function equals(DateRange $other): bool
    {
        return $this->startDate->equalTo($other->startDate)
            && $this->endDate->equalTo($other->endDate);
    }
}
