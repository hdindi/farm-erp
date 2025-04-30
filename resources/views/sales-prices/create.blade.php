@extends('layouts.app')

@section('title', 'Add New Sales Price')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Sales Price</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-prices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card border-success">
            <div class="card-header bg-success text-white">
                Sales Price Details
            </div>
            <div class="card-body">
                <form action="{{ route('sales-prices.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass necessary collections for dropdowns --}}
                    @include('sales-prices._form', [
                        'salesUnits' => $salesUnits,
                        'batches' => $batches
                        // Pass $salesPrice as null or omit if _form checks with isset()
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
