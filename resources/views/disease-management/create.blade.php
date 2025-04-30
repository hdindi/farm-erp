@extends('layouts.app')

@section('title', 'Add Disease Management Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Disease Management Record</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('disease-management.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Record Details
            </div>
            <div class="card-body">
                <form action="{{ route('disease-management.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass necessary collections from controller --}}
                    @include('disease-management._form', [
                       'batches' => $batches,
                       'diseases' => $diseases,
                       'drugs' => $drugs
                   ])

                </form>
            </div>
        </div>
    </div>
@endsection
