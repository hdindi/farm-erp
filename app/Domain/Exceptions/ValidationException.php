<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use Illuminate\Http\Response;

/**
 * Validation Exception
 *
 * Thrown when domain business rules validation fails.
 * Distinct from HTTP form validation errors, this handles
 * complex business logic validation within the domain layer.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class ValidationException extends DomainException
{
    /**
     * @param  string  $message  The main validation error message
     * @param  array  $errors  Detailed validation errors (field => [messages])
     * @param  array  $context  Additional context for debugging
     */
    public function __construct(
        string $message = 'Validation failed',
        private readonly array $errors = [],
        array $context = []
    ) {
        parent::__construct($message, $context, 4220);
    }

    /**
     * Create validation exception with single error
     */
    public static function single(string $field, string $message, array $context = []): self
    {
        return new self(
            message: "Validation failed for field: {$field}",
            errors: [$field => [$message]],
            context: $context
        );
    }

    /**
     * Create validation exception with multiple errors
     */
    public static function multiple(array $errors, ?string $message = null, array $context = []): self
    {
        $message = $message ?? 'Multiple validation errors occurred';

        return new self(
            message: $message,
            errors: $errors,
            context: $context
        );
    }

    /**
     * Create validation exception for business rule violation
     */
    public static function businessRule(string $rule, string $message, array $context = []): self
    {
        return new self(
            message: "Business rule violation: {$rule}",
            errors: ['business_rule' => [$message]],
            context: array_merge($context, ['violated_rule' => $rule])
        );
    }

    /**
     * Create validation exception for population-related errors
     */
    public static function population(string $message, array $populationData = []): self
    {
        return new self(
            message: "Population validation failed: {$message}",
            errors: ['population' => [$message]],
            context: ['population_data' => $populationData]
        );
    }

    /**
     * Create validation exception for date-related errors
     */
    public static function dateLogic(string $message, array $dates = []): self
    {
        return new self(
            message: "Date validation failed: {$message}",
            errors: ['dates' => [$message]],
            context: ['date_data' => $dates]
        );
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if exception has errors for specific field
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Get errors for specific field
     */
    public function getErrorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Get all error messages as flat array
     */
    public function getAllErrorMessages(): array
    {
        $messages = [];

        foreach ($this->errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $messages[] = $error;
            }
        }

        return $messages;
    }

    /**
     * Get HTTP status code for this exception type
     */
    public function getHttpStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * Get error type identifier
     */
    public function getErrorType(): string
    {
        return 'VALIDATION_ERROR';
    }

    /**
     * Get user-friendly message
     */
    public function getUserMessage(): string
    {
        if (empty($this->errors)) {
            return $this->getMessage();
        }

        $errorCount = count($this->getAllErrorMessages());

        if ($errorCount === 1) {
            return 'Please correct the validation error and try again.';
        }

        return "Please correct the {$errorCount} validation errors and try again.";
    }

    /**
     * Convert exception to array format for API responses
     */
    public function toArray(): array
    {
        return [
            'error' => [
                'type' => $this->getErrorType(),
                'message' => $this->getUserMessage(),
                'code' => $this->getCode(),
                'errors' => $this->errors,
            ],
        ];
    }

    /**
     * Get exception details for logging
     */
    public function getLogData(): array
    {
        return array_merge(parent::getLogData(), [
            'validation_errors' => $this->errors,
            'error_count' => count($this->getAllErrorMessages()),
        ]);
    }

    /**
     * This exception should be reported for business rule violations but not for simple validation errors
     */
    public function shouldReport(): bool
    {
        // Report if it's a business rule violation
        return $this->hasError('business_rule');
    }
}
