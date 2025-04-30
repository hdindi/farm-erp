@extends('layouts.app')

@section('title', 'Edit Module-Permission Link')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Link: {{ $modulePermission->module->name ?? '?' }} -> {{ $modulePermission->permission->name ?? '?' }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('module-permissions.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Module-Permission Link
            </div>
            <div class="card-body">
                <form action="{{ route('module-permissions.update', $modulePermission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- Include the shared form partial --}}
                    @include('module-permissions._form', [
                       'modulePermission' => $modulePermission,
                       'modules' => $modules,
                       'permissions' => $permissions
                   ])
                </form>
            </div>
        </div>
    </div>
@endsection
