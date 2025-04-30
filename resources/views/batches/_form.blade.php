{{-- resources/views/batches/_form.blade.php --}}

<div class="row mb-3">
    {{-- Batch Code --}}
    <div class="col-md-6">
        <label for="batch_code" class="form-label">Batch Code <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('batch_code') is-invalid @enderror" id="batch_code" name="batch_code"
               value="{{ old('batch_code', $batch->batch_code ?? '') }}" required>
        @error('batch_code')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Bird Type --}}
    <div class="col-md-6">
        <label for="bird_type_id" class="form-label">Bird Type <span class="text-danger">*</span></label>
        <select class="form-select @error('bird_type_id') is-invalid @enderror" id="bird_type_id" name="bird_type_id" required>
            <option value="">Select Bird Type</option>
            @foreach($birdTypes as $birdType)
                <option value="{{ $birdType->id }}"
                    {{-- CORRECTED: Check old input or existing batch data --}}
                    {{ (old('bird_type_id', $batch->bird_type_id ?? null) == $birdType->id) ? 'selected' : '' }}
                > {{-- CORRECT: '>' moved here --}}
                    {{ $birdType->name }}
                </option>
            @endforeach
        </select>
        @error('bird_type_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Breed --}}
    <div class="col-md-6">
        <label for="breed_id" class="form-label">Breed <span class="text-danger">*</span></label>
        <select class="form-select @error('breed_id') is-invalid @enderror" id="breed_id" name="breed_id" required>
            <option value="">Select Breed</option>
            @foreach($breeds as $breed)
                <option value="{{ $breed->id }}"
                    {{-- CORRECTED: Check old input or existing batch data --}}
                    {{ (old('breed_id', $batch->breed_id ?? null) == $breed->id) ? 'selected' : '' }}
                > {{-- CORRECT: '>' moved here --}}
                    {{ $breed->name }}
                </option>
            @endforeach
        </select>
        @error('breed_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Source Farm --}}
    <div class="col-md-6">
        <label for="source_farm" class="form-label">Source Farm</label>
        <input type="text" class="form-control @error('source_farm') is-invalid @enderror" id="source_farm" name="source_farm"
               value="{{ old('source_farm', $batch->source_farm ?? '') }}">
        @error('source_farm')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Initial Population --}}
    <div class="col-md-4">
        {{-- Disable initial population on edit form --}}
        <label for="initial_population" class="form-label">Initial Population <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('initial_population') is-invalid @enderror" id="initial_population" name="initial_population"
               value="{{ old('initial_population', $batch->initial_population ?? '') }}" required min="1" {{ isset($batch) ? 'readonly' : '' }}>
        @if(isset($batch))
            <small class="form-text text-muted">Cannot change initial population after creation.</small>
        @endif
        @error('initial_population')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Bird Age (Days) --}}
    <div class="col-md-4">
        <label for="bird_age_days" class="form-label">Bird Age (Days on Receipt) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('bird_age_days') is-invalid @enderror" id="bird_age_days" name="bird_age_days"
               value="{{ old('bird_age_days', $batch->bird_age_days ?? '') }}" required min="0">
        @error('bird_age_days')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Date Received --}}
    <div class="col-md-4">
        <label for="date_received" class="form-label">Date Received <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('date_received') is-invalid @enderror" id="date_received" name="date_received"
               value="{{ old('date_received', isset($batch) && $batch->date_received ? $batch->date_received->format('Y-m-d') : '') }}" required>
        @error('date_received')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Hatch Date --}}
    <div class="col-md-6">
        <label for="hatch_date" class="form-label">Hatch Date</label>
        <input type="date" class="form-control @error('hatch_date') is-invalid @enderror" id="hatch_date" name="hatch_date"
               value="{{ old('hatch_date', isset($batch) && $batch->hatch_date ? $batch->hatch_date->format('Y-m-d') : '') }}">
        @error('hatch_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Expected End Date --}}
    <div class="col-md-6">
        <label for="expected_end_date" class="form-label">Expected End Date</label>
        <input type="date" class="form-control @error('expected_end_date') is-invalid @enderror" id="expected_end_date" name="expected_end_date"
               value="{{ old('expected_end_date', isset($batch) && $batch->expected_end_date ? $batch->expected_end_date->format('Y-m-d') : '') }}">
        @error('expected_end_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Fields only visible/editable on the Edit form --}}
@if(isset($batch))
    <div class="row mb-3">
        {{-- Current Population (Readonly - updated via Daily Records) --}}
        <div class="col-md-6">
            <label for="current_population_display" class="form-label">Current Population</label>
            <input type="number" class="form-control" id="current_population_display" name="current_population_display"
                   value="{{ $batch->current_population ?? 'N/A' }}" readonly>
            {{-- Hidden input to submit value if needed, though typically not updated here --}}
            <input type="hidden" name="current_population" value="{{ $batch->current_population ?? '' }}">
            <small class="form-text text-muted">Updated automatically via Daily Records.</small>
        </div>

        {{-- Status --}}
        <div class="col-md-6">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="active" {{ (old('status', $batch->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ (old('status', $batch->status ?? '') == 'completed') ? 'selected' : '' }}>Completed</option>
                <option value="culled" {{ (old('status', $batch->status ?? '') == 'culled') ? 'selected' : '' }}>Culled</option>
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
@endif

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($batch) ? 'Update Batch' : 'Save Batch' }}
    </button>
    <a href="{{ isset($batch) ? route('batches.show', $batch->id) : route('batches.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
