{{-- resources/views/disease-management/_form.blade.php --}}

<div class="row mb-3">
    {{-- Batch --}}
    <div class="col-md-4">
        <label for="batch_id" class="form-label">Affected Batch <span class="text-danger">*</span></label>
        <select class="form-select @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id" required>
            <option value="">Select Batch</option>
            {{-- $batches passed from controller --}}
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}"
                    {{ (old('batch_id', $diseaseManagement->batch_id ?? null) == $batch->id) ? 'selected' : '' }}
                    {{ $batch->status != 'active' ? 'disabled' : '' }}>
                    {{ $batch->batch_code }} {{ $batch->status != 'active' ? '('.ucfirst($batch->status).')' : '' }}
                </option>
            @endforeach
        </select>
        @error('batch_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Disease --}}
    <div class="col-md-4">
        <label for="disease_id" class="form-label">Observed Disease <span class="text-danger">*</span></label>
        <select class="form-select @error('disease_id') is-invalid @enderror" id="disease_id" name="disease_id" required>
            <option value="">Select Disease</option>
            {{-- $diseases passed from controller --}}
            @foreach($diseases as $disease)
                <option value="{{ $disease->id }}"
                    {{ (old('disease_id', $diseaseManagement->disease_id ?? null) == $disease->id) ? 'selected' : '' }}>
                    {{ $disease->name }}
                </option>
            @endforeach
        </select>
        @error('disease_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Drug Used (Optional) --}}
    <div class="col-md-4">
        <label for="drug_id" class="form-label">Drug Used (Optional)</label>
        <select class="form-select @error('drug_id') is-invalid @enderror" id="drug_id" name="drug_id">
            <option value="">Select Drug (if applicable)</option>
            {{-- $drugs passed from controller --}}
            @foreach($drugs as $drug)
                <option value="{{ $drug->id }}"
                    {{ (old('drug_id', $diseaseManagement->drug_id ?? null) == $drug->id) ? 'selected' : '' }}>
                    {{ $drug->name }}
                </option>
            @endforeach
        </select>
        @error('drug_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Observation Date --}}
    <div class="col-md-4">
        <label for="observation_date" class="form-label">Observation Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('observation_date') is-invalid @enderror" id="observation_date" name="observation_date"
               value="{{ old('observation_date', isset($diseaseManagement) && $diseaseManagement->observation_date ? $diseaseManagement->observation_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('observation_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Affected Count --}}
    <div class="col-md-4">
        <label for="affected_count" class="form-label">Number Affected (Optional)</label>
        <input type="number" class="form-control @error('affected_count') is-invalid @enderror" id="affected_count" name="affected_count"
               value="{{ old('affected_count', $diseaseManagement->affected_count ?? '') }}" min="0" placeholder="Optional">
        @error('affected_count')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Treatment Start Date --}}
    <div class="col-md-6">
        <label for="treatment_start_date" class="form-label">Treatment Start Date (Optional)</label>
        <input type="date" class="form-control @error('treatment_start_date') is-invalid @enderror" id="treatment_start_date" name="treatment_start_date"
               value="{{ old('treatment_start_date', isset($diseaseManagement) && $diseaseManagement->treatment_start_date ? $diseaseManagement->treatment_start_date->format('Y-m-d') : '') }}">
        @error('treatment_start_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Treatment End Date --}}
    <div class="col-md-6">
        <label for="treatment_end_date" class="form-label">Treatment End Date (Optional)</label>
        <input type="date" class="form-control @error('treatment_end_date') is-invalid @enderror" id="treatment_end_date" name="treatment_end_date"
               value="{{ old('treatment_end_date', isset($diseaseManagement) && $diseaseManagement->treatment_end_date ? $diseaseManagement->treatment_end_date->format('Y-m-d') : '') }}">
        @error('treatment_end_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


{{-- Notes --}}
<div class="mb-3">
    <label for="notes" class="form-label">Notes / Observations</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Describe observed symptoms, actions taken, treatment details, recovery progress, etc.">{{ old('notes', $diseaseManagement->notes ?? '') }}</textarea>
    @error('notes')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($diseaseManagement) ? 'Update Record' : 'Save Record' }}
    </button>
    <a href="{{ route('disease-management.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
