@extends('layouts.app')

@section('title', 'Add New Sales Unit')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Sales Unit</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-units.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Sales Unit Details
            </div>
            <div class="card-body">
                <form action="{{ route('sales-units.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    @include('sales-units._form')

                </form>
            </div>
        </div>
    </div>
@endsection
