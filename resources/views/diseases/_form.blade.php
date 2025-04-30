{{-- resources/views/diseases/_form.blade.php --}}

{{-- Name --}}
<div class="mb-3">
    <label for="name" class="form-label">Disease Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $disease->name ?? '') }}" required placeholder="e.g., Newcastle Disease, Gumboro, Fowl Pox">
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description / Symptoms / Notes</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Optional: Describe symptoms, common treatments, prevention methods etc.">{{ old('description', $disease->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($disease) ? 'Update Disease' : 'Save Disease' }}
    </button>
    <a href="{{ route('diseases.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
