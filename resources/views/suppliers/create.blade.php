@extends('layouts.app')

@section('title', 'Add New Supplier')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Supplier</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Supplier Details
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    @include('suppliers._form')

                </form>
            </div>
        </div>
    </div>
@endsection
