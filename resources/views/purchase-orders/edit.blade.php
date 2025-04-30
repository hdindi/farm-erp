@extends('layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Purchase Order: {{ $purchaseOrder->purchase_order_no }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View PO
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Purchase Order Details
            </div>
            <div class="card-body">
                <form action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('purchase-orders._form', [
                        'purchaseOrder' => $purchaseOrder,
                        'suppliers' => $suppliers,
                        'feedTypes' => $feedTypes,
                        'purchaseUnits' => $purchaseUnits,
                        'statuses' => $statuses
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
