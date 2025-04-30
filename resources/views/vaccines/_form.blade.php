{{-- resources/views/vaccines/_form.blade.php --}}

{{-- Name --}}
<div class="mb-3">
    <label for="name" class="form-label">Vaccine Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $vaccine->name ?? '') }}" required placeholder="e.g., Gumboro Vaccine Strain X, Newcastle LaSota">
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Manufacturer --}}
<div class="mb-3">
    <label for="manufacturer" class="form-label">Manufacturer</label>
    <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" id="manufacturer" name="manufacturer"
           value="{{ old('manufacturer', $vaccine->manufacturer ?? '') }}" placeholder="Optional: Manufacturer name">
    @error('manufacturer')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description / Notes</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional: Target diseases, administration route (e.g., drinking water, eye drop), etc.">{{ old('description', $vaccine->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Minimum Age & Booster Interval --}}
<div class="row mb-3">
    <div class="col-md-6">
        <label for="minimum_age_days" class="form-label">Minimum Age for First Dose (Days)</label>
        <input type="number" class="form-control @error('minimum_age_days') is-invalid @enderror" id="minimum_age_days" name="minimum_age_days"
               value="{{ old('minimum_age_days', $vaccine->minimum_age_days ?? '') }}" min="0" placeholder="Optional: e.g., 7">
        @error('minimum_age_days')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="booster_interval_days" class="form-label">Booster Interval (Days)</label>
        <input type="number" class="form-control @error('booster_interval_days') is-invalid @enderror" id="booster_interval_days" name="booster_interval_days"
               value="{{ old('booster_interval_days', $vaccine->booster_interval_days ?? '') }}" min="0" placeholder="Optional: e.g., 14">
        @error('booster_interval_days')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Days after the previous dose for a booster, if applicable.</small>
    </div>
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($vaccine) ? 'Update Vaccine' : 'Save Vaccine' }}
    </button>
    <a href="{{ route('vaccines.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
