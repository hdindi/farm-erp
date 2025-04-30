@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Supplier: {{ $supplier->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Supplier
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Supplier Details
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing $supplier object --}}
                    @include('suppliers._form', ['supplier' => $supplier])

                </form>
            </div>
        </div>
    </div>
@endsection
