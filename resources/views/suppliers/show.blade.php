@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-truck"></i> Supplier Details: {{ $supplier->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $supplier->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $supplier->name }}</dd>

                    <dt class="col-sm-3">Contact Person</dt>
                    <dd class="col-sm-9">{{ $supplier->contact_person ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Phone Number</dt>
                    <dd class="col-sm-9">{{ $supplier->phone_no ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Email Address</dt>
                    <dd class="col-sm-9">{{ $supplier->email ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Address</dt>
                    <dd class="col-sm-9">{{ $supplier->address ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Description / Notes</dt>
                    <dd class="col-sm-9">{{ $supplier->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($supplier->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $supplier->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $supplier->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Sections for related records --}}
        <div class="row">
            {{-- Related Purchase Orders --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-shopping-cart"></i> Recent Purchase Orders from this Supplier
                    </div>
                    <div class="card-body">
                        {{-- Eager load purchaseOrders relationship in controller if needed --}}
                        @if($supplier->purchaseOrders && $supplier->purchaseOrders->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($supplier->purchaseOrders->take(5) as $po)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}">
                                            PO #{{ $po->purchase_order_no }}
                                            <small class="text-muted ms-2">({{ $po->order_date->format('Y-m-d') }})</small>
                                        </a>
                                        <span class="badge bg-info rounded-pill">{{ $po->status->name ?? 'N/A' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('purchase-orders.index') }}?supplier_id={{ $supplier->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Purchase Orders
                            </a>
                        @else
                            <p class="text-muted">No purchase orders found for this supplier.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Supplier Feed Prices --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-tags"></i> Feed Prices from this Supplier
                    </div>
                    <div class="card-body">
                        {{-- Eager load supplierFeedPrices relationship in controller if needed --}}
                        @if($supplier->supplierFeedPrices && $supplier->supplierFeedPrices->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($supplier->supplierFeedPrices->take(5) as $price)
                                    <li class="list-group-item">
                                        <a href="{{ route('supplier-feed-prices.show', $price->id) }}">
                                            {{ $price->feedType->name ?? 'N/A' }}
                                        </a>
                                        <small class="text-muted ms-2">(Effective: {{ $price->effective_date->format('Y-m-d') }})</small>
                                        - Price: {{ number_format($price->supplier_price, 2) }} / {{ $price->purchaseUnit->name ?? 'Unit' }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('supplier-feed-prices.index') }}?supplier_id={{ $supplier->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Supplier Prices
                            </a>
                        @else
                            <p class="text-muted">No supplier feed prices found for this supplier.</p>
                            <a href="{{ route('supplier-feed-prices.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-sm btn-success mt-2">
                                <i class="fas fa-plus"></i> Add Feed Price
                            </a>
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
                    Are you sure you want to delete the Supplier: <strong>{{ $supplier->name }}</strong>? This might affect related purchase orders and supplier prices. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Supplier
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
