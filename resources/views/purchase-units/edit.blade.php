@extends('layouts.app')

@section('title', 'Edit Purchase Unit')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Purchase Unit: {{ $purchaseUnit->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('purchase-units.show', $purchaseUnit->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Unit
                </a>
                <a href="{{ route('purchase-units.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Purchase Unit Details
            </div>
            <div class="card-body">
                <form action="{{ route('purchase-units.update', $purchaseUnit->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing $purchaseUnit object --}}
                    @include('purchase-units._form', ['purchaseUnit' => $purchaseUnit])

                </form>
            </div>
        </div>
    </div>
@endsection
