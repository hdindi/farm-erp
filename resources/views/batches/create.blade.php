@extends('layouts.app')

@section('title', 'Add New Batch')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Batch</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Batch Details
            </div>
            <div class="card-body">
                <form action="{{ route('batches.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass the necessary variables ($birdTypes, $breeds) --}}
                    @include('batches._form', ['birdTypes' => $birdTypes, 'breeds' => $breeds])

                </form>
            </div>
        </div>
    </div>
@endsection
