<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Daily Record Form Request
 *
 * Handles validation for creating new daily records.
 * Ensures data integrity and business rule compliance for daily operations.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class StoreDailyRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\DailyRecord::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Batch association
            'batch_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('batches', 'id')->where('status', 'active'),
            ],

            // Record date
            'record_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:'.Carbon::now()->subYear()->toDateString(), // Not older than 1 year
                // Unique constraint handled in withValidator
            ],

            // Stage information
            'stage_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('stages', 'id'),
            ],
            'day_in_stage' => [
                'required',
                'integer',
                'min:1',
                'max:365', // Maximum days in any stage
            ],

            // Population counts
            'alive_count' => [
                'required',
                'integer',
                'min:0',
                'max:100000', // Reasonable maximum
            ],
            'dead_count' => [
                'required',
                'integer',
                'min:0',
                'max:10000', // Reasonable maximum for single day
            ],
            'culls_count' => [
                'required',
                'integer',
                'min:0',
                'max:10000', // Reasonable maximum for single day
            ],

            // Mortality rate (calculated but can be manually adjusted)
            'mortality_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            // Physical measurements
            'average_weight_grams' => [
                'nullable',
                'integer',
                'min:1',
                'max:10000', // 10kg maximum per bird
            ],

            // Notes
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Environmental data (optional)
            'temperature_celsius' => [
                'nullable',
                'numeric',
                'min:-20',
                'max:60',
            ],
            'humidity_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            // Nested egg production data
            'egg_production' => [
                'nullable',
                'array',
            ],
            'egg_production.total_eggs' => [
                'nullable',
                'integer',
                'min:0',
                'max:50000', // Based on alive count
            ],
            'egg_production.good_eggs' => [
                'nullable',
                'integer',
                'min:0',
                'lte:egg_production.total_eggs',
            ],
            'egg_production.cracked_eggs' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'egg_production.damaged_eggs' => [
                'nullable',
                'integer',
                'min:0',
            ],

            // Nested feed records
            'feed_records' => [
                'nullable',
                'array',
                'max:10', // Maximum 10 feed records per day
            ],
            'feed_records.*.feed_type_id' => [
                'required_with:feed_records',
                'integer',
                Rule::exists('feed_types', 'id'),
            ],
            'feed_records.*.quantity_kg' => [
                'required_with:feed_records',
                'numeric',
                'min:0.01',
                'max:10000',
            ],
            'feed_records.*.cost_per_kg' => [
                'required_with:feed_records',
                'numeric',
                'min:0.01',
                'max:1000',
            ],
            'feed_records.*.feeding_time' => [
                'nullable',
                'date_format:H:i',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateBusinessRules($validator);
        });
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'batch_id.required' => 'Please select a batch.',
            'batch_id.exists' => 'Selected batch does not exist or is not active.',

            'record_date.required' => 'Record date is required.',
            'record_date.before_or_equal' => 'Record date cannot be in the future.',
            'record_date.after' => 'Record date is too far in the past.',

            'stage_id.required' => 'Please select a stage.',
            'stage_id.exists' => 'Selected stage is invalid.',

            'alive_count.required' => 'Alive count is required.',
            'alive_count.min' => 'Alive count cannot be negative.',

            'dead_count.min' => 'Dead count cannot be negative.',
            'culls_count.min' => 'Culls count cannot be negative.',

            'mortality_rate.max' => 'Mortality rate cannot exceed 100%.',

            'average_weight_grams.min' => 'Average weight must be positive.',
            'average_weight_grams.max' => 'Average weight seems unrealistic.',

            'egg_production.good_eggs.lte' => 'Good eggs cannot exceed total eggs.',

            'feed_records.*.feed_type_id.required_with' => 'Feed type is required for feed records.',
            'feed_records.*.quantity_kg.required_with' => 'Feed quantity is required.',
            'feed_records.*.cost_per_kg.required_with' => 'Feed cost per kg is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'batch_id' => 'batch',
            'record_date' => 'record date',
            'stage_id' => 'stage',
            'day_in_stage' => 'day in stage',
            'alive_count' => 'alive count',
            'dead_count' => 'dead count',
            'culls_count' => 'culls count',
            'mortality_rate' => 'mortality rate',
            'average_weight_grams' => 'average weight',
            'temperature_celsius' => 'temperature',
            'humidity_percent' => 'humidity',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Calculate mortality rate if not provided
        if (! $this->has('mortality_rate') && $this->has(['alive_count', 'dead_count', 'culls_count'])) {
            $totalMortality = $this->dead_count + $this->culls_count;
            $totalStartPopulation = $this->alive_count + $totalMortality;

            $mortalityRate = $totalStartPopulation > 0
                ? ($totalMortality / $totalStartPopulation) * 100
                : 0;

            $this->merge(['mortality_rate' => round($mortalityRate, 2)]);
        }

        // Ensure egg production totals are consistent
        if ($this->has('egg_production')) {
            $eggProduction = $this->egg_production;

            if (isset($eggProduction['good_eggs'], $eggProduction['cracked_eggs'], $eggProduction['damaged_eggs'])) {
                $calculatedTotal = $eggProduction['good_eggs'] + $eggProduction['cracked_eggs'] + $eggProduction['damaged_eggs'];

                if (! isset($eggProduction['total_eggs'])) {
                    $eggProduction['total_eggs'] = $calculatedTotal;
                    $this->merge(['egg_production' => $eggProduction]);
                }
            }
        }
    }

    /**
     * Get the validated data with proper structure.
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();

        return [
            'batch_id' => (int) $validated['batch_id'],
            'record_date' => Carbon::parse($validated['record_date']),
            'stage_id' => (int) $validated['stage_id'],
            'day_in_stage' => (int) $validated['day_in_stage'],
            'alive_count' => (int) $validated['alive_count'],
            'dead_count' => (int) $validated['dead_count'],
            'culls_count' => (int) $validated['culls_count'],
            'mortality_rate' => $validated['mortality_rate'] ?? null,
            'average_weight_grams' => $validated['average_weight_grams'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'temperature_celsius' => $validated['temperature_celsius'] ?? null,
            'humidity_percent' => $validated['humidity_percent'] ?? null,
        ];
    }

    /**
     * Get nested relationship data.
     */
    public function getNestedData(): array
    {
        $validated = $this->validated();

        return [
            'egg_production' => $validated['egg_production'] ?? null,
            'feed_records' => $validated['feed_records'] ?? [],
        ];
    }

    /**
     * Apply business rule validations.
     */
    private function validateBusinessRules($validator): void
    {
        $data = $validator->getData();

        // Validate unique record date per batch
        if (! empty($data['batch_id']) && ! empty($data['record_date'])) {
            $this->validateUniqueRecordDate($validator, $data);
        }

        // Validate population consistency
        if (! empty($data['batch_id']) && isset($data['alive_count'])) {
            $this->validatePopulationConsistency($validator, $data);
        }

        // Validate stage progression
        if (! empty($data['batch_id']) && ! empty($data['stage_id'])) {
            $this->validateStageProgression($validator, $data);
        }

        // Validate egg production logic
        if (! empty($data['egg_production'])) {
            $this->validateEggProduction($validator, $data);
        }

        // Validate feed consumption reasonableness
        if (! empty($data['feed_records'])) {
            $this->validateFeedConsumption($validator, $data);
        }
    }

    /**
     * Validate unique record date per batch.
     */
    private function validateUniqueRecordDate($validator, array $data): void
    {
        $exists = \App\Models\DailyRecord::where('batch_id', $data['batch_id'])
            ->where('record_date', $data['record_date'])
            ->exists();

        if ($exists) {
            $validator->errors()->add(
                'record_date',
                'A daily record already exists for this batch on this date.'
            );
        }
    }

    /**
     * Validate population consistency with batch and previous records.
     */
    private function validatePopulationConsistency($validator, array $data): void
    {
        $batch = \App\Models\Batch::find($data['batch_id']);
        if (! $batch) {
            return;
        }

        $aliveCount = (int) $data['alive_count'];
        $deadCount = (int) $data['dead_count'];
        $cullsCount = (int) $data['culls_count'];
        $totalMortality = $deadCount + $cullsCount;

        // Check against batch current population
        if ($aliveCount > $batch->current_population) {
            $validator->errors()->add(
                'alive_count',
                "Alive count ({$aliveCount}) cannot exceed batch current population ({$batch->current_population})."
            );
        }

        // Get previous day's record for validation
        $previousRecord = \App\Models\DailyRecord::where('batch_id', $data['batch_id'])
            ->where('record_date', '<', $data['record_date'])
            ->orderBy('record_date', 'desc')
            ->first();

        if ($previousRecord) {
            $expectedAlive = $previousRecord->alive_count - $totalMortality;
            $tolerance = max(1, $previousRecord->alive_count * 0.05); // 5% tolerance

            if (abs($aliveCount - $expectedAlive) > $tolerance) {
                $validator->errors()->add(
                    'alive_count',
                    "Population count inconsistency detected. Expected approximately {$expectedAlive} based on previous record."
                );
            }
        }

        // Validate mortality is reasonable
        if ($previousRecord && $totalMortality > ($previousRecord->alive_count * 0.3)) { // More than 30% mortality
            $validator->errors()->add(
                'dead_count',
                'Mortality rate exceeds 30% in a single day. Please verify the numbers.'
            );
        }
    }

    /**
     * Validate stage progression makes sense.
     */
    private function validateStageProgression($validator, array $data): void
    {
        $batch = \App\Models\Batch::find($data['batch_id']);
        if (! $batch) {
            return;
        }

        $previousRecord = \App\Models\DailyRecord::where('batch_id', $data['batch_id'])
            ->orderBy('record_date', 'desc')
            ->first();

        if ($previousRecord) {
            $newStageId = (int) $data['stage_id'];
            $previousStageId = $previousRecord->stage_id;

            // Generally, stages should progress forward or stay the same
            $currentStage = \App\Models\Stage::find($newStageId);
            $previousStage = \App\Models\Stage::find($previousStageId);

            if ($currentStage && $previousStage) {
                // This would need stage ordering logic
                // For now, just ensure day_in_stage resets when stage changes
                if ($newStageId !== $previousStageId && ($data['day_in_stage'] ?? 0) > 1) {
                    $validator->errors()->add(
                        'day_in_stage',
                        'Day in stage should start from 1 when changing to a new stage.'
                    );
                }
            }
        }
    }

    /**
     * Validate egg production data.
     */
    private function validateEggProduction($validator, array $data): void
    {
        $eggProduction = $data['egg_production'];
        $aliveCount = (int) ($data['alive_count'] ?? 0);

        if (isset($eggProduction['total_eggs'])) {
            $totalEggs = (int) $eggProduction['total_eggs'];

            // Basic reasonableness check - can't produce more eggs than birds
            if ($totalEggs > $aliveCount) {
                $validator->errors()->add(
                    'egg_production.total_eggs',
                    'Total eggs cannot exceed the number of alive birds.'
                );
            }

            // Check for very high production (>95% would be exceptional)
            $productionRate = $aliveCount > 0 ? ($totalEggs / $aliveCount) * 100 : 0;
            if ($productionRate > 95) {
                $validator->errors()->add(
                    'egg_production.total_eggs',
                    'Production rate exceeds 95%. Please verify the egg count.'
                );
            }
        }

        // Validate egg quality breakdown
        if (isset($eggProduction['good_eggs'], $eggProduction['cracked_eggs'], $eggProduction['damaged_eggs'])) {
            $goodEggs = (int) $eggProduction['good_eggs'];
            $crackedEggs = (int) $eggProduction['cracked_eggs'];
            $damagedEggs = (int) $eggProduction['damaged_eggs'];
            $totalCalculated = $goodEggs + $crackedEggs + $damagedEggs;
            $totalReported = (int) ($eggProduction['total_eggs'] ?? 0);

            if ($totalCalculated !== $totalReported) {
                $validator->errors()->add(
                    'egg_production.total_eggs',
                    "Total eggs ({$totalReported}) doesn't match the sum of good, cracked, and damaged eggs ({$totalCalculated})."
                );
            }
        }
    }

    /**
     * Validate feed consumption data.
     */
    private function validateFeedConsumption($validator, array $data): void
    {
        $feedRecords = $data['feed_records'];
        $aliveCount = (int) ($data['alive_count'] ?? 0);

        $totalFeed = 0;
        foreach ($feedRecords as $index => $feedRecord) {
            $quantity = (float) ($feedRecord['quantity_kg'] ?? 0);
            $totalFeed += $quantity;

            // Validate feeding time if provided
            if (isset($feedRecord['feeding_time'])) {
                // Basic format validation is handled by date_format rule
                // Could add business logic here (e.g., no feeding at night)
            }
        }

        // Check for reasonable feed consumption (typical: 100-150g per bird per day)
        if ($aliveCount > 0) {
            $feedPerBird = ($totalFeed * 1000) / $aliveCount; // Convert to grams

            if ($feedPerBird < 50) { // Less than 50g per bird
                $validator->errors()->add(
                    'feed_records',
                    'Feed consumption seems too low for the number of birds.'
                );
            } elseif ($feedPerBird > 300) { // More than 300g per bird
                $validator->errors()->add(
                    'feed_records',
                    'Feed consumption seems too high for the number of birds.'
                );
            }
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation($validator): void
    {
        // Log validation failures for monitoring
        \Illuminate\Support\Facades\Log::info('Daily record creation validation failed', [
            'user_id' => $this->user()?->id,
            'batch_id' => $this->input('batch_id'),
            'record_date' => $this->input('record_date'),
            'errors' => $validator->errors()->toArray(),
        ]);

        parent::failedValidation($validator);
    }
}
