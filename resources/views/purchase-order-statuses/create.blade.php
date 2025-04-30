@extends('layouts.app')

@section('title', 'Add New Purchase Order Status')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New PO Status</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('purchase-order-statuses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Purchase Order Status Details
            </div>
            <div class="card-body">
                <form action="{{ route('purchase-order-statuses.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    @include('purchase-order-statuses._form')

                </form>
            </div>
        </div>
    </div>
@endsection
