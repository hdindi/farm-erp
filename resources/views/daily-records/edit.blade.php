@extends('layouts.app')

@section('title', 'Edit Daily Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-edit"></i> Edit Record for {{ $dailyRecord->batch->batch_code ?? 'N/A' }}
                    <small class="text-muted">
                        (Date: {{ $dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('daily-records.show', $dailyRecord->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Record
                </a>
                <a href="{{ route('daily-records.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Daily Record Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'dailyRecord' --}}
                <form action="{{ route('daily-records.update', $dailyRecord->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('daily-records._form', [
                       'dailyRecord' => $dailyRecord,
                       'batches' => $batches,
                       'stages' => $stages
                   ])

                </form>
            </div>
        </div>
    </div>
@endsection
