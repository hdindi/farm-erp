@extends('layouts.app')

@section('title', 'Bird Type Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-dove"></i> Bird Type Details: {{ $birdType->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('bird-types.edit', $birdType->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('bird-types.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $birdType->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $birdType->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $birdType->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Avg. Egg Production Cycle (Days)</dt>
                    <dd class="col-sm-9">{{ $birdType->egg_production_cycle ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $birdType->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $birdType->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Add a section for related Batches --}}
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-kiwi-bird"></i> Related Batches
            </div>
            <div class="card-body">
                @if($birdType->batches && $birdType->batches->count() > 0)
                    <ul class="list-group">
                        @foreach($birdType->batches as $batch)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('batches.show', $batch->id) }}">{{ $batch->batch_code }}</a>
                                <span class="badge bg-{{ $batch->status == 'active' ? 'success' : ($batch->status == 'completed' ? 'info' : 'danger') }} rounded-pill">
                                    {{ ucfirst($batch->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No batches found for this bird type.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the Bird Type: <strong>{{ $birdType->name }}</strong>? This might affect related batches. This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('bird-types.destroy', $birdType->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Bird Type
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
