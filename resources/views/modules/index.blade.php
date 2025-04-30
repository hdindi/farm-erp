@extends('layouts.app')

@section('title', 'Modules')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-puzzle-piece"></i> Application Modules</h1>
            </div>
            {{-- Add button only if modules are dynamic --}}
            {{-- <div class="col-auto">
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Module
                </a>
            </div> --}}
        </div>

        <div class="card">
            <div class="card-header">
                Modules List
            </div>
            <div class="card-body">
                @if($modules->isEmpty())
                    <p class="text-muted">No modules found in the database.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($modules as $module)
                                <tr>
                                    <td>{{ $module->id }}</td>
                                    <td>{{ $module->name }}</td>
                                    <td>{{ $module->description ?? '-' }}</td>
                                    <td>
                                        @if($module->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
