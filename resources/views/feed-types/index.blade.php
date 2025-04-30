@extends('layouts.app')

@section('title', 'Feed Types')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-wheat-awn"></i> Feed Types</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('feed-types.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Feed Type
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Feed Type List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="feed-types-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($feedTypes as $feedType) {{-- Passed from FeedTypeController::index --}}
                        <tr>
                            <td>{{ $feedType->id }}</td>
                            <td>{{ $feedType->name }}</td>
                            <td>{{ Str::limit($feedType->description, 60) }}</td> {{-- Limit description length --}}
                            <td>
                                <div class="btn-group" role="group" aria-label="Feed Type Actions">
                                    <a href="{{ route('feed-types.show', $feedType->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('feed-types.edit', $feedType->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $feedType->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                {{-- Delete Confirmation Modal (Unique ID per row) --}}
                                <div class="modal fade" id="deleteModal_{{ $feedType->id }}" tabindex="-1" aria-labelledby="deleteModalLabel_{{ $feedType->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger" id="deleteModalLabel_{{ $feedType->id }}"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the Feed Type: <strong>{{ $feedType->name }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('feed-types.destroy', $feedType->id) }}" method="POST" class="d-inline">
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $feedTypes->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#feed-types-table').DataTable({
                responsive: true,
                // Add Buttons extension configuration
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
                    "targets": [3], // Target the "Actions" column (index 3)
                    "orderable": false,
                    "searchable": false
                } ]
            });
        });
    </script>
@endpush
