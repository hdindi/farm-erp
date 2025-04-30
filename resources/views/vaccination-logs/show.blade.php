@extends('layouts.app')

@section('title', 'Vaccination Log Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-notes-medical"></i> Vaccination Log #{{ $vaccinationLog->id }}
                    <small class="text-muted">
                        (Batch: {{ $vaccinationLog->dailyRecord->batch->batch_code ?? 'N/A' }} |
                        Date: {{ $vaccinationLog->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('vaccination-logs.edit', $vaccinationLog->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('vaccination-logs.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Log Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Log ID</dt>
                    <dd class="col-sm-9">{{ $vaccinationLog->id }}</dd>

                    <dt class="col-sm-3">Batch Code</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('batches.show', $vaccinationLog->dailyRecord->batch->id ?? '#') }}">
                            {{ $vaccinationLog->dailyRecord->batch->batch_code ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Record Date</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('daily-records.show', $vaccinationLog->daily_record_id ?? '#') }}">
                            {{ $vaccinationLog->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Vaccine Used</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('vaccines.show', $vaccinationLog->vaccine_id ?? '#') }}">
                            {{ $vaccinationLog->vaccine->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Birds Vaccinated</dt>
                    <dd class="col-sm-9">{{ $vaccinationLog->birds_vaccinated ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Administered By</dt>
                    <dd class="col-sm-9">{{ $vaccinationLog->administered_by ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Next Due Date</dt>
                    <dd class="col-sm-9">{{ $vaccinationLog->next_due_date ? $vaccinationLog->next_due_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Notes</dt>
                    <dd class="col-sm-9">{!! nl2br(e($vaccinationLog->notes ?? 'N/A')) !!}</dd>

                    <dt class="col-sm-3">Log Created At</dt>
                    <dd class="col-sm-9">{{ $vaccinationLog->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Log Updated At</dt>
                    <dd class="col-sm-9">{{ $vaccinationLog->updated_at->format('Y-m-d H:i:s') }}</dd>
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
                    Are you sure you want to delete Vaccination Log <strong>#{{ $vaccinationLog->id }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('vaccination-logs.destroy', $vaccinationLog->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Log
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
