@extends('layouts.app')

@section('title', 'Edit Breed')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Breed: {{ $breed->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('breeds.show', $breed->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Breed
                </a>
                <a href="{{ route('breeds.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Breed Details
            </div>
            <div class="card-body">
                <form action="{{ route('breeds.update', $breed->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing $breed object --}}
                    @include('breeds._form', ['breed' => $breed])

                </form>
            </div>
        </div>
    </div>
@endsection
