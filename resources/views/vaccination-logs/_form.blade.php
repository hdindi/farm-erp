{{-- resources/views/vaccination-logs/_form.blade.php --}}

<div class="row mb-3">
    {{-- Daily Record (linked to Batch) --}}
    <div class="col-md-6">
        <label for="daily_record_id" class="form-label">Batch & Record Date <span class="text-danger">*</span></label>
        <select class="form-select @error('daily_record_id') is-invalid @enderror" id="daily_record_id" name="daily_record_id" required>
            <option value="">Select Batch and Date</option>
            {{-- $dailyRecords should be passed from the controller (with eager loaded batch) --}}
            @foreach($dailyRecords as $record)
                <option value="{{ $record->id }}"
                        {{ (old('daily_record_id', $vaccinationLog->daily_record_id ?? null) == $record->id) ? 'selected' : '' }}
                        {{-- Add data attribute for alive count validation (optional JS enhancement) --}}
                        data-alive-count="{{ $record->alive_count }}"
                >
                    {{ $record->batch->batch_code ?? 'Unknown Batch' }} - {{ $record->record_date->format('Y-m-d') }} (Alive: {{ $record->alive_count }})
                </option>
            @endforeach
        </select>
        @error('daily_record_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Select the daily record entry for the date the vaccination was administered.</small>
    </div>

    {{-- Vaccine --}}
    <div class="col-md-6">
        <label for="vaccine_id" class="form-label">Vaccine Used <span class="text-danger">*</span></label>
        <select class="form-select @error('vaccine_id') is-invalid @enderror" id="vaccine_id" name="vaccine_id" required>
            <option value="">Select Vaccine</option>
            {{-- $vaccines should be passed from the controller --}}
            @foreach($vaccines as $vaccine)
                <option value="{{ $vaccine->id }}"
                    {{ (old('vaccine_id', $vaccinationLog->vaccine_id ?? null) == $vaccine->id) ? 'selected' : '' }}
                >
                    {{ $vaccine->name }}
                </option>
            @endforeach
        </select>
        @error('vaccine_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Birds Vaccinated --}}
    <div class="col-md-6">
        <label for="birds_vaccinated" class="form-label">Number of Birds Vaccinated <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('birds_vaccinated') is-invalid @enderror" id="birds_vaccinated" name="birds_vaccinated"
               value="{{ old('birds_vaccinated', $vaccinationLog->birds_vaccinated ?? '') }}" required min="1">
        @error('birds_vaccinated')
        <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="form-text text-muted">Cannot exceed the number of alive birds on the selected record date.</small>
            @enderror
            {{-- You could add JS here to compare with data-alive-count from the selected daily_record_id --}}
    </div>

    {{-- Administered By --}}
    <div class="col-md-6">
        <label for="administered_by" class="form-label">Administered By</label>
        <input type="text" class="form-control @error('administered_by') is-invalid @enderror" id="administered_by" name="administered_by"
               value="{{ old('administered_by', $vaccinationLog->administered_by ?? auth()->user()->name) }}" placeholder="Optional: Name of person">
        {{-- Defaults to logged-in user's name --}}
        @error('administered_by')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Next Due Date --}}
<div class="mb-3">
    <label for="next_due_date" class="form-label">Next Due Date (Optional)</label>
    <input type="date" class="form-control @error('next_due_date') is-invalid @enderror" id="next_due_date" name="next_due_date"
           value="{{ old('next_due_date', isset($vaccinationLog) && $vaccinationLog->next_due_date ? $vaccinationLog->next_due_date->format('Y-m-d') : '') }}">
    @error('next_due_date')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Set if this vaccination requires a booster or follow-up.</small>
</div>

{{-- Notes --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Optional: e.g., Batch number of vaccine used, method of administration, observations">{{ old('notes', $vaccinationLog->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($vaccinationLog) ? 'Update Log' : 'Save Log' }}
    </button>
    <a href="{{ route('vaccination-logs.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
