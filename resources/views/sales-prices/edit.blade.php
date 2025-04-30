@extends('layouts.app')

@section('title', 'Edit Sales Price')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                {{-- More descriptive title --}}
                <h1>
                    <i class="fas fa-edit"></i> Edit Price for {{ ucfirst($salesPrice->item_type) }}
                    @if($salesPrice->item_type === 'bird' && $salesPrice->batch)
                        (Batch: {{ $salesPrice->batch->batch_code }})
                    @endif
                    / {{ $salesPrice->salesUnit->name ?? 'Unit' }}
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-prices.show', $salesPrice->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Price
                </a>
                <a href="{{ route('sales-prices.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                Update Sales Price Details
            </div>
            <div class="card-body">
                <form action="{{ route('sales-prices.update', $salesPrice->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('sales-prices._form', [
                        'salesPrice' => $salesPrice,
                        'salesUnits' => $salesUnits,
                        'batches' => $batches
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
