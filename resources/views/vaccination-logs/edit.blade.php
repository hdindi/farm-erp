@extends('layouts.app')

@section('title', 'Edit Vaccination Log')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-edit"></i> Edit Vaccination Log #{{ $vaccinationLog->id }}
                    <small class="text-muted">
                        (Batch: {{ $vaccinationLog->dailyRecord->batch->batch_code ?? 'N/A' }} |
                        Date: {{ $vaccinationLog->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccination-logs.show', $vaccinationLog->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Log
                </a>
                <a href="{{ route('vaccination-logs.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Vaccination Log Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'vaccinationLog' --}}
                <form action="{{ route('vaccination-logs.update', $vaccinationLog->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('vaccination-logs._form', [
                        'vaccinationLog' => $vaccinationLog,
                        'dailyRecords' => $dailyRecords,
                        'vaccines' => $vaccines
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
