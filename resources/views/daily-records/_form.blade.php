{{-- resources/views/daily-records/_form.blade.php --}}

{{-- SECTION 1: Batch Selection (Always Visible) --}}
<div class="card mb-4">
    <div class="card-header">
        <strong>1. Select a Batch to Begin</strong>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <label for="batch_id" class="form-label">Batch <span class="text-danger">*</span></label>
                <select class="form-select @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id" required>
                    <option value="">Choose a batch to load details...</option>
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

                {{-- Read-Only Info Panel --}}
                <div id="batch-info-panel" class="row g-3 mt-3 p-3 bg-light border rounded" style="display: none;">
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small text-muted">Initial Count</label>
                        <p class="fw-bold mb-0" id="info-initial-population">-</p>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small text-muted">Current Count</label>
                        <p class="fw-bold mb-0" id="info-current-population">-</p>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small text-muted">Days Since Birth</label>
                        <p class="fw-bold mb-0" id="info-days-since-birth">-</p>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="form-label small text-muted">Current Week</label>
                        <p class="fw-bold mb-0" id="info-bird-week">-</p>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <label class="form-label small text-muted">Last Record Date</label>
                        <p class="fw-bold mb-0" id="info-last-record">-</p>
                    </div>
                </div>

                <div id="batch-alert" class="alert-message mt-2" style="display: none;"></div>
                <div id="loader" class="loader mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Container for all other form parts --}}
