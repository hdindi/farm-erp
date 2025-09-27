<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use Illuminate\Http\Response;

/**
 * Batch Not Found Exception
 *
 * Thrown when a requested batch cannot be found in the system.
 * This exception indicates that the batch ID or code provided
 * does not match any existing batch record.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class BatchNotFoundException extends DomainException
{
    /**
     * Create exception for batch not found by ID
     */
    public static function byId(int $batchId): self
    {
        return new self(
            message: "Batch with ID {$batchId} was not found.",
            context: ['batch_id' => $batchId],
            code: 4041 // 404 + domain specific code
        );
    }

    /**
     * Create exception for batch not found by code
     */
    public static function byCode(string $batchCode): self
    {
        return new self(
            message: "Batch with code '{$batchCode}' was not found.",
            context: ['batch_code' => $batchCode],
            code: 4042
        );
    }

    /**
     * Create exception for batch not found with custom criteria
     */
    public static function byCriteria(array $criteria): self
    {
        $criteriaString = json_encode($criteria);

        return new self(
            message: 'No batch found matching the specified criteria.',
            context: ['search_criteria' => $criteria],
            code: 4043
        );
    }

    /**
     * Get HTTP status code for this exception type
     */
    public function getHttpStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    /**
     * Get error type identifier
     */
    public function getErrorType(): string
    {
        return 'BATCH_NOT_FOUND';
    }

    /**
     * This exception should not be reported as it's expected behavior
     */
    public function shouldReport(): bool
    {
        return false;
    }

    /**
     * Get user-friendly message
     */
    public function getUserMessage(): string
    {
        return 'The requested batch could not be found. Please verify the batch ID or code and try again.';
    }
}
