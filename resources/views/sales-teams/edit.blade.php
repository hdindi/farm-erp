@extends('layouts.app')

@section('title', 'Edit Sales Team Member')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-user-edit"></i> Edit Sales Team Member: {{ $salesTeam->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-teams.show', $salesTeam->id) }}" class="btn btn-info me-1" title="View">
                    <i class="fas fa-eye"></i> View Member
                </a>
                <a href="{{ route('sales-teams.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Member Details
            </div>
            <div class="card-body">
                {{-- Note: Route model binding uses 'salesTeam' --}}
                <form action="{{ route('sales-teams.update', $salesTeam->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Important for update action --}}

                    {{-- Include the shared form partial, passing the existing object --}}
                    @include('sales-teams._form', ['salesTeam' => $salesTeam])

                </form>
            </div>
        </div>
    </div>
@endsection
