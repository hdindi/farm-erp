@extends('layouts.app')

@section('title', 'Sales Team Member Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-user"></i> Sales Team Member: {{ $salesTeam->name }}</h1>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="{{ route('sales-teams.edit', $salesTeam->id) }}" class="btn btn-primary me-1" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('sales-teams.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Information
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $salesTeam->id }}</dd>

                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $salesTeam->name }}</dd>

                    <dt class="col-sm-3">Phone Number</dt>
                    <dd class="col-sm-9">{{ $salesTeam->phone_no ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Email Address</dt>
                    <dd class="col-sm-9">{{ $salesTeam->email ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if($salesTeam->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created At</dt>
                    <dd class="col-sm-9">{{ $salesTeam->created_at->format('Y-m-d H:i:s') }}</dd>

                    <dt class="col-sm-3">Updated At</dt>
                    <dd class="col-sm-9">{{ $salesTeam->updated_at->format('Y-m-d H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        {{-- Optional: Section for related Sales Records --}}
        {{-- This depends on your relationship setup. If sales_records.sales_person_id relates to users table, this section might not apply directly to SalesTeam model. --}}
        {{-- If you intend sales_person_id to link here, adjust SalesRecord model relationship and uncomment/modify below. --}}
        {{--
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-receipt"></i> Recent Sales Records by this Member
            </div>
            <div class="card-body">
                 // Eager load salesRecords relationship in controller if defined
                @if($salesTeam->salesRecords && $salesTeam->salesRecords->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($salesTeam->salesRecords->take(10) as $record) // Show last 10
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('sales-records.show', $record->id) }}">
                                    Record from {{ $record->sale_date->format('Y-m-d') }}
                                </a>
                                <span>Amount: {{ number_format($record->total_amount, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                     <a href="{{ route('sales-records.index') }}?sales_person_id={{ $salesTeam->id }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-list"></i> View All Sales Records
                    </a>
                @else
                    <p class="text-muted">No sales records found directly linked to this team member record.</p>
                @endif
            </div>
        </div>
        --}}


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
                    Are you sure you want to delete the Sales Team Member: <strong>{{ $salesTeam->name }}</strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('sales-teams.destroy', $salesTeam->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Member
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
