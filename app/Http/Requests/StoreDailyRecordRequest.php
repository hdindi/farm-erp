<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Batch;

class StoreDailyRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set to true to allow anyone who is authenticated to use this form.
        // You can add more complex authorization logic here if needed.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $batchId = $this->input('batch_id');
        $batch = Batch::find($batchId);
        $maxAlive = $batch ? $batch->current_population : 0;

        return [
            'batch_id' => [
                'required',
                'exists:batches,id'
            ],
            'record_date' => [
                'required',
                'date',
                'before_or_equal:today',
                // This ensures you cannot enter a duplicate record for the same batch on the same day.
                Rule::unique('daily_records')->where(function ($query) use ($batchId) {
                    return $query->where('batch_id', $batchId);
                }),
            ],
            'stage_id' => [
                'required',
                'exists:stages,id'
            ],
            'alive_count' => [
                'required',
                'integer',
                'min:0',
                // The number of alive birds cannot exceed the batch's last known population.
                'max:' . $maxAlive,
            ],
            'dead_count' => [
                'required',
                'integer',
                'min:0'
            ],
            'culls_count' => [
                'required',
                'integer',
                'min:0'
            ],
            'average_weight_grams' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'notes' => [
                'nullable',
                'string'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'batch_id.required' => 'You must select a batch.',
            'record_date.unique' => 'A daily record for this batch on this date already exists.',
            'record_date.before_or_equal' => 'The record date cannot be in the future.',
            'alive_count.max' => 'The number of alive birds cannot be greater than the current batch population.',
        ];
    }
}