<div id="record-details-container" style="display: none;">

    {{-- SECTION 2: Recording Details --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>2. Recording Details</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="record_date" class="form-label">Record Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('record_date') is-invalid @enderror" id="record_date" name="record_date"
                           value="{{ old('record_date', isset($dailyRecord) && $dailyRecord->record_date ? $dailyRecord->record_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    @error('record_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="stage_id" class="form-label">Current Stage <span class="text-danger">*</span></label>
                    <select class="form-select bg-light @error('stage_id') is-invalid @enderror" id="stage_id" name="stage_id" required readonly>
                        <option value="">- Auto-selected -</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}" {{ (old('stage_id', $dailyRecord->stage_id ?? null) == $stage->id) ? 'selected' : '' }}>
                                {{ $stage->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('stage_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" id="days_in_stage" name="days_in_stage">
            </div>
        </div>
    </div>

    {{-- SECTION 3: Daily Vitals --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>3. Daily Vitals</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="alive_count" class="form-label">Number Alive <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('alive_count') is-invalid @enderror" id="alive_count" name="alive_count"
                           value="{{ old('alive_count', $dailyRecord->alive_count ?? '') }}" required min="0" placeholder="End-of-day count">
                    @error('alive_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="dead_count" class="form-label">Mortality <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('dead_count') is-invalid @enderror" id="dead_count" name="dead_count"
                           value="{{ old('dead_count', $dailyRecord->dead_count ?? '0') }}" required min="0">
                    @error('dead_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="culls_count" class="form-label">Culls <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('culls_count') is-invalid @enderror" id="culls_count" name="culls_count"
                           value="{{ old('culls_count', $dailyRecord->culls_count ?? '0') }}" required min="0">
                    @error('culls_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div id="vitals-alert" class="alert-message mt-3" style="display: none;"></div>
        </div>
    </div>

    {{-- SECTION 4: Observations --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>4. Observations</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="average_weight_grams" class="form-label">Average Weight (Grams)</label>
                    <div class="input-group">
                        <input type="number" step="0.1" min="0" class="form-control @error('average_weight_grams') is-invalid @enderror" id="average_weight_grams" name="average_weight_grams"
                               value="{{ old('average_weight_grams', $dailyRecord->average_weight_grams ?? '') }}" placeholder="Optional">
                        <span class="input-group-text">g</span>
                    </div>
                    @error('average_weight_grams')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mt-3">
                <label for="notes" class="form-label">Notes / Remarks</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Record any significant observations for the day...">{{ old('notes', $dailyRecord->notes ?? '') }}</textarea>
                @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div id="form-buttons" class="mt-4">
        <button type="submit" id="submit-button" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ isset($dailyRecord) ? 'Update Daily Record' : 'Save Daily Record' }}
        </button>
        <a href="{{ route('daily-records.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>
</div>

@push('styles')
    <style>
        .alert-message { padding: 0.5rem 1rem; border-radius: .25rem; font-size: 0.875em; color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; }
        .loader { border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 1rem auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Get all form and info elements ---
            const batchSelect = document.getElementById('batch_id');
            const loader = document.getElementById('loader');
            const recordDetailsContainer = document.getElementById('record-details-container');
            const batchAlert = document.getElementById('batch-alert');
            const stageSelect = document.getElementById('stage_id');
            const daysInStageInput = document.getElementById('days_in_stage');

            const batchInfoPanel = document.getElementById('batch-info-panel');
            const infoInitialPopulation = document.getElementById('info-initial-population');
            const infoCurrentPopulation = document.getElementById('info-current-population');
            const infoLastRecord = document.getElementById('info-last-record');
            const infoBirdWeek = document.getElementById('info-bird-week');
            const infoDaysSinceBirth = document.getElementById('info-days-since-birth');

            const submitButton = document.getElementById('submit-button');
            const aliveCountInput = document.getElementById('alive_count');
            const deadCountInput = document.getElementById('dead_count');
            const cullsCountInput = document.getElementById('culls_count');
            const vitalsAlert = document.getElementById('vitals-alert');

            let currentPopulation = 0;
            const stages = @json($stagesData);

            function showAlert(element, message) {
                element.style.display = message ? 'block' : 'none';
                element.textContent = message || '';
            }

            function validateVitals() {
                const alive = parseInt(aliveCountInput.value) || 0;
                const dead = parseInt(deadCountInput.value) || 0;
                const culls = parseInt(cullsCountInput.value) || 0;
                const total = alive + dead + culls;

                if (total !== currentPopulation && aliveCountInput.value !== '') {
                    showAlert(vitalsAlert, `The sum of alive, mortality, and culls (${total}) must equal the Current Count (${currentPopulation}).`);
                    submitButton.disabled = true;
                } else {
                    showAlert(vitalsAlert, null);
                    submitButton.disabled = false;
                }
            }

            function updateFormForBatch(batchId) {
                showAlert(batchAlert, null);
                showAlert(vitalsAlert, null);
                recordDetailsContainer.style.display = 'none';
                batchInfoPanel.style.display = 'none';
                submitButton.disabled = true;

                if (!batchId) { return; }

                loader.style.display = 'block';

                fetch(`/api/batches/${batchId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        currentPopulation = parseInt(data.current_population) || 0;
                        infoInitialPopulation.textContent = data.initial_population;
                        infoCurrentPopulation.textContent = currentPopulation;
                        infoLastRecord.textContent = data.last_record_date;
                        infoBirdWeek.textContent = `Week ${data.bird_week}`;
                        infoDaysSinceBirth.textContent = `${Math.round(data.age_in_days)} days`;
                        batchInfoPanel.style.display = 'flex';

                        let currentStage = null;
                        for (const stageId in stages) {
                            if (data.age_in_days >= stages[stageId].min_age_days && data.age_in_days <= stages[stageId].max_age_days) {
                                stageSelect.value = stageId;
                                currentStage = stages[stageId];
                                break;
                            }
                        }

                        if (currentStage) {
                            daysInStageInput.value = Math.round(data.age_in_days - currentStage.min_age_days + 1);
                            recordDetailsContainer.style.display = 'block';
                            validateVitals();
                        } else {
                            showAlert(batchAlert, 'No matching stage found for this batch.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching batch details:', error);
                        showAlert(batchAlert, 'Could not fetch batch details.');
                    })
                    .finally(() => {
                        loader.style.display = 'none';
                    });
            }

            batchSelect.addEventListener('change', () => updateFormForBatch(batchSelect.value));
            [aliveCountInput, deadCountInput, cullsCountInput].forEach(input => {
                input.addEventListener('input', validateVitals);
            });

            if (batchSelect.value) {
                updateFormForBatch(batchSelect.value);
            }
        });
    </script>
@endpush
