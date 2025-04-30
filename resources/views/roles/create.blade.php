@extends('layouts.app')

@section('title', 'Add New Role')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1><i class="fas fa-plus-circle"></i> Add New Role</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Roles List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Role Details
            </div>
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    @include('roles._form')
                </form>
            </div>
        </div>
    </div>
@endsection
