{{-- resources/views/daily-records/_form.blade.php --}}

<div class="row mb-3">
    {{-- Batch --}}
    <div class="col-md-6">
        <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
        <select class="form-select @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id" required>
            <option value="">Select Batch</option>
            {{-- $batches passed from controller --}}
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}"
                    {{ (old('batch_id', $dailyRecord->batch_id ?? request('batch_id')) == $batch->id) ? 'selected' : '' }}
                    {{ $batch->status != 'active' ? 'disabled' : '' }}>
                    {{ $batch->batch_code }} {{ $batch->status != 'active' ? '('.ucfirst($batch->status).')' : '' }}
                </option>
            @endforeach
        </select>
        @error('batch_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Record Date --}}
    <div class="col-md-6">
        <label for="record_date" class="form-label">Record Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('record_date') is-invalid @enderror" id="record_date" name="record_date"
               value="{{ old('record_date', isset($dailyRecord) && $dailyRecord->record_date ? $dailyRecord->record_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('record_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Stage --}}
    <div class="col-md-6">
        <label for="stage_id" class="form-label">Current Stage <span class="text-danger">*</span></label>
        <select class="form-select @error('stage_id') is-invalid @enderror" id="stage_id" name="stage_id" required>
            <option value="">Select Stage</option>
            {{-- $stages passed from controller --}}
            @foreach($stages as $stage)
                <option value="{{ $stage->id }}"
                    {{ (old('stage_id', $dailyRecord->stage_id ?? null) == $stage->id) ? 'selected' : '' }}>
                    {{ $stage->name }} ({{ $stage->min_age_days }} - {{ $stage->max_age_days }} days)
                </option>
            @endforeach
        </select>
        @error('stage_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Day In Stage --}}
    <div class="col-md-6">
        <label for="day_in_stage" class="form-label">Day # in Stage <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('day_in_stage') is-invalid @enderror" id="day_in_stage" name="day_in_stage"
               value="{{ old('day_in_stage', $dailyRecord->day_in_stage ?? '') }}" required min="1" placeholder="e.g., 5">
        @error('day_in_stage')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">The number of days the birds have been in *this specific stage* as of the record date.</small>
    </div>
</div>


<div class="row mb-3">
    {{-- Alive Count --}}
    <div class="col-md-4">
        <label for="alive_count" class="form-label">Number Alive <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('alive_count') is-invalid @enderror" id="alive_count" name="alive_count"
               value="{{ old('alive_count', $dailyRecord->alive_count ?? '') }}" required min="0" placeholder="Current count">
        @error('alive_count')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">End-of-day count after deaths/culls.</small>
    </div>

    {{-- Dead Count --}}
    <div class="col-md-4">
        <label for="dead_count" class="form-label">Number Dead (Mortality) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('dead_count') is-invalid @enderror" id="dead_count" name="dead_count"
               value="{{ old('dead_count', $dailyRecord->dead_count ?? '0') }}" required min="0">
        @error('dead_count')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Culls Count --}}
    <div class="col-md-4">
        <label for="culls_count" class="form-label">Number Culled <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('culls_count') is-invalid @enderror" id="culls_count" name="culls_count"
               value="{{ old('culls_count', $dailyRecord->culls_count ?? '0') }}" required min="0">
        @error('culls_count')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Average Weight --}}
    <div class="col-md-6">
        <label for="average_weight_grams" class="form-label">Average Weight (grams)</label>
        <div class="input-group">
            <input type="number" step="0.1" min="0" class="form-control @error('average_weight_grams') is-invalid @enderror" id="average_weight_grams" name="average_weight_grams"
                   value="{{ old('average_weight_grams', $dailyRecord->average_weight_grams ?? '') }}" placeholder="Optional">
            <span class="input-group-text">grams</span>
        </div>
        @error('average_weight_grams')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>


{{-- Notes --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes / Observations</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Record any significant observations for the day (e.g., feed intake, water consumption, bird behaviour, environmental conditions).">{{ old('notes', $dailyRecord->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($dailyRecord) ? 'Update Daily Record' : 'Save Daily Record' }}
    </button>
    <a href="{{ route('daily-records.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
