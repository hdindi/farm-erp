@extends('layouts.app')

@section('title', 'Add Egg Production Record')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Egg Production Record</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('egg-production.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        @if($dailyRecords->isEmpty())
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle"></i> No active 'Layer' batches older than 18 weeks found with daily records. Please add relevant <a href="{{route('daily-records.create')}}">daily records</a> first.
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    Egg Collection Details
                </div>
                <div class="card-body">
                    {{-- Add id="egg-production-form" if using JS submit prevention --}}
                    <form action="{{ route('egg-production.store') }}" method="POST" id="egg-production-form">
                        @csrf

                        {{-- Include the shared form partial --}}
                        {{-- Pass necessary collections from controller --}}
                        @include('egg-production._form', [
                            'dailyRecords' => $dailyRecords
                        ])

                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
