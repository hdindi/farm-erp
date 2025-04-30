{{-- resources/views/module-permissions/_form.blade.php --}}

{{-- Module --}}
<div class="mb-3">
    <label for="module_id" class="form-label">Module <span class="text-danger">*</span></label>
    <select class="form-select @error('module_id') is-invalid @enderror" id="module_id" name="module_id" required>
        <option value="">Select Module</option>
        @foreach($modules as $id => $name)
            <option value="{{ $id }}" {{ old('module_id', $modulePermission->module_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('module_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Permission --}}
<div class="mb-3">
    <label for="permission_id" class="form-label">Permission <span class="text-danger">*</span></label>
    <select class="form-select @error('permission_id') is-invalid @enderror" id="permission_id" name="permission_id" required>
        <option value="">Select Permission</option>
        @foreach($permissions as $id => $name)
            <option value="{{ $id }}" {{ old('permission_id', $modulePermission->permission_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    @error('permission_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Is Active --}}
<div class="mb-3">
    <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
        <option value="1" {{ old('is_active', $modulePermission->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $modulePermission->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('is_active')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($modulePermission) ? 'Update Link' : 'Create Link' }}
    </button>
    <a href="{{ route('module-permissions.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
