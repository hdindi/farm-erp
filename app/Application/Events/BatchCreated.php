<?php

declare(strict_types=1);

namespace App\Application\Events;

use App\Models\Batch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Batch Created Event
 *
 * Fired when a new batch is successfully created in the system.
 * This event can trigger various side effects such as:
 * - Creating vaccination schedules
 * - Notifying relevant personnel
 * - Updating dashboard metrics
 * - Logging audit records
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class BatchCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  Batch  $batch  The newly created batch
     * @param  array  $metadata  Additional context about the batch creation
     */
    public function __construct(
        public readonly Batch $batch,
        public readonly array $metadata = []
    ) {}

    /**
     * Get the batch with necessary relationships loaded
     */
    public function getBatch(): Batch
    {
        return $this->batch->load(['birdType', 'breed']);
    }

    /**
     * Get creation context metadata
     */
    public function getMetadata(): array
    {
        return array_merge([
            'created_at' => now()->toISOString(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ], $this->metadata);
    }

    /**
     * Get event data for logging
     */
    public function getLogData(): array
    {
        return [
            'event' => 'batch_created',
            'batch_id' => $this->batch->id,
            'batch_code' => $this->batch->batch_code,
            'initial_population' => $this->batch->initial_population,
            'bird_type' => $this->batch->birdType?->name,
            'breed' => $this->batch->breed?->name,
            'metadata' => $this->getMetadata(),
        ];
    }

    /**
     * Check if this batch requires special handling
     */
    public function requiresSpecialHandling(): bool
    {
        // Large batches might need special handling
        if ($this->batch->initial_population > 5000) {
            return true;
        }

        // Young birds need special care
        if ($this->batch->bird_age_days < 7) {
            return true;
        }

        return false;
    }

    /**
     * Get suggested actions for this batch
     */
    public function getSuggestedActions(): array
    {
        $actions = [];

        // Always create vaccination schedule
        $actions[] = 'create_vaccination_schedule';

        // Create feed plan
        $actions[] = 'create_feed_plan';

        // Notify farm manager
        $actions[] = 'notify_farm_manager';

        // Special handling for large batches
        if ($this->batch->initial_population > 5000) {
            $actions[] = 'notify_senior_management';
            $actions[] = 'verify_facility_capacity';
        }

        // Special handling for young birds
        if ($this->batch->bird_age_days < 7) {
            $actions[] = 'setup_brooding_environment';
            $actions[] = 'schedule_frequent_monitoring';
        }

        // End-of-life planning for older birds
        if ($this->batch->bird_age_days > 300) {
            $actions[] = 'plan_culling_schedule';
        }

        return $actions;
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
                'initial_population' => $this->batch->initial_population,
                'status' => $this->batch->status,
                'bird_type' => $this->batch->birdType?->name,
                'breed' => $this->batch->breed?->name,
                'date_received' => $this->batch->date_received->toISOString(),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get tags for this event (for filtering/routing)
     */
    public function getTags(): array
    {
        $tags = ['batch', 'created'];

        // Add bird type tag
        if ($this->batch->birdType) {
            $tags[] = 'bird_type:'.$this->batch->birdType->name;
        }

        // Add size category tag
        if ($this->batch->initial_population > 5000) {
            $tags[] = 'large_batch';
        } elseif ($this->batch->initial_population < 1000) {
            $tags[] = 'small_batch';
        } else {
            $tags[] = 'medium_batch';
        }

        // Add age category tag
        if ($this->batch->bird_age_days < 7) {
            $tags[] = 'chicks';
        } elseif ($this->batch->bird_age_days < 56) {
            $tags[] = 'young_birds';
        } else {
            $tags[] = 'mature_birds';
        }

        return $tags;
    }
}
