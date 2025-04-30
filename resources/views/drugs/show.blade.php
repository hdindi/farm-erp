@extends('layouts.app')

@section('title', 'Drug Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-pills"></i> Drug Details: {{ $drug->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('drugs.edit', $drug->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('drugs.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $drug->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $drug->name }}</dd>

                    <dt class="col-sm-3">Description / Usage / Notes</dt>
                    <dd class="col-sm-9">{!! nl2br(e($drug->description ?? 'N/A')) !!}</dd> {{-- Use nl2br for line breaks --}}

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $drug->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $drug->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related Disease Management Records --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-notes-medical"></i> Recent Disease Management Records using {{ $drug->name }}
            </div>
            <div class="card-body">
                {{-- Eager load diseaseManagementRecords relationship in controller: $drug->load('diseaseManagementRecords') --}}
                {{-- Assuming 'diseaseManagementRecords' is the relationship name defined in Drug model --}}
                @php $records = $drug->diseaseManagementRecords; @endphp
                @if($records && $records->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($records->take(10) as $record) {{-- Show last 10 --}}
                        <li class="list-group-item">
                            <a href="{{ route('disease-management.show', $record->id) }}">
                                Record from {{ $record->observation_date->format('Y-m-d') }}
                            </a>
                            <small class="text-muted ms-2">
                                (Batch: <a href="{{route('batches.show', $record->batch_id)}}">{{ $record->batch->batch_code ?? 'N/A' }}</a> |
                                Disease: {{ $record->disease->name ?? 'N/A' }} |
                                Affected: {{ $record->affected_count ?? 'N/A' }})
                            </small>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('disease-management.index') }}?drug_id={{ $drug->id }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-list"></i> View All Management Records
                    </a>
                @else
                    <p class="text-muted">No disease management records found using this drug.</p>
                    <a href="{{ route('disease-management.create') }}?drug_id={{ $drug->id }}" class="btn btn-sm btn-success mt-2">
                        <i class="fas fa-plus"></i> Add Management Record
                    </a>
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
                    Are you sure you want to delete the Drug: <strong>{{ $drug->name }}</strong>? This might affect related disease management records. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('drugs.destroy', $drug->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Drug
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
