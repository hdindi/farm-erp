<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

/**
 * Money Value Object
 *
 * Represents monetary values with currency handling and precision.
 * Immutable object following DDD principles.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
final class Money
{
    /**
     * @param  float  $amount  The monetary amount
     * @param  string  $currency  The currency code (ISO 4217)
     */
    public function __construct(
        private readonly float $amount,
        private readonly string $currency = 'USD'
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }

        if (strlen($currency) !== 3) {
            throw new \InvalidArgumentException('Currency must be a 3-character ISO code');
        }
    }

    /**
     * Create Money instance from cents/smallest unit
     */
    public static function fromCents(int $cents, string $currency = 'USD'): self
    {
        return new self($cents / 100, $currency);
    }

    /**
     * Create Money instance from string
     */
    public static function fromString(string $amount, string $currency = 'USD'): self
    {
        $numericAmount = (float) $amount;

        return new self($numericAmount, $currency);
    }

    /**
     * Get the amount value
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Get the currency code
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get amount in cents/smallest unit
     */
    public function getCents(): int
    {
        return (int) round($this->amount * 100);
    }

    /**
     * Add another money amount
     *
     * @throws \InvalidArgumentException if currencies don't match
     */
    public function add(Money $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * Subtract another money amount
     *
     * @throws \InvalidArgumentException if currencies don't match or result would be negative
     */
    public function subtract(Money $other): self
    {
        $this->ensureSameCurrency($other);

        $newAmount = $this->amount - $other->amount;

        return new self($newAmount, $this->currency);
    }

    /**
     * Multiply by a factor
     */
    public function multiply(float $factor): self
    {
        if ($factor < 0) {
            throw new \InvalidArgumentException('Factor cannot be negative');
        }

        return new self($this->amount * $factor, $this->currency);
    }

    /**
     * Divide by a divisor
     */
    public function divide(float $divisor): self
    {
        if ($divisor <= 0) {
            throw new \InvalidArgumentException('Divisor must be positive');
        }

        return new self($this->amount / $divisor, $this->currency);
    }

    /**
     * Check if this money equals another
     */
    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    /**
     * Check if this money is greater than another
     */
    public function greaterThan(Money $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->amount > $other->amount;
    }

    /**
     * Check if this money is less than another
     */
    public function lessThan(Money $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->amount < $other->amount;
    }

    /**
     * Check if this money is zero
     */
    public function isZero(): bool
    {
        return $this->amount === 0.0;
    }

    /**
     * Check if this money is positive
     */
    public function isPositive(): bool
    {
        return $this->amount > 0.0;
    }

    /**
     * Format as string with currency
     */
    public function format(): string
    {
        return sprintf('%.2f %s', $this->amount, $this->currency);
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
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    /**
     * Create Money from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['amount'] ?? 0.0,
            $data['currency'] ?? 'USD'
        );
    }

    /**
     * Ensure two Money objects have the same currency
     *
     * @throws \InvalidArgumentException if currencies don't match
     */
    private function ensureSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException(
                "Cannot perform operation on different currencies: {$this->currency} and {$other->currency}"
            );
        }
    }
}
