@extends('layouts.app')

@section('title', 'Edit Supplier Feed Price')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                {{-- Showing more context in title --}}
                <h1><i class="fas fa-edit"></i> Edit Price for {{ $supplierFeedPrice->feedType->name ?? 'N/A' }} from {{ $supplierFeedPrice->supplier->name ?? 'N/A' }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('supplier-feed-prices.show', $supplierFeedPrice->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Price
                </a>
                <a href="{{ route('supplier-feed-prices.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to Price List
                </a>
            </div>
        </div>

        <div class="card border-primary"> {{-- Added border color --}}
            <div class="card-header bg-primary text-white"> {{-- Added background color --}}
                Update Feed Price Details
            </div>
            <div class="card-body">
                <form action="{{ route('supplier-feed-prices.update', $supplierFeedPrice->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('supplier-feed-prices._form', [
                        'supplierFeedPrice' => $supplierFeedPrice,
                        'suppliers' => $suppliers,
                        'feedTypes' => $feedTypes,
                        'purchaseUnits' => $purchaseUnits
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
