{{-- resources/views/users/_form.blade.php --}}

<div class="row mb-3">
    {{-- Name --}}
    <div class="col-md-6">
        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
               value="{{ old('name', $user->name ?? '') }}" required autofocus>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="col-md-6">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
               value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-3">
    {{-- Phone Number --}}
    <div class="col-md-6">
        <label for="phone_number" class="form-label">Phone Number</label>
        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number"
               value="{{ old('phone_number', $user->phone_number ?? '') }}" placeholder="Optional">
        @error('phone_number')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-6">
        <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
            <option value="1" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $user->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('is_active')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr>
<h5 class="mb-3">Password</h5>

<div class="row mb-3">
    {{-- Password --}}
    <div class="col-md-6">
        <label for="password" class="form-label">Password @if(!isset($user))<span class="text-danger">*</span>@endif</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
               {{ isset($user) ? '' : 'required' }} autocomplete="new-password">
        @if(isset($user))
            <small class="form-text text-muted">Leave blank to keep the current password.</small>
        @endif
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirm Password @if(!isset($user))<span class="text-danger">*</span>@endif</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
               {{ isset($user) ? '' : 'required' }} autocomplete="new-password">
        {{-- No error display needed here, 'confirmed' rule handles it --}}
    </div>
</div>

<hr>
<h5 class="mb-3">Assign Roles</h5>

<div class="mb-3">
    <label class="form-label">Roles</label>
    @error('roles') <span class="text-danger small">{{ $message }}</span> @enderror
    <div class="row">
        {{-- $roles passed from controller --}}
        @forelse($roles as $role)
            <div class="col-md-4 col-sm-6">
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           value="{{ $role->id }}"
                           id="role_{{ $role->id }}"
                           name="roles[]"
                        {{-- Check if old input exists, OR if editing, check if role is assigned --}}
                        {{ (is_array(old('roles')) && in_array($role->id, old('roles'))) || (isset($user) && $user->roles->contains($role->id)) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="role_{{ $role->id }}">
                        {{ $role->name }}
                    </label>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">No active roles found. Please <a href="{{ route('roles.create') }}">create a role</a> first.</p>
            </div>
        @endforelse
    </div>
    @error('roles.*') {{-- Error for individual role IDs if needed --}}
    <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>


{{-- Buttons --}}
<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
    </button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
    </a>
</div>
