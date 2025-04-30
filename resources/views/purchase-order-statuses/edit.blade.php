@extends('layouts.app')

@section('title', 'Edit Purchase Order Status')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit PO Status: {{ $purchaseOrderStatus->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('purchase-order-statuses.show', $purchaseOrderStatus->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Status
                </a>
                <a href="{{ route('purchase-order-statuses.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update PO Status Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'purchaseOrderStatus' --}}
                <form action="{{ route('purchase-order-statuses.update', $purchaseOrderStatus->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object --}}
                    @include('purchase-order-statuses._form', ['purchaseOrderStatus' => $purchaseOrderStatus])

                </form>
            </div>
        </div>
    </div>
@endsection
