@extends('layouts.app')

@section('title', 'Record New Sale')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Record New Sale</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-records.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Sales List
                </a>
            </div>
        </div>

        <div class="card border-success">
            <div class="card-header bg-success text-white">
                Sale Details
            </div>
            <div class="card-body">
                <form action="{{ route('sales-records.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass necessary collections --}}
                    @include('sales-records._form', [
                        'salesPeople' => $salesPeople,
                        'salesPrices' => $salesPrices
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
