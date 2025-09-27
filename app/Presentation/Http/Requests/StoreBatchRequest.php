<?php

declare(strict_types=1);

namespace App\Presentation\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Batch Form Request
 *
 * Handles validation for creating new batches.
 * Encapsulates all validation rules and business logic for batch creation.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class StoreBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user has permission to create batches
        return $this->user()?->can('create', \App\Models\Batch::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Batch identification
            'batch_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-_]+$/', // Only uppercase letters, numbers, hyphens, underscores
                Rule::unique('batches', 'batch_code'),
            ],

            // Bird type and breed
            'bird_type_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('bird_types', 'id'),
            ],
            'breed_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('breeds', 'id'),
            ],

            // Source information
            'source_farm' => [
                'nullable',
                'string',
                'max:255',
            ],

            // Bird age and population
            'bird_age_days' => [
                'required',
                'integer',
                'min:0',
                'max:700', // ~2 years maximum
            ],
            'initial_population' => [
                'required',
                'integer',
                'min:1',
                'max:50000', // Maximum reasonable batch size
            ],
            'current_population' => [
                'nullable',
                'integer',
                'min:0',
                'lte:initial_population', // Cannot exceed initial population
            ],

            // Dates
            'date_received' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:2020-01-01', // Reasonable historical limit
            ],
            'hatch_date' => [
                'nullable',
                'date',
                'before_or_equal:date_received',
                'after:2020-01-01',
            ],
            'expected_end_date' => [
                'nullable',
                'date',
                'after:date_received',
                'before:'.Carbon::now()->addYears(5)->toDateString(), // Max 5 years in future
            ],

            // Status
            'status' => [
                'sometimes',
                'string',
                Rule::in(['active', 'completed', 'culled']),
            ],

            // Optional metadata
            'notes' => [
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
            $this->validateBusinessRules($validator);
        });
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'batch_code.required' => 'Batch code is required and must be unique.',
            'batch_code.regex' => 'Batch code must contain only uppercase letters, numbers, hyphens, and underscores.',
            'batch_code.unique' => 'This batch code is already in use. Please choose a different one.',

            'bird_type_id.required' => 'Please select a bird type.',
            'bird_type_id.exists' => 'Selected bird type is invalid.',

            'breed_id.required' => 'Please select a breed.',
            'breed_id.exists' => 'Selected breed is invalid.',

            'bird_age_days.min' => 'Bird age cannot be negative.',
            'bird_age_days.max' => 'Bird age seems unrealistic. Please verify.',

            'initial_population.required' => 'Initial population is required.',
            'initial_population.min' => 'Initial population must be at least 1 bird.',
            'initial_population.max' => 'Initial population exceeds maximum allowed batch size.',

            'current_population.lte' => 'Current population cannot exceed initial population.',

            'date_received.required' => 'Date received is required.',
            'date_received.before_or_equal' => 'Date received cannot be in the future.',
            'date_received.after' => 'Date received is too far in the past.',

            'hatch_date.before_or_equal' => 'Hatch date must be before or on the date received.',

            'expected_end_date.after' => 'Expected end date must be after the date received.',
            'expected_end_date.before' => 'Expected end date is too far in the future.',
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
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Set current population to initial if not provided
            'current_population' => $this->current_population ?? $this->initial_population,

            // Set default status if not provided
            'status' => $this->status ?? 'active',

            // Normalize batch code to uppercase
            'batch_code' => strtoupper($this->batch_code ?? ''),
        ]);
    }

    /**
     * Get the validated data with proper type casting.
     */
    public function getValidatedData(): array
    {
        $validated = $this->validated();

        return [
            'batch_code' => $validated['batch_code'],
            'bird_type_id' => (int) $validated['bird_type_id'],
            'breed_id' => (int) $validated['breed_id'],
            'source_farm' => $validated['source_farm'] ?? null,
            'bird_age_days' => (int) $validated['bird_age_days'],
            'initial_population' => (int) $validated['initial_population'],
            'current_population' => (int) $validated['current_population'],
            'date_received' => Carbon::parse($validated['date_received']),
            'hatch_date' => isset($validated['hatch_date'])
                ? Carbon::parse($validated['hatch_date'])
                : null,
            'expected_end_date' => isset($validated['expected_end_date'])
                ? Carbon::parse($validated['expected_end_date'])
                : null,
            'status' => $validated['status'] ?? 'active',
        ];
    }

    /**
     * Apply additional business rule validations.
     */
    private function validateBusinessRules($validator): void
    {
        $data = $validator->getData();

        // Validate bird type and breed compatibility
        if (! empty($data['bird_type_id']) && ! empty($data['breed_id'])) {
            $this->validateBirdTypeBreedCompatibility($validator, $data);
        }

        // Validate age logic
        if (! empty($data['hatch_date']) && ! empty($data['bird_age_days'])) {
            $this->validateAgeConsistency($validator, $data);
        }

        // Validate population density if area is configured
        if (! empty($data['initial_population'])) {
            $this->validatePopulationDensity($validator, $data);
        }

        // Validate expected end date logic
        if (! empty($data['hatch_date']) && ! empty($data['expected_end_date'])) {
            $this->validateLifespanLogic($validator, $data);
        }

        // Validate seasonal considerations
        if (! empty($data['date_received'])) {
            $this->validateSeasonalFactors($validator, $data);
        }
    }

    /**
     * Validate bird type and breed are compatible.
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
     * Validate age consistency with hatch date.
     */
    private function validateAgeConsistency($validator, array $data): void
    {
        $hatchDate = Carbon::parse($data['hatch_date']);
        $receivedDate = Carbon::parse($data['date_received']);
        $birdAgeDays = (int) $data['bird_age_days'];

        $calculatedAge = $hatchDate->diffInDays($receivedDate);

        // Allow some tolerance (±7 days)
        if (abs($calculatedAge - $birdAgeDays) > 7) {
            $validator->errors()->add(
                'bird_age_days',
                "Bird age ({$birdAgeDays} days) is inconsistent with hatch date and received date (calculated: {$calculatedAge} days)."
            );
        }
    }

    /**
     * Validate population density against facility capacity.
     */
    private function validatePopulationDensity($validator, array $data): void
    {
        $maxDensity = config('farm.max_birds_per_house', 5000);
        $initialPopulation = (int) $data['initial_population'];

        if ($initialPopulation > $maxDensity) {
            $validator->errors()->add(
                'initial_population',
                "Initial population exceeds facility capacity of {$maxDensity} birds."
            );
        }
    }

    /**
     * Validate lifespan logic makes sense.
     */
    private function validateLifespanLogic($validator, array $data): void
    {
        $hatchDate = Carbon::parse($data['hatch_date']);
        $expectedEndDate = Carbon::parse($data['expected_end_date']);

        $lifespanDays = $hatchDate->diffInDays($expectedEndDate);

        // Typical poultry lifespan: 70-100 weeks (490-700 days)
        if ($lifespanDays < 100) { // Too short
            $validator->errors()->add(
                'expected_end_date',
                'Expected lifespan is too short for typical poultry production.'
            );
        } elseif ($lifespanDays > 1000) { // Too long
            $validator->errors()->add(
                'expected_end_date',
                'Expected lifespan is unusually long for poultry production.'
            );
        }
    }

    /**
     * Validate seasonal factors and best practices.
     */
    private function validateSeasonalFactors($validator, array $data): void
    {
        $receivedDate = Carbon::parse($data['date_received']);
        $month = $receivedDate->month;

        // Example: warn about extreme weather seasons
        if (in_array($month, [12, 1, 2])) { // Winter months
            // Could add warnings about winter management
            // This is informational, not blocking validation
        }

        if (in_array($month, [6, 7, 8])) { // Summer months
            // Could add warnings about heat stress
            // This is informational, not blocking validation
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation($validator): void
    {
        // Log validation failures for monitoring
        \Illuminate\Support\Facades\Log::info('Batch creation validation failed', [
            'user_id' => $this->user()?->id,
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
        ]);

        parent::failedValidation($validator);
    }
}
