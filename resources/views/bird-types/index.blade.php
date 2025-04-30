@extends('layouts.app')

@section('title', 'Bird Types')

@push('styles')
    {{-- Add specific styles if needed --}}
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-dove"></i> Bird Types</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('bird-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Bird Type
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Bird Type List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="bird-types-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Egg Cycle (Days)</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- Data will be populated by DataTables if using server-side processing,
                             or loop through $birdTypes if using client-side processing.
                             Assuming client-side for simplicity based on controller code. --}}
                        @foreach($birdTypes as $birdType)
                            <tr>
                                <td>{{ $birdType->id }}</td>
                                <td>{{ $birdType->name }}</td>
                                <td>{{ Str::limit($birdType->description, 50) }}</td> {{-- Limit description length --}}
                                <td>{{ $birdType->egg_production_cycle ?? '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Bird Type Actions">
                                        <a href="{{ route('bird-types.show', $birdType->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('bird-types.edit', $birdType->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Using Modal Trigger for Delete --}}
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $birdType->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Confirmation Modal (Unique ID per row) --}}
                                    <div class="modal fade" id="deleteModal_{{ $birdType->id }}" tabindex="-1" aria-labelledby="deleteModalLabel_{{ $birdType->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger" id="deleteModalLabel_{{ $birdType->id }}"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the Bird Type: <strong>{{ $birdType->name }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('bird-types.destroy', $birdType->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- If using client-side DataTables, remove manual pagination --}}
                {{-- If using server-side DataTables, pagination links might be handled differently or removed --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $birdTypes->links() }} {{-- Keep if NOT using DataTables or using server-side with manual links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#bird-types-table').DataTable({
                responsive: true,
                // Add Buttons extension configuration (optional)
                dom: 'Bfrtip', // Layout: Buttons, filtering, processing, table, info, pagination
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [ {
                    "targets": [4], // Target the "Actions" column (index 4)
                    "orderable": false,
                    "searchable": false
                } ]
            });
        });
    </script>
@endpush
