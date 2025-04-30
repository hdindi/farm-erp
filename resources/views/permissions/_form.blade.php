{{-- resources/views/permissions/_form.blade.php --}}

{{-- Name (Code) --}}
<div class="mb-3">
    <label for="name" class="form-label">Permission Name (Code) <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $permission->name ?? '') }}" required placeholder="e.g., create_batch, view_report, manage_users">
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Use lowercase letters and underscores (snake_case). This is used internally.</small>
</div>

{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Optional: Explain what this permission allows">{{ old('description', $permission->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Is Active --}}
<div class="mb-3">
    <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
        <option value="1" {{ old('is_active', $permission->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $permission->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('is_active')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($permission) ? 'Update Permission' : 'Save Permission' }}
    </button>
    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
