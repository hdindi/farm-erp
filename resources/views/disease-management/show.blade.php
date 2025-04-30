@extends('layouts.app')

@section('title', 'Disease Management Record Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-notes-medical"></i> Record #{{ $diseaseManagement->id }}: {{ $diseaseManagement->disease->name ?? 'N/A' }}
                    <small class="text-muted">
                        (Batch: {{ $diseaseManagement->batch->batch_code ?? 'N/A' }} |
                        Observed: {{ $diseaseManagement->observation_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('disease-management.edit', $diseaseManagement->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('disease-management.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Record Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Record ID</dt>
                    <dd class="col-sm-9">{{ $diseaseManagement->id }}</dd>

                    <dt class="col-sm-3">Observation Date</dt>
                    <dd class="col-sm-9">{{ $diseaseManagement->observation_date ? $diseaseManagement->observation_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Batch</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('batches.show', $diseaseManagement->batch_id) }}">
                            {{ $diseaseManagement->batch->batch_code ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Observed Disease</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('diseases.show', $diseaseManagement->disease_id) }}">
                            {{ $diseaseManagement->disease->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Number Affected</dt>
                    <dd class="col-sm-9">{{ $diseaseManagement->affected_count ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Drug Used</dt>
                    <dd class="col-sm-9">
                        @if($diseaseManagement->drug_id)
                            <a href="{{ route('drugs.show', $diseaseManagement->drug_id) }}">
                                {{ $diseaseManagement->drug->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                            </a>
                        @else
                            N/A
                        @endif
                    </dd>

                    <dt class="col-sm-3">Treatment Start Date</dt>
                    <dd class="col-sm-9">{{ $diseaseManagement->treatment_start_date ? $diseaseManagement->treatment_start_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Treatment End Date</dt>
                    <dd class="col-sm-9">{{ $diseaseManagement->treatment_end_date ? $diseaseManagement->treatment_end_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Notes / Observations</dt>
                    <dd class="col-sm-9">{!! nl2br(e($diseaseManagement->notes ?? 'N/A')) !!}</dd>

                    <dt class="col-sm-3">Record Created At</dt>
                    <dd class="col-sm-9">{{ $diseaseManagement->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Record Updated At</dt>
                    <dd class="col-sm-9">{{ $diseaseManagement->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
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
                    Are you sure you want to delete Disease Management Record <strong>#{{ $diseaseManagement->id }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('disease-management.destroy', $diseaseManagement->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
