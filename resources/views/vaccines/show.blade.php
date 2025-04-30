@extends('layouts.app')

@section('title', 'Vaccine Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-syringe"></i> Vaccine Details: {{ $vaccine->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('vaccines.edit', $vaccine->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('vaccines.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $vaccine->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $vaccine->name }}</dd>

                    <dt class="col-sm-3">Manufacturer</dt>
                    <dd class="col-sm-9">{{ $vaccine->manufacturer ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Description / Notes</dt>
                    <dd class="col-sm-9">{!! nl2br(e($vaccine->description ?? 'N/A')) !!}</dd>

                    <dt class="col-sm-3">Min Age (Days)</dt>
                    <dd class="col-sm-9">{{ $vaccine->minimum_age_days ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Booster Interval (Days)</dt>
                    <dd class="col-sm-9">{{ $vaccine->booster_interval_days ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $vaccine->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $vaccine->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Related Records Sections --}}
        <div class="row">
            {{-- Vaccination Logs --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-notes-medical"></i> Recent Vaccination Logs using {{ $vaccine->name }}
                    </div>
                    <div class="card-body">
                        {{-- Eager load vaccinationLogs relationship in controller --}}
                        @php $logs = $vaccine->vaccinationLogs; @endphp
                        @if($logs && $logs->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($logs->take(5) as $log)
                                    <li class="list-group-item">
                                        <a href="{{ route('vaccination-logs.show', $log->id) }}">
                                            Log from {{ $log->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }}
                                        </a>
                                        <small class="text-muted ms-2">
                                            (Batch: <a href="{{route('batches.show', $log->dailyRecord->batch->id)}}">{{ $log->dailyRecord->batch->batch_code ?? 'N/A' }}</a> |
                                            Birds: {{ $log->birds_vaccinated ?? 'N/A' }})
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('vaccination-logs.index') }}?vaccine_id={{ $vaccine->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Logs
                            </a>
                        @else
                            <p class="text-muted">No vaccination logs found for this vaccine.</p>
                            <a href="{{ route('vaccination-logs.create') }}?vaccine_id={{ $vaccine->id }}" class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-plus"></i> Add Log Entry
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Vaccine Schedules --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt"></i> Recent Vaccine Schedules for {{ $vaccine->name }}
                    </div>
                    <div class="card-body">
                        {{-- Eager load vaccineSchedules relationship in controller --}}
                        @php $schedules = $vaccine->vaccineSchedules; @endphp
                        @if($schedules && $schedules->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($schedules->take(5) as $schedule)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ route('vaccine-schedule.show', $schedule->id) }}">
                                                Schedule for {{ $schedule->date_due->format('Y-m-d') }}
                                            </a>
                                            <small class="text-muted ms-2">
                                                (Batch: <a href="{{route('batches.show', $schedule->batch_id)}}">{{ $schedule->batch->batch_code ?? 'N/A' }}</a>)
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $schedule->status == 'administered' ? 'success' : ($schedule->status == 'scheduled' ? 'info' : 'warning') }} rounded-pill">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('vaccine-schedule.index') }}?vaccine_id={{ $vaccine->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Schedules
                            </a>
                        @else
                            <p class="text-muted">No vaccine schedules found for this vaccine.</p>
                            <a href="{{ route('vaccine-schedule.create') }}?vaccine_id={{ $vaccine->id }}" class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-plus"></i> Add Schedule Item
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the Vaccine: <strong>{{ $vaccine->name }}</strong>? This might affect related vaccination logs and schedules. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('vaccines.destroy', $vaccine->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Vaccine
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
