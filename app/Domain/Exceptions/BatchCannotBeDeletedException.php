<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use App\Models\Batch;
use Illuminate\Http\Response;

/**
 * Batch Cannot Be Deleted Exception
 *
 * Thrown when attempting to delete a batch that cannot be deleted
 * due to business rules or data integrity constraints.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class BatchCannotBeDeletedException extends DomainException
{
    /**
     * Create exception for active batch with living birds
     */
    public static function activeBatchWithLivingBirds(Batch $batch): self
    {
        return new self(
            message: "Cannot delete active batch '{$batch->batch_code}' with {$batch->current_population} living birds.",
            context: [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'status' => $batch->status,
                'current_population' => $batch->current_population,
            ],
            code: 4091
        );
    }

    /**
     * Create exception for batch with related records
     */
    public static function hasRelatedRecords(Batch $batch, array $relatedCounts): self
    {
        $relatedSummary = [];
        foreach ($relatedCounts as $type => $count) {
            if ($count > 0) {
                $relatedSummary[] = "{$count} {$type}";
            }
        }

        $relatedText = implode(', ', $relatedSummary);

        return new self(
            message: "Cannot delete batch '{$batch->batch_code}' because it has related records: {$relatedText}.",
            context: [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'related_records' => $relatedCounts,
            ],
            code: 4092
        );
    }

    /**
     * Create exception for batch with financial transactions
     */
    public static function hasFinancialTransactions(Batch $batch): self
    {
        return new self(
            message: "Cannot delete batch '{$batch->batch_code}' because it has associated financial transactions.",
            context: [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'reason' => 'financial_transactions',
            ],
            code: 4093
        );
    }

    /**
     * Create exception for archived batch
     */
    public static function archivedBatch(Batch $batch): self
    {
        return new self(
            message: "Cannot delete archived batch '{$batch->batch_code}'. Archived batches are protected from deletion.",
            context: [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'archived_at' => $batch->archived_at?->toISOString(),
            ],
            code: 4094
        );
    }

    /**
     * Create exception for batch with audit requirements
     */
    public static function auditRequirement(Batch $batch, string $reason): self
    {
        return new self(
            message: "Cannot delete batch '{$batch->batch_code}' due to audit requirements: {$reason}",
            context: [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'audit_reason' => $reason,
            ],
            code: 4095
        );
    }

    /**
     * Create exception for batch with recent activity
     */
    public static function recentActivity(Batch $batch, string $lastActivity): self
    {
        return new self(
            message: "Cannot delete batch '{$batch->batch_code}' with recent activity. Last activity: {$lastActivity}",
            context: [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'last_activity' => $lastActivity,
            ],
            code: 4096
        );
    }

    /**
     * Create exception for insufficient permissions
     */
    public static function insufficientPermissions(Batch $batch): self
    {
        return new self(
            message: "Insufficient permissions to delete batch '{$batch->batch_code}'.",
            context: [
                'batch_id' => $batch->id,
                'batch_code' => $batch->batch_code,
                'reason' => 'insufficient_permissions',
            ],
            code: 4097
        );
    }

    /**
     * Get HTTP status code for this exception type
     */
    public function getHttpStatusCode(): int
    {
        return match ($this->getCode()) {
            4097 => Response::HTTP_FORBIDDEN,
            default => Response::HTTP_CONFLICT,
        };
    }

    /**
     * Get error type identifier
     */
    public function getErrorType(): string
    {
        return 'BATCH_CANNOT_BE_DELETED';
    }

    /**
     * Get user-friendly message
     */
    public function getUserMessage(): string
    {
        return match ($this->getCode()) {
            4091 => 'This batch cannot be deleted because it is still active and has living birds. Please complete or cull the batch first.',
            4092 => 'This batch cannot be deleted because it has related records (daily records, sales, etc.). Archive the batch instead.',
            4093 => 'This batch cannot be deleted because it has financial transactions. Contact an administrator if deletion is necessary.',
            4094 => 'Archived batches cannot be deleted to maintain data integrity for historical reporting.',
            4095 => 'This batch cannot be deleted due to audit and compliance requirements.',
            4096 => 'This batch cannot be deleted due to recent activity. Wait for the activity to complete or contact an administrator.',
            4097 => 'You do not have permission to delete this batch. Contact your administrator for assistance.',
            default => 'This batch cannot be deleted due to business rules or data constraints.',
        };
    }

    /**
     * Get suggested actions for the user
     */
    public function getSuggestedActions(): array
    {
        return match ($this->getCode()) {
            4091 => [
                'Complete the batch lifecycle by updating the batch status to "completed"',
                'If necessary, update the batch status to "culled"',
                'Archive the batch instead of deleting it',
            ],
            4092 => [
                'Archive the batch to preserve historical data',
                'Export batch data if needed for external storage',
                'Contact administrator if deletion is absolutely necessary',
            ],
            4093 => [
                'Archive the batch to maintain financial audit trail',
                'Contact finance team or administrator',
                'Review financial transactions before attempting deletion',
            ],
            4094 => [
                'Archived batches should not be deleted',
                'Contact administrator if this is an error',
                'Review archival policies if needed',
            ],
            4095 => [
                'Follow company audit procedures',
                'Contact compliance team',
                'Archive instead of delete',
            ],
            4096 => [
                'Wait for ongoing operations to complete',
                'Check for active daily records or pending transactions',
                'Try again later or contact administrator',
            ],
            4097 => [
                'Contact your system administrator',
                'Request appropriate permissions',
                'Use archive function if available',
            ],
            default => [
                'Review batch status and related records',
                'Consider archiving instead of deleting',
                'Contact administrator for assistance',
            ],
        };
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
                'suggested_actions' => $this->getSuggestedActions(),
                'context' => $this->getContext(),
            ],
        ];
    }
}
