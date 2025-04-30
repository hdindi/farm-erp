@extends('layouts.app')

@section('title', 'Add New Bird Type')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Bird Type</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('bird-types.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Bird Type Details
            </div>
            <div class="card-body">
                <form action="{{ route('bird-types.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    @include('bird-types._form')

                </form>
            </div>
        </div>
    </div>
@endsection
