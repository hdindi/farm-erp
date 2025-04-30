@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-user-edit"></i> Edit User: {{ $user->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-info me-1" title="View User">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to Users List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update User Details & Roles
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- Include the shared form partial, passing user and roles --}}
                    @include('users._form', ['user' => $user, 'roles' => $roles])
                </form>
            </div>
        </div>
    </div>
@endsection
