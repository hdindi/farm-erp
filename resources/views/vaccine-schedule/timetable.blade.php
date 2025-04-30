@extends('layouts.app')

@section('title', 'Vaccine Timetable')

@push('styles')
    <style>
        .batch-schedule-card {
            margin-bottom: 1.5rem;
            border-left: 5px solid #17a2b8; /* Teal border */
        }
        .batch-schedule-card .card-header {
            background-color: #e8f7f9; /* Light teal background */
            font-weight: bold;
        }
        .schedule-item {
            border-bottom: 1px solid #eee;
            padding: 0.75rem 0;
        }
        .schedule-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .schedule-date {
            font-weight: 500;
            color: #555;
        }
        .schedule-vaccine {
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-list-alt"></i> Upcoming Vaccine Timetable</h1>
                <small class="text-muted">Grouped by Batch</small>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccine-schedule.calendar') }}" class="btn btn-info me-1" title="Calendar View">
                    <i class="fas fa-calendar-day"></i> Calendar
                </a>
                <a href="{{ route('vaccine-schedule.index') }}" class="btn btn-secondary" title="List View">
                    <i class="fas fa-list"></i> List View
                </a>
                <a href="{{ route('vaccine-schedule.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Schedule Item
                </a>
            </div>
        </div>

        @if($batches->isEmpty())
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> No upcoming vaccine schedules found for active batches.
            </div>
        @else
            <div class="row">
                @foreach($batches as $batch)
                    <div class="col-md-6 col-lg-4"> {{-- Adjust column size as needed --}}
                        <div class="card batch-schedule-card shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-kiwi-bird"></i> Batch:
                                <a href="{{ route('batches.show', $batch->id) }}" class="text-decoration-none">
                                    {{ $batch->batch_code }}
                                </a>
                                <span class="badge bg-primary float-end">{{ $batch->vaccineSchedules->count() }} Upcoming</span>
                            </div>
                            <div class="card-body">
                                @if($batch->vaccineSchedules->isEmpty())
                                    <p class="text-muted fst-italic">No upcoming schedules for this batch.</p>
                                @else
                                    @foreach($batch->vaccineSchedules as $schedule)
                                        <div class="schedule-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="schedule-vaccine">
                                                    <a href="{{ route('vaccine-schedule.show', $schedule->id) }}" class="text-dark text-decoration-none">
                                                        <i class="fas fa-syringe text-info"></i> {{ $schedule->vaccine->name ?? 'N/A' }}
                                                    </a>
                                                </div>
                                                <small class="schedule-date">
                                                    Due: {{ $schedule->date_due ? $schedule->date_due->format('D, M j, Y') : 'N/A' }}
                                                    ({{ $schedule->date_due ? $schedule->date_due->diffForHumans() : '' }})
                                                </small>
                                            </div>
                                            {{-- Status badge (likely always 'scheduled' due to controller query) --}}
                                            <span class="badge bg-info">{{ ucfirst($schedule->status) }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
