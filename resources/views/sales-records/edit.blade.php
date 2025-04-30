@extends('layouts.app')

@section('title', 'Edit Sales Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                {{-- More descriptive title --}}
                <h1>
                    <i class="fas fa-edit"></i> Edit Sale Record #{{ $salesRecord->id }}
                    <small class="text-muted"> ({{ $salesRecord->sale_date->format('Y-m-d') }})</small>
                </h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-records.show', $salesRecord->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Record
                </a>
                <a href="{{ route('sales-records.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to Sales List
                </a>
            </div>
        </div>

        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                Update Sale Details
            </div>
            <div class="card-body">
                <form action="{{ route('sales-records.update', $salesRecord->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object and dropdown data --}}
                    @include('sales-records._form', [
                        'salesRecord' => $salesRecord,
                        'salesPeople' => $salesPeople,
                        'salesPrices' => $salesPrices
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
