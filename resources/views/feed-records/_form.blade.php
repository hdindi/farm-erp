{{-- resources/views/feed-records/_form.blade.php --}}
@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

<div class="row mb-3">
    {{-- Daily Record (linked to Batch) --}}
    <div class="col-md-6">
        <label for="daily_record_id" class="form-label">Batch & Record Date <span class="text-danger">*</span></label>
        <select class="form-select @error('daily_record_id') is-invalid @enderror" id="daily_record_id" name="daily_record_id" required>
            <option value="">Select Batch and Date</option>
            {{-- $dailyRecords should be passed from the controller (with eager loaded batch) --}}
            @foreach($dailyRecords as $record)
                <option value="{{ $record->id }}"
                    {{ (old('daily_record_id', $feedRecord->daily_record_id ?? null) == $record->id) ? 'selected' : '' }}>
                    {{ $record->batch->batch_code ?? 'Unknown Batch' }} - {{ $record->record_date->format('Y-m-d') }}
                </option>
            @endforeach
        </select>
        @error('daily_record_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Select the daily record entry this feeding applies to.</small>
    </div>

    {{-- Feed Type --}}
    <div class="col-md-6">
        <label for="feed_type_id" class="form-label">Feed Type <span class="text-danger">*</span></label>
        <select class="form-select @error('feed_type_id') is-invalid @enderror" id="feed_type_id" name="feed_type_id" required>
            <option value="">Select Feed Type</option>
            {{-- $feedTypes should be passed from the controller --}}
            @foreach($feedTypes as $feedType)
                <option value="{{ $feedType->id }}"
                    {{ (old('feed_type_id', $feedRecord->feed_type_id ?? null) == $feedType->id) ? 'selected' : '' }}>
                    {{ $feedType->name }}
                </option>
            @endforeach
        </select>
        @error('feed_type_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Quantity (kg) --}}
    <div class="col-md-4">
        <label for="quantity_kg" class="form-label">Quantity (kg) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01" class="form-control @error('quantity_kg') is-invalid @enderror" id="quantity_kg" name="quantity_kg"
               value="{{ old('quantity_kg', $feedRecord->quantity_kg ?? '') }}" required placeholder="e.g., 120.5">
        @error('quantity_kg')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Cost per Kg --}}
    <div class="col-md-4">
        <label for="cost_per_kg" class="form-label">Cost per Kg (Optional)</label>
        <div class="input-group">
            <span class="input-group-text">{{ $currencySymbol }}</span>
            <input type="number" step="0.01" min="0" class="form-control @error('cost_per_kg') is-invalid @enderror" id="cost_per_kg" name="cost_per_kg"
                   value="{{ old('cost_per_kg', $feedRecord->cost_per_kg ?? '') }}" placeholder="Optional cost">
        </div>
        @error('cost_per_kg')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Feeding Time --}}
    <div class="col-md-4">
        <label for="feeding_time" class="form-label">Feeding Time (Optional)</label>
        <input type="time" class="form-control @error('feeding_time') is-invalid @enderror" id="feeding_time" name="feeding_time"
               value="{{ old('feeding_time', $feedRecord->feeding_time ?? '') }}">
        @error('feeding_time')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Notes --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Optional: Observations during feeding, feed batch number, etc.">{{ old('notes', $feedRecord->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($feedRecord) ? 'Update Feed Record' : 'Save Feed Record' }}
    </button>
    <a href="{{ route('feed-records.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
