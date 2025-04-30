@extends('layouts.app')

@section('title', 'Sales Unit Details')

@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-box"></i> Sales Unit Details: {{ $salesUnit->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('sales-units.edit', $salesUnit->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('sales-units.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $salesUnit->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $salesUnit->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $salesUnit->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $salesUnit->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $salesUnit->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related Sales Prices --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-dollar-sign"></i> Active Sales Prices using this Unit
            </div>
            <div class="card-body">
                {{-- Eager load salesPrices relationship in controller: $salesUnit->load('salesPrices') --}}
                @if($salesUnit->salesPrices && $salesUnit->salesPrices->where('status','active')->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($salesUnit->salesPrices->where('status','active')->take(10) as $price) {{-- Show last 10 active --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('sales-prices.show', $price->id) }}">
                                    {{-- Display Item Type --}}
                                    @if($price->item_type === 'bird' && $price->batch)
                                        {{ ucfirst($price->item_type) }} (Batch: {{ $price->batch->batch_code }})
                                    @else
                                        {{ ucfirst($price->item_type) }}
                                    @endif
                                </a>
                                <small class="text-muted ms-2">(Effective: {{ $price->effective_date->format('Y-m-d') }})</small>
                            </div>
                            <span class="fw-bold text-success">{{ $currencySymbol }}{{ number_format($price->price, 2) }} / {{ $salesUnit->name }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('sales-prices.index') }}?sales_unit_id={{ $salesUnit->id }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-list"></i> View All Sales Prices for this Unit
                    </a>
                @else
                    <p class="text-muted">No active sales prices found using this unit.</p>
                    <a href="{{ route('sales-prices.create') }}?sales_unit_id={{ $salesUnit->id }}" class="btn btn-sm btn-success mt-2">
                        <i class="fas fa-plus"></i> Add Sales Price
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
                    Are you sure you want to delete the Sales Unit: <strong>{{ $salesUnit->name }}</strong>? This might affect related sales prices and records. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('sales-units.destroy', $salesUnit->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Sales Unit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
