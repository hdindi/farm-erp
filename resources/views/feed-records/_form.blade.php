@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

{{-- Batch and Feed Selection --}}
<div class="row mb-3">
    <div class="col-md-6">
        <label for="daily_record_id" class="form-label">Batch & Record Date <span class="text-danger">*</span></label>
        <select class="form-select @error('daily_record_id') is-invalid @enderror" id="daily_record_id" name="daily_record_id" required>
            <option value="">Select Batch and Date...</option>
            @foreach($dailyRecords as $record)
                <option value="{{ $record->id }}"
                    {{ (old('daily_record_id', $feedRecord->daily_record_id ?? null) == $record->id) ? 'selected' : '' }}>
                    {{ $record->batch->batch_code ?? 'N/A' }} - {{ $record->record_date->format('Y-m-d') }}
                </option>
            @endforeach
        </select>
        @error('daily_record_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="feed_type_id" class="form-label">Feed Type <span class="text-danger">*</span></label>
        <select class="form-select @error('feed_type_id') is-invalid @enderror" id="feed_type_id" name="feed_type_id" required>
            <option value="">Select Feed Type...</option>
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

{{-- ✅ NEW: Insightful Information Panel --}}
<div id="info-panel" class="row g-3 mb-3 p-3 bg-light border rounded" style="display: none;">
    <div class="col-md-3">
        <label class="form-label small text-muted">Bird Count</label>
        <p class="fw-bold mb-0" id="info-bird-count">-</p>
    </div>
    <div class="col-md-3">
        <label class="form-label small text-muted">Bird Age</label>
        <p class="fw-bold mb-0" id="info-bird-age">-</p>
    </div>
    <div class="col-md-3">
        <label class="form-label small text-muted">Recommended Feed</label>
        <p class="fw-bold mb-0" id="info-recommended-feed">-</p>
    </div>
    <div class="col-md-3">
        <label class="form-label small text-muted">Feed Given Today</label>
        <p class="fw-bold mb-0" id="info-feed-given">-</p>
    </div>
</div>
<div id="loader" class="loader mt-3" style="display: none;"></div>

{{-- Quantity and Cost --}}
<div class="row mb-3">
    <div class="col-md-4">
        <label for="quantity_kg" class="form-label">Quantity (kg) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01" class="form-control @error('quantity_kg') is-invalid @enderror" id="quantity_kg" name="quantity_kg"
               value="{{ old('quantity_kg', $feedRecord->quantity_kg ?? '') }}" required placeholder="e.g., 120.5">
        @error('quantity_kg')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="cost_per_kg" class="form-label">Cost per Kg</label>
        <div class="input-group">
            <span class="input-group-text">{{ $currencySymbol }}</span>
            <input type="number" step="0.01" min="0" class="form-control @error('cost_per_kg') is-invalid @enderror" id="cost_per_kg" name="cost_per_kg"
                   value="{{ old('cost_per_kg', $feedRecord->cost_per_kg ?? '') }}" placeholder="Optional cost">
        </div>
        @error('cost_per_kg')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="feeding_time" class="form-label">Feeding Time</label>
        <input type="time" class="form-control @error('feeding_time') is-invalid @enderror" id="feeding_time" name="feeding_time"
               value="{{ old('feeding_time', $feedRecord->feeding_time ?? '') }}">
        @error('feeding_time')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Notes & Buttons --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Optional notes...">{{ old('notes', $feedRecord->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($feedRecord) ? 'Update Feed Record' : 'Save Feed Record' }}
    </button>
    <a href="{{ route('feed-records.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>


@push('styles')
    <style>
        .loader { border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 1rem; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dailyRecordSelect = document.getElementById('daily_record_id');
            const infoPanel = document.getElementById('info-panel');
            const loader = document.getElementById('loader');

            const infoBirdCount = document.getElementById('info-bird-count');
            const infoBirdAge = document.getElementById('info-bird-age');
            const infoRecommendedFeed = document.getElementById('info-recommended-feed');
            const infoFeedGiven = document.getElementById('info-feed-given');

            function updateInfoPanel(recordId) {
                if (!recordId) {
                    infoPanel.style.display = 'none';
                    return;
                }

                loader.style.display = 'block';
                infoPanel.style.display = 'none';

                fetch(`/api/daily-records/${recordId}/feed-data`)
                    .then(response => {
                        if (!response.ok) throw new Error('Data not found');
                        return response.json();
                    })
                    .then(data => {
                        const recommendedTotal = (data.bird_count * data.recommended_feed_grams) / 1000;
                        infoBirdCount.textContent = `${data.bird_count} birds`;
                        infoBirdAge.textContent = `${data.age_in_days} days old (${data.stage_name})`;
                        infoRecommendedFeed.textContent = `${recommendedTotal.toFixed(2)} kg`;
                        infoFeedGiven.textContent = `${data.feed_given_today_kg} kg`;
                        infoPanel.style.display = 'flex';
                    })
                    .catch(error => {
                        console.error('Error fetching feed data:', error);
                        infoPanel.style.display = 'none';
                    })
                    .finally(() => {
                        loader.style.display = 'none';
                    });
            }

            dailyRecordSelect.addEventListener('change', () => {
                updateInfoPanel(dailyRecordSelect.value);
            });

            // If a record is already selected on page load (e.g., edit form)
            if (dailyRecordSelect.value) {
                updateInfoPanel(dailyRecordSelect.value);
            }
        });
    </script>
@endpush
