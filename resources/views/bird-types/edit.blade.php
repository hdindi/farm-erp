@extends('layouts.app')

@section('title', 'Edit Bird Type')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Bird Type: {{ $birdType->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('bird-types.show', $birdType->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Bird Type
                </a>
                <a href="{{ route('bird-types.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Bird Type Details
            </div>
            <div class="card-body">
                <form action="{{ route('bird-types.update', $birdType->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing $birdType object --}}
                    @include('bird-types._form', ['birdType' => $birdType])

                </form>
            </div>
        </div>
    </div>
@endsection
