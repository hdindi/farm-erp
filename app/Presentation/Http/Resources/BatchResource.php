<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Batch API Resource
 *
 * Transforms batch model data into a consistent JSON API response format.
 * Provides different levels of detail based on context and includes
 * computed fields for better API usability.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class BatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_code' => $this->batch_code,
            'status' => $this->status,

            // Bird information
            'bird_type' => new BirdTypeResource($this->whenLoaded('birdType')),
            'breed' => new BreedResource($this->whenLoaded('breed')),
            'source_farm' => $this->source_farm,

            // Population data
            'population' => [
                'initial' => $this->initial_population,
                'current' => $this->current_population,
                'mortality_count' => $this->initial_population - $this->current_population,
                'mortality_rate' => $this->getMortalityRate(),
                'survival_rate' => $this->getSurvivalRate(),
            ],

            // Age and lifecycle
            'age' => [
                'days_at_arrival' => $this->bird_age_days,
                'current_age_days' => $this->getCurrentAge(),
                'lifecycle_stage' => $this->getLifecycleStage(),
            ],

            // Important dates
            'dates' => [
                'received' => $this->date_received?->toDateString(),
                'hatched' => $this->hatch_date?->toDateString(),
                'expected_end' => $this->expected_end_date?->toDateString(),
                'created' => $this->created_at?->toISOString(),
                'updated' => $this->updated_at?->toISOString(),
            ],

            // Status indicators
            'indicators' => [
                'is_active' => $this->status === 'active',
                'is_overdue' => $this->isOverdue(),
                'has_high_mortality' => $this->getMortalityRate() > 10.0,
                'requires_attention' => $this->requiresAttention(),
            ],

            // Performance metrics (when available)
            'performance' => $this->when(
                $this->relationLoaded('dailyRecords') && $this->dailyRecords->isNotEmpty(),
                fn () => $this->getPerformanceMetrics()
            ),

            // Recent activity
            'recent_activity' => $this->when(
                $this->relationLoaded('dailyRecords'),
                fn () => [
                    'last_record_date' => $this->dailyRecords->max('record_date'),
                    'days_since_last_record' => $this->getDaysSinceLastRecord(),
                    'total_records' => $this->dailyRecords->count(),
                ]
            ),

            // Relationships (conditional loading)
            'daily_records' => DailyRecordResource::collection($this->whenLoaded('dailyRecords')),
            'vaccination_schedules' => VaccineScheduleResource::collection($this->whenLoaded('vaccineSchedules')),
            'disease_incidents' => DiseaseManagementResource::collection($this->whenLoaded('diseaseManagement')),

            // Links for HATEOAS
            'links' => [
                'self' => route('api.batches.show', $this->id),
                'daily_records' => route('api.batches.daily-records.index', $this->id),
                'performance' => route('api.batches.performance', $this->id),
                'reports' => route('api.batches.reports', $this->id),
            ],

            // Metadata
            'meta' => $this->when($request->has('include_meta'), [
                'resource_version' => '1.0',
                'generated_at' => now()->toISOString(),
                'includes' => $this->getLoadedRelations(),
            ]),
        ];
    }

    /**
     * Calculate mortality rate percentage
     */
    private function getMortalityRate(): float
    {
        if ($this->initial_population === 0) {
            return 0.0;
        }

        $mortality = $this->initial_population - $this->current_population;

        return round(($mortality / $this->initial_population) * 100, 2);
    }

    /**
     * Calculate survival rate percentage
     */
    private function getSurvivalRate(): float
    {
        return round(100 - $this->getMortalityRate(), 2);
    }

    /**
     * Get current age in days
     */
    private function getCurrentAge(): int
    {
        if ($this->hatch_date) {
            return $this->hatch_date->diffInDays(now());
        }

        return $this->date_received->diffInDays(now()) + $this->bird_age_days;
    }

    /**
     * Get lifecycle stage based on age
     */
    private function getLifecycleStage(): string
    {
        $age = $this->getCurrentAge();

        return match (true) {
            $age <= 7 => 'chick',
            $age <= 28 => 'young',
            $age <= 112 => 'grower', // 16 weeks
            $age <= 504 => 'layer',  // 72 weeks
            default => 'mature'
        };
    }

    /**
     * Check if batch is overdue
     */
    private function isOverdue(): bool
    {
        return $this->expected_end_date
            && now()->isAfter($this->expected_end_date)
            && $this->status === 'active';
    }

    /**
     * Check if batch requires attention
     */
    private function requiresAttention(): bool
    {
        return $this->isOverdue()
            || $this->getMortalityRate() > 15.0
            || ($this->status === 'active' && $this->current_population === 0);
    }

    /**
     * Get performance metrics from daily records
     */
    private function getPerformanceMetrics(): array
    {
        if (! $this->relationLoaded('dailyRecords') || $this->dailyRecords->isEmpty()) {
            return [];
        }

        $records = $this->dailyRecords;
        $latestRecord = $records->sortByDesc('record_date')->first();

        return [
            'total_records' => $records->count(),
            'latest_record_date' => $latestRecord?->record_date?->toDateString(),
            'average_daily_mortality' => round($records->avg(fn ($record) => $record->dead_count + $record->culls_count), 2),
            'peak_mortality_day' => $records->max(fn ($record) => $record->dead_count + $record->culls_count),
            'average_weight' => round($records->whereNotNull('average_weight_grams')->avg('average_weight_grams'), 0),
            'weight_trend' => $this->getWeightTrend($records),
        ];
    }

    /**
     * Get weight trend direction
     */
    private function getWeightTrend($records): string
    {
        $weightRecords = $records->whereNotNull('average_weight_grams')->sortBy('record_date');

        if ($weightRecords->count() < 2) {
            return 'insufficient_data';
        }

        $first = $weightRecords->first()->average_weight_grams;
        $last = $weightRecords->last()->average_weight_grams;
        $percentChange = (($last - $first) / $first) * 100;

        return match (true) {
            $percentChange > 5 => 'increasing',
            $percentChange < -5 => 'decreasing',
            default => 'stable'
        };
    }

    /**
     * Get days since last daily record
     */
    private function getDaysSinceLastRecord(): ?int
    {
        if (! $this->relationLoaded('dailyRecords') || $this->dailyRecords->isEmpty()) {
            return null;
        }

        $lastRecordDate = $this->dailyRecords->max('record_date');

        return $lastRecordDate ? now()->diffInDays($lastRecordDate) : null;
    }

    /**
     * Get loaded relationships for metadata
     */
    private function getLoadedRelations(): array
    {
        return array_keys($this->resource->getRelations());
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, $response): void
    {
        // Add custom headers for API versioning and caching
        $response->header('X-Resource-Version', '1.0')
            ->header('Cache-Control', 'private, max-age=300'); // 5 minutes cache
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'api_version' => '1.0',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Create a resource collection with consistent metadata
     */
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'resource_type' => 'batch_collection',
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }
}
