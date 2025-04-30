{{-- resources/views/egg-production/_form.blade.php --}}

{{-- Daily Record (linked to Batch) --}}
<div class="mb-3">
    <label for="daily_record_id" class="form-label">Batch & Record Date <span class="text-danger">*</span></label>
    <select class="form-select @error('daily_record_id') is-invalid @enderror" id="daily_record_id" name="daily_record_id" required>
        <option value="">Select Batch and Date</option>
        {{-- $dailyRecords passed from controller (filtered for layers > 18 weeks) --}}
        @foreach($dailyRecords as $record)
            <option value="{{ $record->id }}"
                {{ (old('daily_record_id', $eggProduction->daily_record_id ?? request('daily_record_id')) == $record->id) ? 'selected' : '' }}>
                {{ $record->batch->batch_code ?? 'Unknown Batch' }} - {{ $record->record_date->format('Y-m-d') }} (Alive: {{ $record->alive_count }})
            </option>
        @endforeach
    </select>
    @error('daily_record_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Select the daily record entry this egg collection applies to.</small>
</div>


<div class="row mb-3">
    {{-- Total Eggs --}}
    <div class="col-md-3">
        <label for="total_eggs" class="form-label">Total Eggs Collected <span class="text-danger">*</span></label>
        <input type="number" class="form-control egg-count @error('total_eggs') is-invalid @enderror" id="total_eggs" name="total_eggs"
               value="{{ old('total_eggs', $eggProduction->total_eggs ?? '0') }}" required min="0">
        @error('total_eggs')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Good Eggs --}}
    <div class="col-md-3">
        <label for="good_eggs" class="form-label">Good Eggs <span class="text-danger">*</span></label>
        <input type="number" class="form-control egg-count @error('good_eggs') is-invalid @enderror" id="good_eggs" name="good_eggs"
               value="{{ old('good_eggs', $eggProduction->good_eggs ?? '0') }}" required min="0">
        @error('good_eggs')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Cracked Eggs --}}
    <div class="col-md-3">
        <label for="cracked_eggs" class="form-label">Cracked Eggs <span class="text-danger">*</span></label>
        <input type="number" class="form-control egg-count @error('cracked_eggs') is-invalid @enderror" id="cracked_eggs" name="cracked_eggs"
               value="{{ old('cracked_eggs', $eggProduction->cracked_eggs ?? '0') }}" required min="0">
        @error('cracked_eggs')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Damaged Eggs --}}
    <div class="col-md-3">
        <label for="damaged_eggs" class="form-label">Damaged/Other Eggs <span class="text-danger">*</span></label>
        <input type="number" class="form-control egg-count @error('damaged_eggs') is-invalid @enderror" id="damaged_eggs" name="damaged_eggs"
               value="{{ old('damaged_eggs', $eggProduction->damaged_eggs ?? '0') }}" required min="0">
        @error('damaged_eggs')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    {{-- Validation message area for egg counts --}}
    <div id="egg-count-validation-msg" class="text-danger mt-2" style="display: none;">
        The sum of Good, Cracked, and Damaged eggs must equal the Total Eggs Collected.
    </div>
</div>

<div class="row mb-3">
    {{-- Collection Time --}}
    <div class="col-md-6">
        <label for="collection_time" class="form-label">Collection Time (Optional)</label>
        <input type="time" class="form-control @error('collection_time') is-invalid @enderror" id="collection_time" name="collection_time"
               value="{{ old('collection_time', $eggProduction->collection_time ?? '') }}">
        @error('collection_time')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Notes --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Optional: Observations during collection, egg size/quality notes, etc.">{{ old('notes', $eggProduction->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-warning text-dark"> {{-- Changed color --}}
        <i class="fas fa-save"></i> {{ isset($eggProduction) ? 'Update Egg Record' : 'Save Egg Record' }}
    </button>
    <a href="{{ route('egg-production.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>

{{-- Script for client-side egg count validation --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalInput = document.getElementById('total_eggs');
            const goodInput = document.getElementById('good_eggs');
            const crackedInput = document.getElementById('cracked_eggs');
            const damagedInput = document.getElementById('damaged_eggs');
            const validationMsg = document.getElementById('egg-count-validation-msg');
            const eggInputs = document.querySelectorAll('.egg-count'); // Select all egg inputs

            function validateEggCounts() {
                const total = parseInt(totalInput.value, 10) || 0;
                const good = parseInt(goodInput.value, 10) || 0;
                const cracked = parseInt(crackedInput.value, 10) || 0;
                const damaged = parseInt(damagedInput.value, 10) || 0;

                if (total !== (good + cracked + damaged)) {
                    validationMsg.style.display = 'block'; // Show warning
                    // Optionally add 'is-invalid' class to all inputs
                    totalInput.classList.add('is-invalid');
                    goodInput.classList.add('is-invalid');
                    crackedInput.classList.add('is-invalid');
                    damagedInput.classList.add('is-invalid');
                    return false; // Indicate validation failed
                } else {
                    validationMsg.style.display = 'none'; // Hide warning
                    // Remove 'is-invalid' class if counts match
                    totalInput.classList.remove('is-invalid');
                    goodInput.classList.remove('is-invalid');
                    crackedInput.classList.remove('is-invalid');
                    damagedInput.classList.remove('is-invalid');
                    return true; // Indicate validation passed
                }
            }

            // Validate whenever any egg input changes
            eggInputs.forEach(input => {
                input.addEventListener('input', validateEggCounts);
            });

            // Initial check on page load (in case of old data or edit form)
            validateEggCounts();

            // Optionally prevent form submission if client-side validation fails
            const form = document.querySelector('#egg-production-form'); // Add id="egg-production-form" to your <form> tag
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!validateEggCounts()) {
                        event.preventDefault(); // Stop submission
                        alert('Please correct the egg counts before saving.');
                    }
                });
            }
        });
    </script>
@endpush
