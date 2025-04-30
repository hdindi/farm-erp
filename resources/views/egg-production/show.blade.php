@extends('layouts.app')

@section('title', 'Egg Production Record Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-egg"></i> Egg Record #{{ $eggProduction->id }}
                    <small class="text-muted">
                        (Batch: {{ $eggProduction->dailyRecord->batch->batch_code ?? 'N/A' }} |
                        Date: {{ $eggProduction->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('egg-production.edit', $eggProduction->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('egg-production.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $eggProduction->id }}</dd>

                    <dt class="col-sm-3">Batch Code</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('batches.show', $eggProduction->dailyRecord->batch->id ?? '#') }}">
                            {{ $eggProduction->dailyRecord->batch->batch_code ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Record Date</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('daily-records.show', $eggProduction->daily_record_id ?? '#') }}">
                            {{ $eggProduction->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Total Eggs Collected</dt>
                    <dd class="col-sm-9 fw-bold">{{ $eggProduction->total_eggs ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Good Eggs</dt>
                    <dd class="col-sm-9">{{ $eggProduction->good_eggs ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Cracked Eggs</dt>
                    <dd class="col-sm-9">{{ $eggProduction->cracked_eggs ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Damaged/Other Eggs</dt>
                    <dd class="col-sm-9">{{ $eggProduction->damaged_eggs ?? 'N/A' }}</dd>

                    {{-- Calculate Lay Rate --}}
                    @php
                        $aliveCount = $eggProduction->dailyRecord->alive_count ?? 0;
                        $layRate = ($aliveCount > 0) ? ($eggProduction->total_eggs / $aliveCount) * 100 : 0;
                    @endphp
                    <dt class="col-sm-3">Lay Rate (%)</dt>
                    <dd class="col-sm-9">{{ $aliveCount > 0 ? number_format($layRate, 1) . '%' : 'N/A' }} <small class="text-muted">(Total Eggs / Alive Birds)</small></dd>


                    <dt class="col-sm-3">Collection Time</dt>
                    <dd class="col-sm-9">{{ $eggProduction->collection_time ? date('H:i', strtotime($eggProduction->collection_time)) : 'N/A' }}</dd>

                    <dt class="col-sm-3">Notes</dt>
                    <dd class="col-sm-9">{!! nl2br(e($eggProduction->notes ?? 'N/A')) !!}</dd>

                    <dt class="col-sm-3">Log Created At</dt>
                    <dd class="col-sm-9">{{ $eggProduction->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Log Updated At</dt>
                    <dd class="col-sm-9">{{ $eggProduction->updated_at->format('Y-m-d H:i:s') }}</dd>
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
                    Are you sure you want to delete Egg Production Record <strong>#{{ $eggProduction->id }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('egg-production.destroy', $eggProduction->id) }}" method="POST" class="d-inline">
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
