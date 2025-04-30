@extends('layouts.app')

@section('title', 'Edit Vaccine Schedule Item')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-edit"></i> Edit Schedule: {{ $vaccineSchedule->vaccine->name ?? 'N/A' }} for {{ $vaccineSchedule->batch->batch_code ?? 'N/A' }}
                    <small class="text-muted">(Due: {{ $vaccineSchedule->date_due ? $vaccineSchedule->date_due->format('Y-m-d') : 'N/A' }})</small>
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccine-schedule.show', $vaccineSchedule->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Item
                </a>
                <a href="{{ route('vaccine-schedule.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to Schedule List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Schedule Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'vaccineSchedule' --}}
                <form action="{{ route('vaccine-schedule.update', $vaccineSchedule->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('vaccine-schedule._form', [
                       'vaccineSchedule' => $vaccineSchedule,
                       'batches' => $batches,
                       'vaccines' => $vaccines,
                       'vaccinationLogs' => $vaccinationLogs ?? [] // Pass empty array if not always needed
                   ])

                </form>
            </div>
        </div>
    </div>
@endsection
