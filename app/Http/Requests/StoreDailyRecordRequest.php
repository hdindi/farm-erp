<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Batch;

class StoreDailyRecordRequest extends FormRequest
{
    // ... authorize() method ...

//    public function rules(): array
//    {
//        $batchId = $this->input('batch_id');
//        $batch = Batch::find($batchId);
//        $maxAlive = $batch ? $batch->current_population : 0;
//
//        return [
//            'batch_id' => ['required', 'exists:batches,id'],
//            'record_date' => [
//                'required',
//                'date',
//                'before_or_equal:today',
//                Rule::unique('daily_records')->where(function ($query) use ($batchId) {
//                    return $query->where('batch_id', $batchId);
//                })->ignore($this->daily_record), // Ignore current record on update
//            ],
//            'stage_id' => ['required', 'exists:stages,id'],
//
//            // ✅ CORRECTED: This field is now validated correctly
////            'days_in_stage' => [
////                'required',
////                'integer',
////                'min:1'
////            ],
//
//            'alive_count' => ['required', 'integer', 'min:0', 'max:' . $maxAlive],
//            'dead_count' => ['required', 'integer', 'min:0'],
//            'culls_count' => ['required', 'integer', 'min:0'],
//            'average_weight_grams' => ['nullable', 'numeric', 'min:0'],
//            'notes' => ['nullable', 'string'],
//        ];
//    }
//
//    public function messages(): array
//    {
//        return [
//            'batch_id.required' => 'You must select a batch.',
//            'record_date.unique' => 'A daily record for this batch on this date already exists.',
//            'record_date.before_or_equal' => 'The record date cannot be in the future.',
//            'alive_count.max' => 'The number of alive birds cannot be greater than the current batch population.',
//            'days_in_stage.required' => 'The Day # in Stage could not be calculated. Please re-select the batch.',
//        ];
//    }
}
