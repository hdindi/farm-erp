@extends('layouts.app')

@section('title', 'Edit Batch')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                {{-- Added icon --}}
                <h1><i class="fas fa-edit"></i> Edit Batch: {{ $batch->batch_code }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Batch
                </a>
                <a href="{{ route('batches.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Batch Details
            </div>
            <div class="card-body">
                <form action="{{ route('batches.update', $batch->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial --}}
                    {{-- Pass the existing $batch object and related data --}}
                    @include('batches._form', ['batch' => $batch, 'birdTypes' => $birdTypes, 'breeds' => $breeds])

                </form>
            </div>
        </div>
    </div>
@endsection
