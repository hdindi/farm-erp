@extends('layouts.app')

@section('title', 'Sales Record Details')

@php
    $currencySymbol = config('app.currency_symbol', '$');
    $balance = $salesRecord->total_amount - $salesRecord->amount_paid;
    $balanceClass = $balance > 0 ? 'text-danger' : 'text-success';
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1>
                    <i class="fas fa-receipt"></i> Sales Record #{{ $salesRecord->id }}
                    <small class="text-muted"> ({{ $salesRecord->sale_date->format('F j, Y') }})</small>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('sales-records.edit', $salesRecord->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                {{-- Optional Print Button --}}
                {{-- <button onclick="window.print();" class="btn btn-light me-1" title="Print"><i class="fas fa-print"></i> Print</button> --}}
                <a href="{{ route('sales-records.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Sale Summary
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-user me-1"></i> Customer & Sales Person</h5>
                        <dl class="row">
                            <dt class="col-sm-4">Customer Name:</dt>
                            <dd class="col-sm-8">{{ $salesRecord->customer_name ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Customer Phone:</dt>
                            <dd class="col-sm-8">{{ $salesRecord->customer_phone ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Sales Person:</dt>
                            <dd class="col-sm-8">{{ $salesRecord->salesPerson->name ?? 'N/A' }}</dd>
                            <dt class="col-sm-4">Sale Date:</dt>
                            <dd class="col-sm-8">{{ $salesRecord->sale_date->format('Y-m-d') }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-shopping-cart me-1"></i> Item & Payment Details</h5>
                        <dl class="row">
                            <dt class="col-sm-4">Item Sold:</dt>
                            <dd class="col-sm-8">
                                @if($salesRecord->salesPrice)
                                    @if($salesRecord->salesPrice->item_type === 'bird' && $salesRecord->salesPrice->batch)
                                        {{ ucfirst($salesRecord->salesPrice->item_type) }} (Batch: {{ $salesRecord->salesPrice->batch->batch_code }})
                                    @else
                                        {{ ucfirst($salesRecord->salesPrice->item_type) }}
                                    @endif
                                    <small><a href="{{ route('sales-prices.show', $salesRecord->sales_price_id) }}" target="_blank">(View Price <i class="fas fa-external-link-alt fa-xs"></i>)</a></small>
                                @else
                                    N/A
                                @endif
                            </dd>

                            <dt class="col-sm-4">Quantity:</dt>
                            <dd class="col-sm-8">{{ $salesRecord->quantity }} {{ $salesRecord->salesPrice->salesUnit->name ?? 'Unit' }}(s)</dd>

                            <dt class="col-sm-4">Total Amount:</dt>
                            <dd class="col-sm-8 fw-bold">{{ $currencySymbol }}{{ number_format($salesRecord->total_amount, 2) }}</dd>

                            <dt class="col-sm-4">Amount Paid:</dt>
                            <dd class="col-sm-8 text-success">{{ $currencySymbol }}{{ number_format($salesRecord->amount_paid, 2) }}</dd>

                            <dt class="col-sm-4">Balance Due:</dt>
                            <dd class="col-sm-8 fw-bold {{ $balanceClass }}">{{ $currencySymbol }}{{ number_format($balance, 2) }}</dd>

                        </dl>
                    </div>
                </div>
                <hr>
                <h5><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                <p>{{ $salesRecord->notes ?? 'No notes provided.' }}</p>
                <hr>
                <small class="text-muted">
                    Recorded At: {{ $salesRecord->created_at->format('Y-m-d H:i:s') }} |
                    Last Updated: {{ $salesRecord->updated_at->format('Y-m-d H:i:s') }}
                </small>
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
                    Are you sure you want to delete Sales Record <strong>#{{ $salesRecord->id }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('sales-records.destroy', $salesRecord->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Sales Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
