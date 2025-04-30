@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Role: {{ $role->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info me-1" title="View Role">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to Roles List
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                Update Role Details
            </div>
            <div class="card-body">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('roles._form', ['role' => $role])
                </form>
            </div>
        </div>

        {{-- Permissions Mapping Section --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-key"></i> Assign Permissions to Role: {{ $role->name }}
            </div>
            <div class="card-body">
                {{-- Ensure route name matches --}}
                <form action="{{ route('roles.updatePermissions', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if($modules->isEmpty())
                        <p class="text-muted">No modules or permissions found to assign.</p>
                    @else
                        @foreach($modules as $module)
                            <div class="mb-3 border-bottom pb-2">
                                <h5 class="mb-2">{{ $module->name }}</h5>
                                <div class="row">
                                    {{-- Group permissions for the current module --}}
                                    @foreach($module->modulePermissions as $modulePermission)
                                        @php
                                            $permissionName = $modulePermission->permission->name ?? 'N/A';
                                            $inputId = "perm_{$modulePermission->id}";
                                            // Check if this specific modulePermission is currently assigned to the role
                                            $isChecked = $role->modulePermissions->contains('id', $modulePermission->id);
                                        @endphp
                                        <div class="col-md-3 col-sm-6 mb-1">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       value="{{ $modulePermission->id }}"
                                                       id="{{ $inputId }}"
                                                       name="module_permission_ids[]"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ $inputId }}">
                                                    {{ ucfirst($permissionName) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Update Assigned Permissions
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

    </div>
@endsection
