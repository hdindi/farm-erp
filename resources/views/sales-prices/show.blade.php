@extends('layouts.app')

@section('title', 'Sales Price Details')

@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                {{-- More descriptive title --}}
                <h1>
                    <i class="fas fa-dollar-sign"></i> Price Details: {{ ucfirst($salesPrice->item_type) }}
                    @if($salesPrice->item_type === 'bird' && $salesPrice->batch)
                        <span class="text-muted">(Batch: <a href="{{ route('batches.show', $salesPrice->item_id) }}">{{ $salesPrice->batch->batch_code }} <i class="fas fa-external-link-alt fa-xs"></i></a>)</span>
                    @endif
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('sales-prices.edit', $salesPrice->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('sales-prices.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $salesPrice->id }}</dd>

                    <dt class="col-sm-3">Item Type</dt>
                    <dd class="col-sm-9">{{ ucfirst($salesPrice->item_type) }}</dd>

                    @if($salesPrice->item_type === 'bird' && $salesPrice->batch)
                        <dt class="col-sm-3">Batch</dt>
                        <dd class="col-sm-9">
                            <a href="{{ route('batches.show', $salesPrice->item_id) }}">
                                {{ $salesPrice->batch->batch_code }} <i class="fas fa-external-link-alt fa-xs"></i>
                            </a>
                        </dd>
                    @endif

                    <dt class="col-sm-3">Sales Unit</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('sales-units.show', $salesPrice->sales_unit_id) }}">
                            {{ $salesPrice->salesUnit->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Price</dt>
                    <dd class="col-sm-9 fw-bold fs-5 text-success">
                        {{ $currencySymbol }}{{ number_format($salesPrice->price, 2) }} / {{ $salesPrice->salesUnit->name ?? 'Unit' }}
                    </dd>

                    <dt class="col-sm-3">Effective Date</dt>
                    <dd class="col-sm-9">{{ $salesPrice->effective_date ? $salesPrice->effective_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($salesPrice->status == 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $salesPrice->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $salesPrice->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related Sales Records using this price --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-receipt"></i> Recent Sales Records using this Price
            </div>
            <div class="card-body">
                {{-- Eager load salesRecords relationship in controller: $salesPrice->load('salesRecords') --}}
                @if($salesPrice->salesRecords && $salesPrice->salesRecords->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($salesPrice->salesRecords->take(10) as $record) {{-- Show last 10 --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('sales-records.show', $record->id) }}">
                                    Record from {{ $record->sale_date->format('Y-m-d') }}
                                </a>
                                <small class="text-muted ms-2">(Sold by: {{ $record->salesPerson->name ?? 'N/A' }})</small>
                            </div>
                            <span>{{ $record->quantity }} {{ $salesPrice->salesUnit->name ?? 'Unit' }}(s) - Total: {{ $currencySymbol }}{{ number_format($record->total_amount, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('sales-records.index') }}?sales_price_id={{ $salesPrice->id }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-list"></i> View All Sales Records
                    </a>
                @else
                    <p class="text-muted">No sales records found using this specific price entry.</p>
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
                    Are you sure you want to delete this sales price record (ID: <strong>{{ $salesPrice->id }}</strong>)? This action cannot be undone. Consider setting it to 'Inactive' instead if it might be needed for historical reporting.
                    <p class="mt-2 small text-muted">
                        Item: {{ ucfirst($salesPrice->item_type) }} |
                        Price: {{ $currencySymbol }}{{ number_format($salesPrice->price, 2) }} / {{ $salesPrice->salesUnit->name ?? 'Unit' }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('sales-prices.destroy', $salesPrice->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Sales Price
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
