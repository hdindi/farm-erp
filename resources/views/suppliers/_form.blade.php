{{-- resources/views/suppliers/_form.blade.php --}}

<div class="row mb-3">
    {{-- Name --}}
    <div class="col-md-6">
        <label for="name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
               value="{{ old('name', $supplier->name ?? '') }}" required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Contact Person --}}
    <div class="col-md-6">
        <label for="contact_person" class="form-label">Contact Person</label>
        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person"
               value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
        @error('contact_person')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Phone Number --}}
    <div class="col-md-6">
        <label for="phone_no" class="form-label">Phone Number</label>
        <input type="tel" class="form-control @error('phone_no') is-invalid @enderror" id="phone_no" name="phone_no"
               value="{{ old('phone_no', $supplier->phone_no ?? '') }}" placeholder="e.g., +2547...">
        @error('phone_no')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="col-md-6">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
               value="{{ old('email', $supplier->email ?? '') }}" placeholder="e.g., contact@supplier.com">
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Address --}}
<div class="mb-3">
    <label for="address" class="form-label">Address</label>
    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
    @error('address')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Description --}}
<div class="mb-3">
    <label for="description" class="form-label">Description / Notes</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $supplier->description ?? '') }}</textarea>
    @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Is Active --}}
<div class="mb-3">
    <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
        <option value="1" {{ old('is_active', $supplier->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $supplier->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('is_active')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($supplier) ? 'Update Supplier' : 'Save Supplier' }}
    </button>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
