@extends('layouts.app')

@section('title', 'Edit Disease Management Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-edit"></i> Edit Record #{{ $diseaseManagement->id }}
                    <small class="text-muted">
                        (Batch: {{ $diseaseManagement->batch->batch_code ?? 'N/A' }} |
                        Observed: {{ $diseaseManagement->observation_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('disease-management.show', $diseaseManagement->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Record
                </a>
                <a href="{{ route('disease-management.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Record Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'diseaseManagement' --}}
                <form action="{{ route('disease-management.update', $diseaseManagement->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('disease-management._form', [
                       'diseaseManagement' => $diseaseManagement,
                       'batches' => $batches,
                       'diseases' => $diseases,
                       'drugs' => $drugs
                   ])

                </form>
            </div>
        </div>
    </div>
@endsection
