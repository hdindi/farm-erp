{{-- resources/views/stages/_form.blade.php --}}

{{-- Name --}}
<div class="mb-3">
    <label for="name" class="form-label">Stage Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $stage->name ?? '') }}" required>
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $stage->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Age Range --}}
<div class="row mb-3">
    <div class="col-md-6">
        <label for="min_age_days" class="form-label">Minimum Age (Days) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('min_age_days') is-invalid @enderror" id="min_age_days" name="min_age_days"
               value="{{ old('min_age_days', $stage->min_age_days ?? '') }}" required min="0">
        @error('min_age_days')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="max_age_days" class="form-label">Maximum Age (Days) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('max_age_days') is-invalid @enderror" id="max_age_days" name="max_age_days"
               value="{{ old('max_age_days', $stage->max_age_days ?? '') }}" required min="0">
        @error('max_age_days')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Must be greater than minimum age.</small>
    </div>
</div>

{{-- Target Weight --}}
<div class="mb-3">
    <label for="target_weight_grams" class="form-label">Target Weight (Grams)</label>
    <input type="number" class="form-control @error('target_weight_grams') is-invalid @enderror" id="target_weight_grams" name="target_weight_grams"
           value="{{ old('target_weight_grams', $stage->target_weight_grams ?? '') }}" min="0">
    @error('target_weight_grams')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Optional: Target weight for birds at the end of this stage.</small>
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($stage) ? 'Update Stage' : 'Save Stage' }}
    </button>
    <a href="{{ route('stages.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
