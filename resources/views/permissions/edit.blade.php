@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-edit"></i> Edit Permission: {{ $permission->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-info me-1" title="View Permission">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to Permissions List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Update Permission Details
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- Include the shared form partial, passing the existing object --}}
                    @include('permissions._form', ['permission' => $permission])
                </form>
            </div>
        </div>

        {{-- Optional: Show Modules this permission is linked to --}}
        {{-- You would need to load the 'modules' relationship in the controller --}}
        {{--
        <div class="card mt-4">
            <div class="card-header"><i class="fas fa-puzzle-piece"></i> Used In Modules</div>
            <div class="card-body">
                 @if($permission->modules->isNotEmpty())
                     <ul class="list-inline">
                         @foreach($permission->modules as $module)
                             <li class="list-inline-item"><span class="badge bg-secondary">{{ $module->name }}</span></li>
                         @endforeach
                     </ul>
                 @else
                    <p class="text-muted">This permission is not currently assigned to any modules.</p>
                 @endif
            </div>
        </div>
         --}}


    </div>
@endsection
