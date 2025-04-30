@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-user-plus"></i> Add New User</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                User Details & Roles
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    {{-- Include the shared form partial, passing roles --}}
                    @include('users._form', ['roles' => $roles])
                </form>
            </div>
        </div>
    </div>
@endsection
