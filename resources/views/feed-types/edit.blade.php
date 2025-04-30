@extends('layouts.app')

@section('title', 'Edit Feed Type')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Feed Type: {{ $feedType->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('feed-types.show', $feedType->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Feed Type
                </a>
                <a href="{{ route('feed-types.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Feed Type Details
            </div>
            <div class="card-body">
                <form action="{{ route('feed-types.update', $feedType->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing $feedType object --}}
                    @include('feed-types._form', ['feedType' => $feedType])

                </form>
            </div>
        </div>
    </div>
@endsection
