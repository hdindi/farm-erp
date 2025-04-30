@extends('layouts.app')

@section('title', 'Add Vaccination Log')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Vaccination Log</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccination-logs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Vaccination Log Details
            </div>
            <div class="card-body">
                <form action="{{ route('vaccination-logs.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass necessary collections from controller --}}
                    @include('vaccination-logs._form', [
                        'dailyRecords' => $dailyRecords,
                        'vaccines' => $vaccines
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
