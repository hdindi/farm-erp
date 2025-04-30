@extends('layouts.app')

@section('title', 'Stage Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-layer-group"></i> Stage Details: {{ $stage->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('stages.edit', $stage->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('stages.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $stage->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $stage->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $stage->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Min Age (Days)</dt>
                    <dd class="col-sm-9">{{ $stage->min_age_days }}</dd>

                    <dt class="col-sm-3">Max Age (Days)</dt>
                    <dd class="col-sm-9">{{ $stage->max_age_days }}</dd>

                    <dt class="col-sm-3">Target Weight (Grams)</dt>
                    <dd class="col-sm-9">{{ $stage->target_weight_grams ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $stage->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $stage->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related Daily Records --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-clipboard-list"></i> Recent Daily Records in this Stage
            </div>
            <div class="card-body">
                {{-- Eager load dailyRecords relationship in controller: $stage->load('dailyRecords') --}}
                @if($stage->dailyRecords && $stage->dailyRecords->count() > 0)
                    <ul class="list-group">
                        @foreach($stage->dailyRecords->sortByDesc('record_date')->take(10) as $record) {{-- Show last 10 --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('daily-records.show', $record->id) }}">
                                    Record Date: {{ $record->record_date->format('Y-m-d') }}
                                </a>
                                <small class="text-muted ms-2">(Batch: {{ $record->batch->batch_code ?? 'N/A' }})</small>
                            </div>
                            <span>Alive: {{ $record->alive_count }} | Dead: {{ $record->dead_count }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('daily-records.index') }}?stage_id={{ $stage->id }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-list"></i> View All Daily Records for this Stage
                    </a>
                @else
                    <p class="text-muted">No daily records found for this stage.</p>
                @endif
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
                    Are you sure you want to delete the Stage: <strong>{{ $stage->name }}</strong>? This might affect related daily records. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('stages.destroy', $stage->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Stage
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
