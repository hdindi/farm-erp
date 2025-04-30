@extends('layouts.app')

@section('title', 'Permission Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-key"></i> Permission Details: {{ $permission->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary me-1" title="Edit Permission">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $permission->id }}</dd>

                    <dt class="col-sm-3">Name (Code)</dt>
                    <dd class="col-sm-9"><code>{{ $permission->name }}</code></dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $permission->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($permission->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $permission->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $permission->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Show Modules this permission is linked to --}}
        <div class="card mt-4">
            <div class="card-header"><i class="fas fa-puzzle-piece"></i> Linked Modules</div>
            <div class="card-body">
                {{-- Ensure $permission->modules relationship is loaded --}}
                @if($permission->modules->isNotEmpty())
                    <ul class="list-inline">
                        @foreach($permission->modules as $module)
                            <li class="list-inline-item mb-1"><span class="badge bg-secondary">{{ $module->name }}</span></li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">This permission is not currently assigned to any modules via the Module-Permissions link.</p>
                @endif
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
                    Are you sure you want to delete the Permission: <strong>{{ $permission->name }}</strong>? This will remove it from any Module-Permission links and potentially affect Role assignments. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Permission
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
