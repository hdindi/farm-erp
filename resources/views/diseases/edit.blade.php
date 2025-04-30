@extends('layouts.app')

@section('title', 'Edit Disease')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Disease: {{ $disease->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('diseases.show', $disease->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Disease
                </a>
                <a href="{{ route('diseases.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Disease Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'disease' --}}
                <form action="{{ route('diseases.update', $disease->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object --}}
                    @include('diseases._form', ['disease' => $disease])

                </form>
            </div>
        </div>
    </div>
@endsection
