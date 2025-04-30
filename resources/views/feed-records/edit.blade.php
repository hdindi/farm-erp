@extends('layouts.app')

@section('title', 'Edit Feed Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-edit"></i> Edit Feed Record #{{ $feedRecord->id }}
                    <small class="text-muted">
                        (Batch: {{ $feedRecord->dailyRecord->batch->batch_code ?? 'N/A' }} |
                        Date: {{ $feedRecord->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('feed-records.show', $feedRecord->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Record
                </a>
                <a href="{{ route('feed-records.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Feed Record Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'feedRecord' --}}
                <form action="{{ route('feed-records.update', $feedRecord->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('feed-records._form', [
                        'feedRecord' => $feedRecord,
                        'dailyRecords' => $dailyRecords,
                        'feedTypes' => $feedTypes
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
