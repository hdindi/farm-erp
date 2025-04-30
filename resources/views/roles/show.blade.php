@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-user-shield"></i> Role Details: {{ $role->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary me-1" title="Edit Role & Permissions">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $role->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $role->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $role->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($role->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $role->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $role->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Assigned Permissions --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-key"></i> Assigned Permissions
            </div>
            <div class="card-body">
                @if($role->modulePermissions->isEmpty())
                    <p class="text-muted">No permissions assigned to this role.</p>
                @else
                    @php
                        // Group permissions by module for display
                        $groupedPermissions = $role->modulePermissions->groupBy('module.name');
                    @endphp
                    @foreach($groupedPermissions as $moduleName => $permissions)
                        <div class="mb-3 border-bottom pb-2">
                            <h5 class="mb-1">{{ $moduleName ?? 'General Permissions' }}</h5>
                            <ul class="list-inline">
                                @foreach($permissions as $modulePermission)
                                    <li class="list-inline-item">
                                         <span class="badge bg-primary">
                                             {{ $modulePermission->permission->name ?? 'N/A' }}
                                         </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @endif
                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-edit"></i> Edit Assigned Permissions
                </a>
            </div>
        </div>

    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the Role: <strong>{{ $role->name }}</strong>? This will unassign it from all users. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Role
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
