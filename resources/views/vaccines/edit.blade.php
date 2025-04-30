@extends('layouts.app')

@section('title', 'Edit Vaccine')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Vaccine: {{ $vaccine->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccines.show', $vaccine->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Vaccine
                </a>
                <a href="{{ route('vaccines.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Vaccine Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'vaccine' --}}
                <form action="{{ route('vaccines.update', $vaccine->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object --}}
                    @include('vaccines._form', ['vaccine' => $vaccine])

                </form>
            </div>
        </div>
    </div>
@endsection
