@extends('layouts.app')

@section('title', 'Batch Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                {{-- Added icon --}}
                <h1><i class="fas fa-kiwi-bird"></i> Batch Details: {{ $batch->batch_code }}</h1>
            </div>
            <div class="col-auto">
                {{-- Added Delete button with modal trigger (optional but recommended) --}}
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteBatchModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('batches.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Main Details Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Basic Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h5>Batch & Type</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width: 150px;">Batch Code:</th>
                                <td>{{ $batch->batch_code }}</td>
                            </tr>
                            <tr>
                                <th>Bird Type:</th>
                                <td>{{ $batch->birdType->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Breed:</th>
                                <td>{{ $batch->breed->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Source Farm:</th>
                                <td>{{ $batch->source_farm ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-{{ $batch->status == 'active' ? 'success' : ($batch->status == 'completed' ? 'info' : 'danger') }}">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5>Population & Dates</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width: 180px;">Initial Population:</th>
                                <td>{{ $batch->initial_population }}</td>
                            </tr>
                            <tr>
                                <th>Current Population:</th>
                                <td>{{ $batch->current_population }}</td>
                            </tr>
                            <tr>
                                <th>Age at Receipt (Days):</th>
                                <td>{{ $batch->bird_age_days }}</td>
                            </tr>
                            <tr>
                                <th>Date Received:</th>
                                <td>{{ $batch->date_received ? $batch->date_received->format('Y-m-d') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Hatch Date:</th>
                                <td>{{ $batch->hatch_date ? $batch->hatch_date->format('Y-m-d') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Expected End Date:</th>
                                <td>{{ $batch->expected_end_date ? $batch->expected_end_date->format('Y-m-d') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Records Row --}}
        <div class="row">
            {{-- Daily Records Card --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clipboard-list"></i> Recent Daily Records
                    </div>
                    <div class="card-body">
                        @if($batch->dailyRecords && $batch->dailyRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Alive</th>
                                        <th>Dead</th>
                                        <th>Stage</th>
                                        <th>Avg. Wt (g)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {{-- Eager load stage relationship in controller if not already done --}}
                                    @foreach($batch->dailyRecords->sortByDesc('record_date')->take(5) as $record)
                                        <tr>
                                            <td>{{ $record->record_date->format('Y-m-d') }}</td>
                                            <td>{{ $record->alive_count }}</td>
                                            <td>{{ $record->dead_count }}</td>
                                            <td>{{ $record->stage->name ?? 'N/A' }}</td>
                                            <td>{{ $record->average_weight_grams ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Link to filtered daily records --}}
                            <a href="{{ route('daily-records.index') }}?batch_id={{ $batch->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Daily Records
                            </a>
                        @else
                            <p class="text-muted">No daily records found for this batch.</p>
                            <a href="{{ route('daily-records.create') }}?batch_id={{ $batch->id }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Daily Record
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Vaccination Schedule Card --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-syringe"></i> Upcoming/Recent Vaccinations
                    </div>
                    <div class="card-body">
                        {{-- Eager load vaccine relationship in controller --}}
                        @if($batch->vaccineSchedules && $batch->vaccineSchedules->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                    <tr>
                                        <th>Vaccine</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Administered</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($batch->vaccineSchedules->sortBy('date_due')->take(5) as $schedule)
                                        <tr>
                                            <td>{{ $schedule->vaccine->name ?? 'N/A' }}</td>
                                            <td>{{ $schedule->date_due ? $schedule->date_due->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->status == 'administered' ? 'success' : ($schedule->status == 'scheduled' ? 'info' : 'warning') }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $schedule->administered_date ? $schedule->administered_date->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <a href="{{ route('vaccine-schedule.index') }}?batch_id={{ $batch->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-calendar-alt"></i> View Full Schedule
                            </a>
                        @else
                            <p class="text-muted">No vaccination schedules found for this batch.</p>
                            <a href="{{ route('vaccine-schedule.create') }}?batch_id={{ $batch->id }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Add Schedule Item
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Add more cards/rows for other related data like Feed Records, Disease Management etc. if needed --}}

    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteBatchModal" tabindex="-1" aria-labelledby="deleteBatchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteBatchModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete Batch <strong>{{ $batch->batch_code }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Batch
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Add specific styles if needed --}}
@push('styles')
    <style>
        .table-borderless th {
            border: none !important;
            white-space: nowrap;
        }
        .table-borderless td {
            border: none !important;
        }
        .card-header i {
            margin-right: 0.5rem;
        }
    </style>
@endpush
