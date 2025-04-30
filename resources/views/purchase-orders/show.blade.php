@extends('layouts.app')

@section('title', 'Purchase Order Details')

@php
    // Define currency symbol (consider moving this to a config or helper)
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-receipt"></i> Purchase Order: {{ $purchaseOrder->purchase_order_no }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('purchase-orders.edit', $purchaseOrder->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Summary Info --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><i class="fas fa-truck me-2"></i>Supplier</h5>
                        <p class="card-text">
                            <a href="{{ route('suppliers.show', $purchaseOrder->supplier_id) }}">
                                {{ $purchaseOrder->supplier->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title text-success"><i class="fas fa-dollar-sign me-2"></i>Total Value</h5>
                        <p class="card-text fs-4 fw-bold">
                            {{ $currencySymbol }}{{ number_format($purchaseOrder->total_price, 2) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title text-info"><i class="fas fa-check-circle me-2"></i>Status</h5>
                        <p class="card-text">
                            <span class="badge bg-info fs-6">{{ $purchaseOrder->status->name ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Order Details
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Feed Type:</dt>
                            <dd class="col-sm-8">
                                <a href="{{ route('feed-types.show', $purchaseOrder->feed_type_id) }}">
                                    {{ $purchaseOrder->feedType->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                                </a>
                            </dd>

                            <dt class="col-sm-4">Purchase Unit:</dt>
                            <dd class="col-sm-8">
                                <a href="{{ route('purchase-units.show', $purchaseOrder->purchase_unit_id) }}">
                                    {{ $purchaseOrder->purchaseUnit->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                                </a>
                            </dd>

                            <dt class="col-sm-4">Quantity:</dt>
                            <dd class="col-sm-8">{{ $purchaseOrder->quantity ?? 'N/A' }} {{ $purchaseOrder->purchaseUnit->name ?? '' }}</dd>

                            <dt class="col-sm-4">Unit Price:</dt>
                            <dd class="col-sm-8">{{ $currencySymbol }}{{ number_format($purchaseOrder->unit_price, 2) }} / {{ $purchaseOrder->purchaseUnit->name ?? 'Unit' }}</dd>

                        </dl>
                    </div>
                    {{-- Right Column --}}
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Order Date:</dt>
                            <dd class="col-sm-8">{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : 'N/A' }}</dd>

                            <dt class="col-sm-4">Expected Delivery:</dt>
                            <dd class="col-sm-8">{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : 'N/A' }}</dd>

                            <dt class="col-sm-4">Actual Delivery:</dt>
                            <dd class="col-sm-8">{{ $purchaseOrder->actual_delivery_date ? $purchaseOrder->actual_delivery_date->format('Y-m-d') : 'N/A' }}</dd>

                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8">
                                <a href="{{ route('purchase-order-statuses.show', $purchaseOrder->purchase_order_status_id) }}">
                                    {{ $purchaseOrder->status->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                                </a>
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Notes Section --}}
                <hr>
                <h5><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                <p>{{ $purchaseOrder->notes ?? 'No notes provided.' }}</p>
                <hr>
                <small class="text-muted">
                    Created At: {{ $purchaseOrder->created_at->format('Y-m-d H:i:s') }} |
                    Last Updated: {{ $purchaseOrder->updated_at->format('Y-m-d H:i:s') }}
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
                    Are you sure you want to delete Purchase Order <strong>{{ $purchaseOrder->purchase_order_no }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Purchase Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
