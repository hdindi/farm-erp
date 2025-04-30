@extends('layouts.app')

@section('title', 'Add Supplier Feed Price')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Supplier Feed Price</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('supplier-feed-prices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Price List
                </a>
            </div>
        </div>

        <div class="card border-success"> {{-- Added border color --}}
            <div class="card-header bg-success text-white"> {{-- Added background color --}}
                Enter New Feed Price Details
            </div>
            <div class="card-body">
                <form action="{{ route('supplier-feed-prices.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass the necessary collections for dropdowns --}}
                    @include('supplier-feed-prices._form', [
                        'suppliers' => $suppliers,
                        'feedTypes' => $feedTypes,
                        'purchaseUnits' => $purchaseUnits
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
