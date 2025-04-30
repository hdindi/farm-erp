@extends('layouts.app')

@section('title', 'Add Sales Team Member')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-user-plus"></i> Add New Sales Team Member</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-teams.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Sales Team Member Details
            </div>
            <div class="card-body">
                <form action="{{ route('sales-teams.store') }}" method="POST">
                    @csrf

                    {{-- Include the shared form partial --}}
                    @include('sales-teams._form')

                </form>
            </div>
        </div>
    </div>
@endsection
