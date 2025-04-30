@extends('layouts.app')

@section('title', 'Edit Stage')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Stage: {{ $stage->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('stages.show', $stage->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Stage
                </a>
                <a href="{{ route('stages.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Stage Details
            </div>
            <div class="card-body">
                <form action="{{ route('stages.update', $stage->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing $stage object --}}
                    @include('stages._form', ['stage' => $stage])

                </form>
            </div>
        </div>
    </div>
@endsection
