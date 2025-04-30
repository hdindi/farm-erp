@extends('layouts.app')

@section('title', 'Link Module & Permission')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-link"></i> Link Module and Permission</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('module-permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Select Module and Permission to Link
            </div>
            <div class="card-body">
                <form action="{{ route('module-permissions.store') }}" method="POST">
                    @csrf
                    {{-- Include the shared form partial --}}
                    @include('module-permissions._form', [
                        'modules' => $modules,
                        'permissions' => $permissions
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection
