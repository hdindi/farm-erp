@extends('layouts.app')

@section('title', 'Supplier Feed Price Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                {{-- Showing more context in title --}}
                <h1>
                    <i class="fas fa-tag"></i> Price Details:
                    <span class="text-primary">{{ $supplierFeedPrice->feedType->name ?? 'N/A' }}</span> from
                    <span class="text-success">{{ $supplierFeedPrice->supplier->name ?? 'N/A' }}</span>
                </h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('supplier-feed-prices.edit', $supplierFeedPrice->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('supplier-feed-prices.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Price Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $supplierFeedPrice->id }}</dd>

                    <dt class="col-sm-3">Supplier</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('suppliers.show', $supplierFeedPrice->supplier_id) }}">
                            {{ $supplierFeedPrice->supplier->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Feed Type</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('feed-types.show', $supplierFeedPrice->feed_type_id) }}">
                            {{ $supplierFeedPrice->feedType->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Purchase Unit</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('purchase-units.show', $supplierFeedPrice->purchase_unit_id) }}">
                            {{ $supplierFeedPrice->purchaseUnit->name ?? 'N/A' }} <i class="fas fa-external-link-alt fa-xs"></i>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Supplier Price</dt>
                    <dd class="col-sm-9 fw-bold fs-5 text-success">
                        {{ config('app.currency_symbol', '$') }}{{ number_format($supplierFeedPrice->supplier_price, 2) }}
                        / {{ $supplierFeedPrice->purchaseUnit->name ?? 'Unit' }}
                    </dd>

                    <dt class="col-sm-3">Effective Date</dt>
                    <dd class="col-sm-9">{{ $supplierFeedPrice->effective_date ? $supplierFeedPrice->effective_date->format('Y-m-d') : 'N/A' }}</dd>

                    <dt class="col-sm-3">Description / Notes</dt>
                    <dd class="col-sm-9">{{ $supplierFeedPrice->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Recorded At</dt>
                    <dd class="col-sm-9">{{ $supplierFeedPrice->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Last Updated</dt>
                    <dd class="col-sm-9">{{ $supplierFeedPrice->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related purchase orders using this price (more complex logic needed) --}}
        {{-- You might need to query Purchase Orders based on supplier, feed type, unit, and date range --}}

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
                    Are you sure you want to delete this price record (ID: <strong>{{ $supplierFeedPrice->id }}</strong>)? This action cannot be undone.
                    <p class="mt-2">
                        Supplier: <strong>{{ $supplierFeedPrice->supplier->name ?? 'N/A' }}</strong><br>
                        Feed Type: <strong>{{ $supplierFeedPrice->feedType->name ?? 'N/A' }}</strong><br>
                        Price: <strong>{{ config('app.currency_symbol', '$') }}{{ number_format($supplierFeedPrice->supplier_price, 2) }} / {{ $supplierFeedPrice->purchaseUnit->name ?? 'Unit' }}</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('supplier-feed-prices.destroy', $supplierFeedPrice->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Price Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
