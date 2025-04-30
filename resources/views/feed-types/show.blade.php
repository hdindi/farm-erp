@extends('layouts.app')

@section('title', 'Feed Type Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-wheat-awn"></i> Feed Type Details: {{ $feedType->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('feed-types.edit', $feedType->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('feed-types.index') }}" class="btn btn-secondary" title="Back to List">
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
                    <dd class="col-sm-9">{{ $feedType->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $feedType->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $feedType->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $feedType->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $feedType->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related records --}}
        <div class="row">
            {{-- Related Feed Records --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-utensils"></i> Recent Feed Records using this Type
                    </div>
                    <div class="card-body">
                        {{-- Eager load feedRecords relationship in controller if needed --}}
                        @if($feedType->feedRecords && $feedType->feedRecords->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($feedType->feedRecords->take(5) as $record)
                                    <li class="list-group-item">
                                        <a href="{{ route('feed-records.show', $record->id) }}">
                                            Record from {{ $record->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }}
                                            (Batch: {{ $record->dailyRecord->batch->batch_code ?? 'N/A' }})
                                            - {{ $record->quantity_kg }} kg
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('feed-records.index') }}?feed_type_id={{ $feedType->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Feed Records
                            </a>
                        @else
                            <p class="text-muted">No feed records found for this type.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Purchase Orders --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-shopping-cart"></i> Recent Purchase Orders for this Type
                    </div>
                    <div class="card-body">
                        {{-- Eager load purchaseOrders relationship in controller if needed --}}
                        @if($feedType->purchaseOrders && $feedType->purchaseOrders->count() > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($feedType->purchaseOrders->take(5) as $po)
                                    <li class="list-group-item">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}">
                                            PO #{{ $po->purchase_order_no }}
                                        </a>
                                        <small class="text-muted ms-2">({{ $po->order_date->format('Y-m-d') }})</small>
                                        - Status: {{ $po->status->name ?? 'N/A' }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('purchase-orders.index') }}?feed_type_id={{ $feedType->id }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-list"></i> View All Purchase Orders
                            </a>
                        @else
                            <p class="text-muted">No purchase orders found for this feed type.</p>
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
                    Are you sure you want to delete the Feed Type: <strong>{{ $feedType->name }}</strong>? This might affect related records (feed records, purchase orders, supplier prices). This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('feed-types.destroy', $feedType->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Feed Type
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
