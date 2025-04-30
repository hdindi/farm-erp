@extends('layouts.app')

@section('title', 'Purchase Unit Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-weight-scale"></i> Purchase Unit Details: {{ $purchaseUnit->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('purchase-units.edit', $purchaseUnit->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('purchase-units.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $purchaseUnit->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $purchaseUnit->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $purchaseUnit->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $purchaseUnit->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $purchaseUnit->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Sections for related records --}}
        <div class="row">
            {{-- Related Purchase Orders --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-shopping-cart"></i> Recent Purchase Orders using this Unit
                    </div>
                    <div class="card-body">
                        {{-- Eager load purchaseOrders relationship in controller if needed --}}
                        @if($purchaseUnit->purchaseOrders && $purchaseUnit->purchaseOrders->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($purchaseUnit->purchaseOrders->take(5) as $po)
                                    <li class="list-group-item">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}">
                                            PO #{{ $po->purchase_order_no }}
                                        </a>
                                        <small class="text-muted ms-2">({{ $po->order_date->format('Y-m-d') }})</small>
                                        - {{ $po->quantity }} {{ $purchaseUnit->name }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('purchase-orders.index') }}?purchase_unit_id={{ $purchaseUnit->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Purchase Orders
                            </a>
                        @else
                            <p class="text-muted">No purchase orders found using this unit.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Supplier Feed Prices --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-tags"></i> Recent Supplier Prices using this Unit
                    </div>
                    <div class="card-body">
                        {{-- Eager load supplierFeedPrices relationship in controller if needed --}}
                        @if($purchaseUnit->supplierFeedPrices && $purchaseUnit->supplierFeedPrices->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($purchaseUnit->supplierFeedPrices->take(5) as $price)
                                    <li class="list-group-item">
                                        <a href="{{ route('supplier-feed-prices.show', $price->id) }}">
                                            {{ $price->supplier->name ?? 'N/A' }} - {{ $price->feedType->name ?? 'N/A' }}
                                        </a>
                                        <small class="text-muted ms-2">(Effective: {{ $price->effective_date->format('Y-m-d') }})</small>
                                        - Price: {{ number_format($price->supplier_price, 2) }} / {{ $purchaseUnit->name }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('supplier-feed-prices.index') }}?purchase_unit_id={{ $purchaseUnit->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Supplier Prices
                            </a>
                        @else
                            <p class="text-muted">No supplier feed prices found using this unit.</p>
                        @endif
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
                    Are you sure you want to delete the Purchase Unit: <strong>{{ $purchaseUnit->name }}</strong>? This might affect related purchase orders and supplier prices. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('purchase-units.destroy', $purchaseUnit->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Unit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
