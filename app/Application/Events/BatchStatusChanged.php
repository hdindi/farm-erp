<?php

declare(strict_types=1);

namespace App\Application\Events;

use App\Models\Batch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Batch Status Changed Event
 *
 * Fired when a batch status changes (active -> completed/culled).
 * This event triggers various cleanup and finalization processes.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class BatchStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  Batch  $batch  The batch with updated status
     * @param  string  $previousStatus  The previous status
     * @param  string  $reason  Reason for status change
     * @param  array  $metadata  Additional context
     */
    public function __construct(
        public readonly Batch $batch,
        public readonly string $previousStatus,
        public readonly string $reason = '',
        public readonly array $metadata = []
    ) {}

    /**
     * Get the current status
     */
    public function getCurrentStatus(): string
    {
        return $this->batch->status;
    }

    /**
     * Check if batch was completed
     */
    public function wasCompleted(): bool
    {
        return $this->getCurrentStatus() === 'completed';
    }

    /**
     * Check if batch was culled
     */
    public function wasCulled(): bool
    {
        return $this->getCurrentStatus() === 'culled';
    }

    /**
     * Check if batch became final (completed or culled)
     */
    public function becameFinal(): bool
    {
        return $this->wasCompleted() || $this->wasCulled();
    }

    /**
     * Get event data for logging
     */
    public function getLogData(): array
    {
        return [
            'event' => 'batch_status_changed',
            'batch_id' => $this->batch->id,
            'batch_code' => $this->batch->batch_code,
            'previous_status' => $this->previousStatus,
            'current_status' => $this->getCurrentStatus(),
            'reason' => $this->reason,
            'final_population' => $this->batch->current_population,
            'mortality_rate' => $this->calculateMortalityRate(),
            'lifecycle_days' => $this->calculateLifecycleDays(),
            'metadata' => $this->metadata,
            'changed_at' => now()->toISOString(),
            'changed_by' => auth()->id(),
        ];
    }

    /**
     * Calculate mortality rate for the batch
     */
    public function calculateMortalityRate(): float
    {
        if ($this->batch->initial_population === 0) {
            return 0.0;
        }

        $mortality = $this->batch->initial_population - $this->batch->current_population;

        return round(($mortality / $this->batch->initial_population) * 100, 2);
    }

    /**
     * Calculate total lifecycle days
     */
    public function calculateLifecycleDays(): int
    {
        if ($this->batch->hatch_date) {
            return $this->batch->hatch_date->diffInDays(now());
        }

        return $this->batch->date_received->diffInDays(now()) + $this->batch->bird_age_days;
    }

    /**
     * Get actions triggered by this status change
     */
    public function getTriggeredActions(): array
    {
        $actions = [];

        if ($this->becameFinal()) {
            $actions[] = 'generate_final_report';
            $actions[] = 'calculate_profitability';
            $actions[] = 'update_performance_metrics';
            $actions[] = 'notify_management';

            if ($this->wasCompleted()) {
                $actions[] = 'schedule_cleanup';
                $actions[] = 'process_final_sales';
            }

            if ($this->wasCulled()) {
                $actions[] = 'document_culling_reason';
                $actions[] = 'notify_veterinarian';
                $actions[] = 'update_health_protocols';
            }
        }

        return $actions;
    }

    /**
     * Get performance summary
     */
    public function getPerformanceSummary(): array
    {
        return [
            'batch_code' => $this->batch->batch_code,
            'lifecycle_days' => $this->calculateLifecycleDays(),
            'initial_population' => $this->batch->initial_population,
            'final_population' => $this->batch->current_population,
            'mortality_rate' => $this->calculateMortalityRate(),
            'completion_status' => $this->getCurrentStatus(),
            'completion_reason' => $this->reason,
        ];
    }

    /**
     * Check if performance was concerning
     */
    public function hasPerformanceConcerns(): bool
    {
        // High mortality rate
        if ($this->calculateMortalityRate() > 15.0) {
            return true;
        }

        // Premature culling
        if ($this->wasCulled() && $this->calculateLifecycleDays() < 365) {
            return true;
        }

        // Very short lifecycle
        if ($this->becameFinal() && $this->calculateLifecycleDays() < 180) {
            return true;
        }

        return false;
    }

    /**
     * Get concerns and recommendations
     */
    public function getConcernsAndRecommendations(): array
    {
        $concerns = [];

        if ($this->calculateMortalityRate() > 15.0) {
            $concerns['high_mortality'] = [
                'concern' => 'Mortality rate exceeds 15%',
                'recommendations' => [
                    'Review health management protocols',
                    'Investigate disease outbreaks',
                    'Check environmental conditions',
                    'Review feed quality and nutrition',
                ],
            ];
        }

        if ($this->wasCulled() && $this->calculateLifecycleDays() < 365) {
            $concerns['premature_culling'] = [
                'concern' => 'Batch was culled before expected lifecycle completion',
                'recommendations' => [
                    'Document culling reasons',
                    'Review decision-making process',
                    'Analyze cost-benefit of culling vs. completion',
                    'Implement preventive measures',
                ],
            ];
        }

        if ($this->becameFinal() && $this->calculateLifecycleDays() < 180) {
            $concerns['short_lifecycle'] = [
                'concern' => 'Very short batch lifecycle',
                'recommendations' => [
                    'Investigate underlying causes',
                    'Review batch planning processes',
                    'Check for disease or environmental issues',
                    'Consider adjusting management practices',
                ],
            ];
        }

        return $concerns;
    }

    /**
     * Get broadcast data for real-time updates
     */
    public function broadcastWith(): array
    {
        return [
            'batch' => [
                'id' => $this->batch->id,
                'batch_code' => $this->batch->batch_code,
                'previous_status' => $this->previousStatus,
                'current_status' => $this->getCurrentStatus(),
                'reason' => $this->reason,
            ],
            'performance' => $this->getPerformanceSummary(),
            'has_concerns' => $this->hasPerformanceConcerns(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get tags for this event
     */
    public function getTags(): array
    {
        $tags = ['batch', 'status_changed', "status:{$this->getCurrentStatus()}"];

        if ($this->becameFinal()) {
            $tags[] = 'final_status';
        }

        if ($this->hasPerformanceConcerns()) {
            $tags[] = 'performance_concerns';
        }

        return $tags;
    }
}
