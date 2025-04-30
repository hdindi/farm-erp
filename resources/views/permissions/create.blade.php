@extends('layouts.app')

@section('title', 'Add New Permission')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Permission</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Permissions List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Permission Details
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    {{-- Include the shared form partial --}}
                    @include('permissions._form')
                </form>
            </div>
        </div>
    </div>
@endsection
