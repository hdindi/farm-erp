@extends('layouts.app')

@section('title', 'Edit Egg Production Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-edit"></i> Edit Egg Record #{{ $eggProduction->id }}
                    <small class="text-muted">
                        (Batch: {{ $eggProduction->dailyRecord->batch->batch_code ?? 'N/A' }} |
                        Date: {{ $eggProduction->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('egg-production.show', $eggProduction->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Record
                </a>
                <a href="{{ route('egg-production.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        @if($dailyRecords->isEmpty())
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i> No suitable daily records found for association.
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    Update Egg Collection Details
                </div>
                <div class="card-body">
                    {{-- Add id="egg-production-form" if using JS submit prevention --}}
                    <form action="{{ route('egg-production.update', $eggProduction->id) }}" method="POST" id="egg-production-form">
                        @csrf
                        @method('PUT') {{-- Important for update action --}}

                        {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                        @include('egg-production._form', [
                            'eggProduction' => $eggProduction,
                            'dailyRecords' => $dailyRecords
                        ])

                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
