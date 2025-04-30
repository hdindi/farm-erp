@extends('layouts.app')

@section('title', 'Daily Record Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-clipboard-list"></i> Daily Record: {{ $dailyRecord->record_date->format('M d, Y') }}
                    <small class="text-muted">
                        (Batch: {{ $dailyRecord->batch->batch_code ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('daily-records.edit', $dailyRecord->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                {{-- Quick Add buttons for related records --}}
                <a href="{{ route('feed-records.create') }}?daily_record_id={{ $dailyRecord->id }}" class="btn btn-success me-1" title="Add Feed Record for this Day">
                    <i class="fas fa-utensils"></i>+ Feed
                </a>
                <a href="{{ route('egg-production.create') }}?daily_record_id={{ $dailyRecord->id }}" class="btn btn-warning me-1" title="Add Egg Production for this Day">
                    <i class="fas fa-egg"></i>+ Eggs
                </a>
                <a href="{{ route('vaccination-logs.create') }}?daily_record_id={{ $dailyRecord->id }}" class="btn btn-info me-1" title="Add Vaccination Log for this Day">
                    <i class="fas fa-syringe"></i>+ Vax Log
                </a>
                <a href="{{ route('daily-records.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Core Daily Stats --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Daily Stats
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Record Date</dt>
                    <dd class="col-sm-9">{{ $dailyRecord->record_date ? $dailyRecord->record_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Batch</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('batches.show', $dailyRecord->batch_id) }}">
                            {{ $dailyRecord->batch->batch_code ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Stage</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('stages.show', $dailyRecord->stage_id) }}">
                            {{ $dailyRecord->stage->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                        (Day #{{ $dailyRecord->day_in_stage ?? 'N/A' }})
                    </dd>

                    <dt class="col-sm-3">Number Alive (End of Day)</dt>
                    <dd class="col-sm-9">{{ $dailyRecord->alive_count ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Mortality (Dead)</dt>
                    <dd class="col-sm-9">{{ $dailyRecord->dead_count ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Culled</dt>
                    <dd class="col-sm-9">{{ $dailyRecord->culls_count ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Mortality Rate (%)</dt>
                    {{-- Calculate if needed, or show stored value --}}
                    <dd class="col-sm-9">{{ $dailyRecord->mortality_rate !== null ? number_format($dailyRecord->mortality_rate, 2) . '%' : 'N/A' }}</dd>

                    <dt class="col-sm-3">Average Weight (g)</dt>
                    <dd class="col-sm-9">{{ $dailyRecord->average_weight_grams ?? 'N/A' }} g</dd>

                    <dt class="col-sm-3">Notes / Observations</dt>
                    <dd class="col-sm-9">{!! nl2br(e($dailyRecord->notes ?? 'N/A')) !!}</dd>

                    <dt class="col-sm-3">Record Created At</dt>
                    <dd class="col-sm-9">{{ $dailyRecord->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Record Updated At</dt>
                    <dd class="col-sm-9">{{ $dailyRecord->updated_at->format('Y-m-d H:i:s') }}</dd>

                </dl>
            </div>
        </div>


        {{-- Related Records Section --}}
        <div class="row">
            {{-- Feed Records --}}
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header"><i class="fas fa-utensils"></i> Feed Records for this Day</div>
                    <div class="card-body">
                        @php $feedRecs = $dailyRecord->feedRecords; @endphp {{-- Assuming relationship exists --}}
                        @if($feedRecs && $feedRecs->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($feedRecs as $fr)
                                    <li class="list-group-item">
                                        <a href="{{route('feed-records.show', $fr->id)}}">{{ $fr->feedType->name ?? 'N/A' }}:</a> {{ number_format($fr->quantity_kg, 2) }} kg
                                        @if($fr->feeding_time) ({{ date('H:i', strtotime($fr->feeding_time)) }}) @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No feed records for this day.</p>
                        @endif
                        <a href="{{ route('feed-records.create') }}?daily_record_id={{ $dailyRecord->id }}" class="btn btn-sm btn-outline-success mt-2">
                            <i class="fas fa-plus"></i> Add Feed Record
                        </a>
                    </div>
                </div>
            </div>

            {{-- Egg Production --}}
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header"><i class="fas fa-egg"></i> Egg Production for this Day</div>
                    <div class="card-body">
                        @php $eggProd = $dailyRecord->eggProduction()->first(); @endphp {{-- Assuming ONE per day --}}
                        @if($eggProd)
                            <dl>
                                <dt>Total Eggs:</dt><dd>{{ $eggProd->total_eggs }}</dd>
                                <dt>Good:</dt><dd>{{ $eggProd->good_eggs }}</dd>
                                <dt>Cracked:</dt><dd>{{ $eggProd->cracked_eggs }}</dd>
                                <dt>Damaged:</dt><dd>{{ $eggProd->damaged_eggs }}</dd>
                                <dt>Collection Time:</dt><dd>{{ $eggProd->collection_time ? date('H:i', strtotime($eggProd->collection_time)) : '-' }}</dd>
                            </dl>
                            <a href="{{route('egg-production.edit', $eggProd->id)}}" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-edit"></i> Edit Egg Record</a>
                        @else
                            <p class="text-muted">No egg production record for this day.</p>
                            <a href="{{ route('egg-production.create') }}?daily_record_id={{ $dailyRecord->id }}" class="btn btn-sm btn-outline-warning mt-2">
                                <i class="fas fa-plus"></i> Add Egg Record
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Vaccination Logs --}}
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header"><i class="fas fa-syringe"></i> Vaccinations on this Day</div>
                    <div class="card-body">
                        @php $vaxLogs = $dailyRecord->vaccinationLogs; @endphp {{-- Assuming relationship exists --}}
                        @if($vaxLogs && $vaxLogs->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($vaxLogs as $vl)
                                    <li class="list-group-item">
                                        <a href="{{route('vaccination-logs.show', $vl->id)}}">{{ $vl->vaccine->name ?? 'N/A' }}:</a> {{ $vl->birds_vaccinated }} birds
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No vaccinations logged for this day.</p>
                        @endif
                        <a href="{{ route('vaccination-logs.create') }}?daily_record_id={{ $dailyRecord->id }}" class="btn btn-sm btn-outline-info mt-2">
                            <i class="fas fa-plus"></i> Add Vax Log
                        </a>
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
                    Are you sure you want to delete Daily Record <strong>#{{ $dailyRecord->id }}</strong>? This will also delete associated Feed Records, Egg Production, and Vaccination Logs for this day. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('daily-records.destroy', $dailyRecord->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Daily Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
