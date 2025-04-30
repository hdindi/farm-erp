@extends('layouts.app')

@section('title', 'Vaccine Schedule Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-calendar-check"></i> Schedule: {{ $vaccineSchedule->vaccine->name ?? 'N/A' }} for {{ $vaccineSchedule->batch->batch_code ?? 'N/A' }}
                    <small class="text-muted">(Due: {{ $vaccineSchedule->date_due ? $vaccineSchedule->date_due->format('Y-m-d') : 'N/A' }})</small>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('vaccine-schedule.edit', $vaccineSchedule->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('vaccine-schedule.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-info-circle"></i> Schedule Information</span>
                {{-- Button to Mark as Administered (if not already) --}}
                @if($vaccineSchedule->status != 'administered')
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#markAdministeredModal">
                        <i class="fas fa-check"></i> Mark as Administered
                    </button>
                @endif
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Schedule ID</dt>
                    <dd class="col-sm-9">{{ $vaccineSchedule->id }}</dd>

                    <dt class="col-sm-3">Batch</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('batches.show', $vaccineSchedule->batch_id) }}">
                            {{ $vaccineSchedule->batch->batch_code ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Vaccine</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('vaccines.show', $vaccineSchedule->vaccine_id) }}">
                            {{ $vaccineSchedule->vaccine->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Date Due</dt>
                    <dd class="col-sm-9">{{ $vaccineSchedule->date_due ? $vaccineSchedule->date_due->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @php
                            $statusClass = match($vaccineSchedule->status) {
                                'administered' => 'success',
                                'missed' => 'danger',
                                default => 'info', // scheduled
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($vaccineSchedule->status) }}</span>
                    </dd>

                    @if($vaccineSchedule->status == 'administered')
                        <dt class="col-sm-3">Administered Date</dt>
                        <dd class="col-sm-9">{{ $vaccineSchedule->administered_date ? $vaccineSchedule->administered_date->format('Y-m-d') : 'N/A' }}</dd>

                        <dt class="col-sm-3">Linked Vaccination Log</dt>
                        <dd class="col-sm-9">
                            @if($vaccineSchedule->vaccination_log_id)
                                <a href="{{ route('vaccination-logs.show', $vaccineSchedule->vaccination_log_id) }}">
                                    Log #{{ $vaccineSchedule->vaccination_log_id }} <i class="fas fa-external-link-alt fa-xs"></i>
                                </a>
                            @else
                                N/A
                            @endif
                        </dd>
                    @endif


                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $vaccineSchedule->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $vaccineSchedule->updated_at->format('Y-m-d H:i:s') }}</dd>
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
                    Are you sure you want to delete this Vaccine Schedule item (ID: <strong>{{ $vaccineSchedule->id }}</strong>)? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    {{-- Ensure route name matches --}}
                    <form action="{{ route('vaccine-schedule.destroy', $vaccineSchedule->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Schedule Item
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Mark Administered Modal --}}
    @if($vaccineSchedule->status != 'administered')
        <div class="modal fade" id="markAdministeredModal" tabindex="-1" aria-labelledby="markAdministeredModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Ensure route name matches --}}
                    <form action="{{ route('vaccine-schedule.mark-administered', $vaccineSchedule->id) }}" method="POST">
                        @csrf
                        @method('PATCH') {{-- Or PUT --}}
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="markAdministeredModalLabel"><i class="fas fa-check-circle"></i> Mark as Administered</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Confirm administration details for <strong>{{ $vaccineSchedule->vaccine->name ?? 'Vaccine' }}</strong> on Batch <strong>{{ $vaccineSchedule->batch->batch_code ?? 'N/A' }}</strong>.</p>
                            <div class="mb-3">
                                <label for="modal_administered_date" class="form-label">Administered Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="modal_administered_date" name="administered_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="modal_vaccination_log_id" class="form-label">Link to Vaccination Log Entry <span class="text-danger">*</span></label>
                                <select class="form-select" id="modal_vaccination_log_id" name="vaccination_log_id" required>
                                    <option value="">Select Corresponding Log Entry</option>
                                    {{-- Populate with available $vaccinationLogs - ideally filter by batch/vaccine --}}
                                    @foreach($vaccinationLogs ?? [] as $log)
                                        <option value="{{ $log->id }}">
                                            Log #{{ $log->id }} ({{ $log->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }} - {{ $log->vaccine->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">A <a href="{{ route('vaccination-logs.create') }}?batch_id={{$vaccineSchedule->batch_id}}&vaccine_id={{$vaccineSchedule->vaccine_id}}" target="_blank">Vaccination Log entry</a> must exist first.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Confirm Administration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
