@extends('layouts.app')

@section('title', 'Edit Sales Unit')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Sales Unit: {{ $salesUnit->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-units.show', $salesUnit->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Unit
                </a>
                <a href="{{ route('sales-units.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Sales Unit Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'salesUnit' --}}
                <form action="{{ route('sales-units.update', $salesUnit->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object --}}
                    @include('sales-units._form', ['salesUnit' => $salesUnit])

                </form>
            </div>
        </div>
    </div>
@endsection
