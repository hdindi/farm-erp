@extends('layouts.app')

@section('title', 'Add New Drug')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Drug</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('drugs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Drug Details
            </div>
            <div class="card-body">
                <form action="{{ route('drugs.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    @include('drugs._form')

                </form>
            </div>
        </div>
    </div>
@endsection
