{{-- resources/views/bird-types/_form.blade.php --}}

{{-- Name --}}
<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $birdType->name ?? '') }}" required>
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $birdType->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Egg Production Cycle --}}
<div class="mb-3">
    <label for="egg_production_cycle" class="form-label">Avg. Egg Production Cycle (Days)</label>
    <input type="number" class="form-control @error('egg_production_cycle') is-invalid @enderror" id="egg_production_cycle" name="egg_production_cycle"
           value="{{ old('egg_production_cycle', $birdType->egg_production_cycle ?? '') }}" min="0">
    @error('egg_production_cycle')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Optional: Average number of days in the egg production cycle for this type (e.g., for Layers).</small>
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($birdType) ? 'Update Bird Type' : 'Save Bird Type' }}
    </button>
    <a href="{{ route('bird-types.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
