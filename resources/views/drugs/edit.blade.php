@extends('layouts.app')

@section('title', 'Edit Drug')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Drug: {{ $drug->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('drugs.show', $drug->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Drug
                </a>
                <a href="{{ route('drugs.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Drug Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'drug' --}}
                <form action="{{ route('drugs.update', $drug->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object --}}
                    @include('drugs._form', ['drug' => $drug])

                </form>
            </div>
        </div>
    </div>
@endsection
