@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Create New Purchase Order</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Purchase Order Details
            </div>
            <div class="card-body">
                <form action="{{ route('purchase-orders.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass the necessary collections for dropdowns --}}
                    @include('purchase-orders._form', [
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
