@extends('layouts.app')

@section('title', 'Add Vaccine Schedule Item')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Vaccine Schedule Item</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccine-schedule.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Schedule List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Schedule Details
            </div>
            <div class="card-body">
                <form action="{{ route('vaccine-schedule.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    {{-- Pass necessary collections from controller --}}
                    @include('vaccine-schedule._form', [
                        'batches' => $batches,
                        'vaccines' => $vaccines,
                        'vaccinationLogs' => $vaccinationLogs ?? [] // Pass empty array if not always needed
                    ])

                </form>
            </div>
        </div>
    </div>
@endsection
