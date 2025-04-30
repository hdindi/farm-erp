@extends('layouts.app')

@section('title', 'Add Feed Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Feed Record</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('feed-records.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Feed Record Details
            </div>
            <div class="card-body">
                <form action="{{ route('feed-records.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass necessary collections from controller --}}
                    @include('feed-records._form', [
                        'dailyRecords' => $dailyRecords,
                        'feedTypes' => $feedTypes
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
