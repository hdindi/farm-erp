{{-- resources/views/sales-teams/_form.blade.php --}}

{{-- Name --}}
<div class="mb-3">
    <label for="name" class="form-label">Member Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $salesTeam->name ?? '') }}" required>
    @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row mb-3">
    {{-- Phone Number --}}
    <div class="col-md-6">
        <label for="phone_no" class="form-label">Phone Number</label>
        <input type="tel" class="form-control @error('phone_no') is-invalid @enderror" id="phone_no" name="phone_no"
               value="{{ old('phone_no', $salesTeam->phone_no ?? '') }}" placeholder="e.g., +2547...">
        @error('phone_no')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="col-md-6">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
               value="{{ old('email', $salesTeam->email ?? '') }}" placeholder="e.g., member@example.com">
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- Is Active --}}
<div class="mb-3">
    <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
        {{-- Default to Active (1) on create form --}}
        <option value="1" {{ old('is_active', $salesTeam->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $salesTeam->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('is_active')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($salesTeam) ? 'Update Member' : 'Save Member' }}
    </button>
    <a href="{{ route('sales-teams.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
