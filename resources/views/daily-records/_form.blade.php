{{-- resources/views/daily-records/_form.blade.php --}}

{{--
    SECTION 1: Batch Selection
    - This part is always visible for the user to initiate the process.
--}}
<div class="card mb-4">
    <div class="card-header">
        <strong>1. Select a Batch to Begin</strong>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Batch Selection --}}
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
                {{-- Inline alert and Loader --}}
                <div id="batch-alert" class="alert-message mt-2" style="display: none;"></div>
                <div id="loader" class="loader mt-3" style="display: none;"></div>


                {{-- Just after the closing </select> of the Batch dropdown and its error message div --}}

                {{-- ✅ ADD THIS NEW READ-ONLY INFO PANEL --}}
                <div id="batch-info-panel" class="row g-2 mt-3 p-3 bg-light border rounded" style="display: none;">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Initial Bird Count</label>
                        <p class="fw-bold" id="info-initial-population">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Date Received</label>
                        <p class="fw-bold" id="info-date-received">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Current Bird Week</label>
                        <p class="fw-bold" id="info-bird-week">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Expected Culling Date</label>
                        <p class="fw-bold" id="info-culling-date">-</p>
                    </div>
                </div>

                <div id="batch-alert" class="alert-message mt-2" style="display: none;"></div>
                <div id="loader" class="loader mt-3" style="display: none;"></div>

                {{-- ... the rest of your form follows ... --}}



            </div>
        </div>
    </div>
</div>

{{--
    ✅ Container for all other form parts
    - This entire div is hidden until a batch is selected and its data is loaded.
--}}
<div id="record-details-container" class="hidden">





    {{-- SECTION 2: Auto-Populated Details --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>2. Batch Details</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Record Date --}}
                <div class="col-md-6">
                    <label for="record_date" class="form-label">Record Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('record_date') is-invalid @enderror" id="record_date" name="record_date"
                           value="{{ old('record_date', isset($dailyRecord) && $dailyRecord->record_date ? $dailyRecord->record_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    @error('record_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Batch Received Date (Auto-populated) --}}
                <div class="col-md-6">
                    <label for="batch_received_date" class="form-label">Date Received</label>
                    <input type="text" id="batch_received_date" class="form-control bg-light" readonly>
                </div>
                {{-- Current Stage (Auto-populated) --}}
                <div class="col-md-6">
                    <label for="stage_id" class="form-label">Current Stage <span class="text-danger">*</span></label>
                    <select class="form-select bg-light @error('stage_id') is-invalid @enderror" id="stage_id" name="stage_id" required>
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
                {{-- No of Days in Stage (Auto-populated) --}}
                <div class="col-md-6">
                    <label for="days_in_stage" class="form-label">Day # in Stage</label>
                    <input type="number" id="days_in_stage" name="days_in_stage" class="form-control bg-light" readonly>
                </div>
            </div>
        </div>
    </div>

    {{--
        ✅ SECTION 3: Daily Vitals (Restored)
    --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>3. Daily Vitals</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Alive Count --}}
                <div class="col-md-4">
                    <label for="alive_count" class="form-label">Number Alive <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('alive_count') is-invalid @enderror" id="alive_count" name="alive_count"
                           value="{{ old('alive_count', $dailyRecord->alive_count ?? '') }}" required min="0" placeholder="End-of-day count">
                    @error('alive_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Dead Count --}}
                <div class="col-md-4">
                    <label for="dead_count" class="form-label">Mortality <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('dead_count') is-invalid @enderror" id="dead_count" name="dead_count"
                           value="{{ old('dead_count', $dailyRecord->dead_count ?? '0') }}" required min="0">
                    @error('dead_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Culls Count --}}
                <div class="col-md-4">
                    <label for="culls_count" class="form-label">Culls <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('culls_count') is-invalid @enderror" id="culls_count" name="culls_count"
                           value="{{ old('culls_count', $dailyRecord->culls_count ?? '0') }}" required min="0">
                    @error('culls_count')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{--
        ✅ SECTION 4: Observations (Restored)
    --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>4. Observations</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Average Weight --}}
                <div class="col-md-6">
                    <label for="average_weight_grams" class="form-label">Average Weight</label>
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
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ isset($dailyRecord) ? 'Update Daily Record' : 'Save Daily Record' }}
        </button>
        <a href="{{ route('daily-records.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
    </div>

</div> {{-- End of #record-details-container --}}


{{-- Add CSS for loader and transitions --}}
@push('styles')
    <style>
        .alert-message {
            padding: 0.5rem 1rem;
            border-radius: .25rem;
            font-size: 0.875em;
            color: #842029;
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 1rem auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .hidden {
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
            pointer-events: none;
            height: 0;
            overflow: hidden;
        }
        .visible {
            opacity: 1;
            height: auto;
        }
    </style>
@endpush



@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Get all form elements ---
            const batchSelect = document.getElementById('batch_id');
            const loader = document.getElementById('loader');
            const recordDetailsContainer = document.getElementById('record-details-container');
            const batchAlert = document.getElementById('batch-alert');

            // Form inputs
            const stageSelect = document.getElementById('stage_id');
            const receivedDateInput = document.getElementById('batch_received_date');
            const daysInStageInput = document.getElementById('days_in_stage');

            // ✅ NEW: Get the info panel and its elements
            const batchInfoPanel = document.getElementById('batch-info-panel');
            const infoInitialPopulation = document.getElementById('info-initial-population');
            const infoDateReceived = document.getElementById('info-date-received');
            const infoBirdWeek = document.getElementById('info-bird-week');
            const infoCullingDate = document.getElementById('info-culling-date');

            const stages = @json($stagesData);

            function showAlert(message) { /* ... (no changes here) ... */ }

            function updateFormForBatch(batchId) {
                showAlert(null);
                recordDetailsContainer.classList.add('hidden');
                batchInfoPanel.style.display = 'none'; // Hide info panel on new selection

                if (!batchId) { return; }

                loader.style.display = 'block';

                fetch(`/api/batches/${batchId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // ✅ POPULATE THE NEW INFO PANEL
                        infoInitialPopulation.textContent = data.initial_population;
                        infoDateReceived.textContent = data.date_received;
                        infoBirdWeek.textContent = `Week ${data.bird_week}`;
                        infoCullingDate.textContent = data.expected_culling_date;
                        batchInfoPanel.style.display = 'flex'; // Show the panel

                        // --- Populate main form fields (no changes here) ---
                        const ageInDays = data.age_in_days;
                        receivedDateInput.value = data.date_received;
                        let currentStage = null;
                        let selectedStageId = '';

                        for (const stageId in stages) {
                            const stage = stages[stageId];
                            if (ageInDays >= stage.min_age_days && ageInDays <= stage.max_age_days) {
                                selectedStageId = stageId;
                                currentStage = stage;
                                break;
                            }
                        }

                        if (currentStage) {
                            stageSelect.value = selectedStageId;
                            daysInStageInput.value = Math.round(ageInDays - currentStage.min_age_days + 1);
                            recordDetailsContainer.classList.remove('hidden');
                        } else {
                            showAlert('No matching stage found for the current age of this batch.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching batch details:', error);
                        showAlert('Could not fetch batch details. Please try again.');
                    })
                    .finally(() => {
                        loader.style.display = 'none';
                    });
            }

            // --- Event Listener and Initial Check (no changes here) ---
            batchSelect.addEventListener('change', function () {
                updateFormForBatch(this.value);
            });

            if (batchSelect.value) {
                updateFormForBatch(batchSelect.value);
            }
        });
    </script>
@endpush
