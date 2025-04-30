@extends('layouts.app')

@section('title', 'Purchase Order Status Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-check-circle"></i> PO Status Details: {{ $purchaseOrderStatus->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('purchase-order-statuses.edit', $purchaseOrderStatus->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('purchase-order-statuses.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $purchaseOrderStatus->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $purchaseOrderStatus->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $purchaseOrderStatus->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $purchaseOrderStatus->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $purchaseOrderStatus->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related Purchase Orders --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-shopping-cart"></i> Recent Purchase Orders with this Status
            </div>
            <div class="card-body">
                {{-- Eager load purchaseOrders relationship in controller: $purchaseOrderStatus->load('purchaseOrders') --}}
                @if($purchaseOrderStatus->purchaseOrders && $purchaseOrderStatus->purchaseOrders->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($purchaseOrderStatus->purchaseOrders->take(10) as $po) {{-- Show last 10 --}}
                        <li class="list-group-item">
                            <a href="{{ route('purchase-orders.show', $po->id) }}">
                                PO #{{ $po->purchase_order_no }}
                            </a>
                            <small class="text-muted ms-2">
                                (Supplier: {{ $po->supplier->name ?? 'N/A' }} | Order Date: {{ $po->order_date->format('Y-m-d') }})
                            </small>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('purchase-orders.index') }}?purchase_order_status_id={{ $purchaseOrderStatus->id }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-list"></i> View All Purchase Orders with this Status
                    </a>
                @else
                    <p class="text-muted">No purchase orders currently have this status.</p>
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
                    Are you sure you want to delete the Status: <strong>{{ $purchaseOrderStatus->name }}</strong>? This might affect related purchase orders. Consider making it inactive instead if it has been used. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('purchase-order-statuses.destroy', $purchaseOrderStatus->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
