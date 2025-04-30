@extends('layouts.app')

@section('title', 'Disease Management Records')

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
                <h1><i class="fas fa-notes-medical"></i> Disease Management Records</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('disease-management.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Record
                </a>
            </div>
        </div>

        {{-- Filtering Section (Optional) --}}
        {{-- Consider filters for Batch, Disease, Drug, Date Range --}}

        <div class="card">
            <div class="card-header">
                Disease Management List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="disease-management-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Observed On</th>
                            <th>Batch</th>
                            <th>Disease</th>
                            <th>Drug Used</th>
                            <th>Affected #</th>
                            <th>Treatment Start</th>
                            <th>Treatment End</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $diseaseManagements passed from Controller (with eager loaded relations) --}}
                        @foreach($diseaseManagements as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->observation_date ? $record->observation_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $record->batch->batch_code ?? 'N/A' }}</td>
                                <td>{{ $record->disease->name ?? 'N/A' }}</td>
                                <td>{{ $record->drug->name ?? '-' }}</td>
                                <td>{{ $record->affected_count ?? '-' }}</td>
                                <td>{{ $record->treatment_start_date ? $record->treatment_start_date->format('Y-m-d') : '-' }}</td>
                                <td>{{ $record->treatment_end_date ? $record->treatment_end_date->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Record Actions">
                                        <a href="{{ route('disease-management.show', $record->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('disease-management.edit', $record->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $record->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Confirmation Modal --}}
                                    <div class="modal fade" id="deleteModal_{{ $record->id }}" tabindex="-1"> {{-- Content Here --}}
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete Disease Management Record <strong>#{{ $record->id }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('disease-management.destroy', $record->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
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
                    {{ $diseaseManagements->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#disease-management-table').DataTable({
                responsive: true,
                // Add Buttons extension configuration
                dom: 'Bfrtip', // Layout: Buttons, filtering, processing, table, info, pagination
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [ {
                    "targets": [8], // Target the "Actions" column (index 8)
                    "orderable": false,
                    "searchable": false
                } ],
                // Default order by Observation Date descending
                order: [[ 1, 'desc' ]] // Order by Observation Date column (index 1) descending
            });
        });
    </script>
@endpush
