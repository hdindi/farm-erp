@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-user"></i> User Details: {{ $user->name }}</h1>
            </div>
            <div class="col-auto">
                {{-- Prevent deleting self --}}
                @if(auth()->id() != $user->id)
                    <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                @endif
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary me-1" title="Edit User">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $user->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>

                    <dt class="col-sm-3">Phone Number</dt>
                    <dd class="col-sm-9">{{ $user->phone_number ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Email Verified</dt>
                    <dd class="col-sm-9">{{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'No' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $user->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $user->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Assigned Roles --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-shield"></i> Assigned Roles</span>
                <a href="{{ route('users.edit', $user->id) }}#roles" class="btn btn-sm btn-outline-primary"> {{-- Link to edit section --}}
                    <i class="fas fa-edit"></i> Edit Roles
                </a>
            </div>
            <div class="card-body">
                @if($user->roles->isEmpty())
                    <p class="text-muted">No roles assigned to this user.</p>
                @else
                    <ul class="list-inline">
                        @foreach($user->roles as $role)
                            <li class="list-inline-item mb-1">
                                <span class="badge bg-info">{{ $role->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>

    {{-- Delete Confirmation Modal --}}
    @if(auth()->id() != $user->id)
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete the User: <strong>{{ $user->name }}</strong>? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
