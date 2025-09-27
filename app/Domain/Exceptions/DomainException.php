<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * Base Domain Exception
 *
 * Base class for all domain-specific exceptions.
 * Provides common functionality and ensures consistent error handling
 * across the application's business logic layer.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
abstract class DomainException extends Exception
{
    /**
     * @param  string  $message  The exception message
     * @param  array  $context  Additional context data for logging
     * @param  int  $code  The exception code
     * @param  Exception|null  $previous  The previous exception
     */
    public function __construct(
        string $message = '',
        protected readonly array $context = [],
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get additional context data for logging and debugging
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get HTTP status code for this exception type
     */
    public function getHttpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    /**
     * Get error type identifier
     */
    public function getErrorType(): string
    {
        return 'DOMAIN_ERROR';
    }

    /**
     * Check if this exception should be reported to error tracking services
     */
    public function shouldReport(): bool
    {
        return true;
    }

    /**
     * Get user-friendly message for API responses
     */
    public function getUserMessage(): string
    {
        return $this->getMessage();
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
            ],
        ];
    }

    /**
     * Convert exception to JSON format for API responses
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get exception details for logging
     */
    public function getLogData(): array
    {
        return [
            'exception' => static::class,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'context' => $this->getContext(),
            'trace' => $this->getTraceAsString(),
        ];
    }
}
