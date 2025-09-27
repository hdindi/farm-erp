<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Batch Form Request
 *
 * Handles validation for updating existing batches.
 * Includes special rules for status transitions and population updates.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class UpdateBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has permission to update this specific batch
        $batch = $this->route('batch'); // Assuming route model binding

        return $this->user()?->can('update', $batch) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $batch = $this->route('batch');
        $batchId = $batch?->id;

        return [
            // Batch identification - can be updated but must remain unique
            'batch_code' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('batches', 'batch_code')->ignore($batchId),
            ],

            // Bird type and breed - usually shouldn't change after creation
            'bird_type_id' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                Rule::exists('bird_types', 'id'),
            ],
            'breed_id' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                Rule::exists('breeds', 'id'),
            ],

            // Source information
            'source_farm' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            // Bird age - can be adjusted if initial estimate was wrong
            'bird_age_days' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                'max:700',
            ],

            // Population can only decrease or stay the same (birds don't multiply)
            'initial_population' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:50000',
            ],
            'current_population' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($batch) {
                    // Current population can't exceed original initial population
                    $initialPop = $this->input('initial_population', $batch?->initial_population);
                    if ($value > $initialPop) {
                        $fail('Current population cannot exceed initial population.');
                    }

                    // Current population usually can only decrease
                    if ($batch && $value > $batch->current_population) {
                        // Allow increase only in special circumstances with explanation
                        if (! $this->has('population_increase_reason')) {
                            $fail('Population increase requires explanation in population_increase_reason field.');
                        }
                    }
                },
            ],

            // Population increase explanation
            'population_increase_reason' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],

            // Dates
            'date_received' => [
                'sometimes',
                'required',
                'date',
                'before_or_equal:today',
                'after:2020-01-01',
            ],
            'hatch_date' => [
                'sometimes',
                'nullable',
                'date',
                'before_or_equal:date_received',
                'after:2020-01-01',
            ],
            'expected_end_date' => [
                'sometimes',
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($batch) {
                    if ($value) {
                        $receivedDate = Carbon::parse($this->input('date_received', $batch?->date_received));
                        $endDate = Carbon::parse($value);

                        if ($endDate->isBefore($receivedDate)) {
                            $fail('Expected end date must be after the date received.');
                        }

                        if ($endDate->isAfter(Carbon::now()->addYears(5))) {
                            $fail('Expected end date is too far in the future.');
                        }
                    }
                },
            ],

            // Status transitions with specific rules
            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['active', 'completed', 'culled']),
                function ($attribute, $value, $fail) use ($batch) {
                    if ($batch && ! $this->isValidStatusTransition($batch->status, $value)) {
                        $fail("Cannot transition from {$batch->status} to {$value}.");
                    }
                },
            ],

            // Status change reason for auditing
            'status_change_reason' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
                'required_if:status,completed,culled',
            ],

            // Optional metadata
            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUpdateBusinessRules($validator);
        });
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'batch_code.unique' => 'This batch code is already in use by another batch.',
            'batch_code.regex' => 'Batch code must contain only uppercase letters, numbers, hyphens, and underscores.',

            'current_population.min' => 'Current population cannot be negative.',

            'status_change_reason.required_if' => 'Status change reason is required when marking batch as completed or culled.',

            'population_increase_reason.required' => 'Population increases must be explained.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'bird_type_id' => 'bird type',
            'breed_id' => 'breed',
            'bird_age_days' => 'bird age',
            'initial_population' => 'initial population',
            'current_population' => 'current population',
            'date_received' => 'date received',
            'hatch_date' => 'hatch date',
            'expected_end_date' => 'expected end date',
            'status_change_reason' => 'status change reason',
            'population_increase_reason' => 'population increase reason',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize batch code if provided
        if ($this->has('batch_code')) {
            $this->merge([
                'batch_code' => strtoupper($this->batch_code),
            ]);
        }

        // Set current population to initial if initial is being updated
        if ($this->has('initial_population') && ! $this->has('current_population')) {
            $batch = $this->route('batch');
            $this->merge([
                'current_population' => min($this->initial_population, $batch?->current_population ?? $this->initial_population),
            ]);
        }
    }

    /**
     * Get the validated data with proper type casting and filtering.
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();
        $data = [];

        // Only include fields that were actually submitted
        $allowedFields = [
            'batch_code', 'bird_type_id', 'breed_id', 'source_farm', 'bird_age_days',
            'initial_population', 'current_population', 'date_received', 'hatch_date',
            'expected_end_date', 'status', 'notes',
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $validated)) {
                $data[$field] = $this->castField($field, $validated[$field]);
            }
        }

        return $data;
    }

    /**
     * Get audit data for logging changes.
     */
    public function getAuditData(): array
    {
        $auditData = [];

        if ($this->has('status_change_reason')) {
            $auditData['status_change_reason'] = $this->input('status_change_reason');
        }

        if ($this->has('population_increase_reason')) {
            $auditData['population_increase_reason'] = $this->input('population_increase_reason');
        }

        return $auditData;
    }

    /**
     * Check if status transition is valid.
     */
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        $validTransitions = [
            'active' => ['completed', 'culled'],
            'completed' => [], // Final state - no transitions allowed
            'culled' => [], // Final state - no transitions allowed
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? [], true);
    }

    /**
     * Apply additional business rule validations for updates.
     */
    private function validateUpdateBusinessRules($validator): void
    {
        $data = $validator->getData();
        $batch = $this->route('batch');

        if (! $batch) {
            return;
        }

        // Validate bird type/breed changes
        if (isset($data['bird_type_id'], $data['breed_id'])) {
            $this->validateBirdTypeBreedCompatibility($validator, $data);
        }

        // Validate population changes
        if (isset($data['current_population'])) {
            $this->validatePopulationChange($validator, $batch, $data);
        }

        // Validate status changes
        if (isset($data['status'])) {
            $this->validateStatusChange($validator, $batch, $data);
        }

        // Validate date changes
        if (isset($data['date_received']) || isset($data['hatch_date']) || isset($data['expected_end_date'])) {
            $this->validateDateChanges($validator, $batch, $data);
        }
    }

    /**
     * Validate bird type and breed compatibility.
     */
    private function validateBirdTypeBreedCompatibility($validator, array $data): void
    {
        $breed = \App\Models\Breed::find($data['breed_id']);

        if ($breed && $breed->bird_type_id !== (int) $data['bird_type_id']) {
            $validator->errors()->add(
                'breed_id',
                'Selected breed is not compatible with the selected bird type.'
            );
        }
    }

    /**
     * Validate population changes.
     */
    private function validatePopulationChange($validator, $batch, array $data): void
    {
        $newPopulation = (int) $data['current_population'];
        $oldPopulation = $batch->current_population;

        // Check if population increased without justification
        if ($newPopulation > $oldPopulation && empty($data['population_increase_reason'])) {
            $validator->errors()->add(
                'population_increase_reason',
                'Population increases must be explained.'
            );
        }

        // Check for unrealistic population changes
        $changePercent = $oldPopulation > 0 ? abs(($newPopulation - $oldPopulation) / $oldPopulation) * 100 : 0;
        if ($changePercent > 50) { // More than 50% change
            $validator->errors()->add(
                'current_population',
                'Population change seems unusually large. Please verify the numbers.'
            );
        }
    }

    /**
     * Validate status changes.
     */
    private function validateStatusChange($validator, $batch, array $data): void
    {
        $newStatus = $data['status'];
        $oldStatus = $batch->status;

        // Don't allow status changes if there are recent daily records
        if ($newStatus === 'completed' || $newStatus === 'culled') {
            $recentRecord = $batch->dailyRecords()
                ->where('record_date', '>', Carbon::now()->subDays(3))
                ->exists();

            if ($recentRecord) {
                $validator->errors()->add(
                    'status',
                    'Cannot change status to final state when there are recent daily records. Please ensure all records are complete.'
                );
            }
        }

        // Require reason for final status changes
        if (in_array($newStatus, ['completed', 'culled']) && empty($data['status_change_reason'])) {
            $validator->errors()->add(
                'status_change_reason',
                'Status change reason is required for final status changes.'
            );
        }
    }

    /**
     * Validate date changes don't break existing data integrity.
     */
    private function validateDateChanges($validator, $batch, array $data): void
    {
        // Check if changing dates would invalidate existing daily records
        if (isset($data['date_received'])) {
            $newReceivedDate = Carbon::parse($data['date_received']);
            $earliestRecord = $batch->dailyRecords()->orderBy('record_date', 'asc')->first();

            if ($earliestRecord && $newReceivedDate->isAfter($earliestRecord->record_date)) {
                $validator->errors()->add(
                    'date_received',
                    'Cannot set received date after existing daily records. Earliest record: '.$earliestRecord->record_date->format('Y-m-d')
                );
            }
        }
    }

    /**
     * Cast field to appropriate type.
     */
    private function castField(string $field, $value)
    {
        return match ($field) {
            'bird_type_id', 'breed_id', 'bird_age_days', 'initial_population', 'current_population' => (int) $value,
            'date_received', 'hatch_date', 'expected_end_date' => $value ? Carbon::parse($value) : null,
            default => $value,
        };
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation($validator): void
    {
        $batch = $this->route('batch');

        // Log validation failures for monitoring
        \Illuminate\Support\Facades\Log::info('Batch update validation failed', [
            'user_id' => $this->user()?->id,
            'batch_id' => $batch?->id,
            'batch_code' => $batch?->batch_code,
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
        ]);

        parent::failedValidation($validator);
    }
}
