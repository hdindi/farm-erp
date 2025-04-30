@extends('layouts.app')

@section('title', 'Feed Record Details')

@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-utensils"></i> Feed Record #{{ $feedRecord->id }}
                    <small class="text-muted">
                        (Batch: {{ $feedRecord->dailyRecord->batch->batch_code ?? 'N/A' }} |
                        Date: {{ $feedRecord->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                    </small>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('feed-records.edit', $feedRecord->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('feed-records.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $feedRecord->id }}</dd>

                    <dt class="col-sm-3">Batch Code</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('batches.show', $feedRecord->dailyRecord->batch->id ?? '#') }}">
                            {{ $feedRecord->dailyRecord->batch->batch_code ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Record Date</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('daily-records.show', $feedRecord->daily_record_id ?? '#') }}">
                            {{ $feedRecord->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Feed Type Used</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('feed-types.show', $feedRecord->feed_type_id ?? '#') }}">
                            {{ $feedRecord->feedType->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Quantity</dt>
                    <dd class="col-sm-9">{{ number_format($feedRecord->quantity_kg, 2) }} kg</dd>

                    <dt class="col-sm-3">Cost per Kg</dt>
                    <dd class="col-sm-9">{{ $feedRecord->cost_per_kg !== null ? $currencySymbol . number_format($feedRecord->cost_per_kg, 2) : 'N/A' }}</dd>

                    <dt class="col-sm-3">Total Cost</dt>
                    <dd class="col-sm-9">
                        @if($feedRecord->cost_per_kg !== null)
                            {{ $currencySymbol }}{{ number_format($feedRecord->quantity_kg * $feedRecord->cost_per_kg, 2) }}
                        @else
                            N/A
                        @endif
                    </dd>

                    <dt class="col-sm-3">Feeding Time</dt>
                    <dd class="col-sm-9">{{ $feedRecord->feeding_time ? date('H:i', strtotime($feedRecord->feeding_time)) : 'N/A' }}</dd>

                    <dt class="col-sm-3">Notes</dt>
                    <dd class="col-sm-9">{!! nl2br(e($feedRecord->notes ?? 'N/A')) !!}</dd>

                    <dt class="col-sm-3">Log Created At</dt>
                    <dd class="col-sm-9">{{ $feedRecord->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Log Updated At</dt>
                    <dd class="col-sm-9">{{ $feedRecord->updated_at->format('Y-m-d H:i:s') }}</dd>
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
                    Are you sure you want to delete Feed Record <strong>#{{ $feedRecord->id }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('feed-records.destroy', $feedRecord->id) }}" method="POST" class="d-inline">
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
