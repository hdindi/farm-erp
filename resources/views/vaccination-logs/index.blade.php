@extends('layouts.app')

@section('title', 'Vaccination Logs')

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
                <h1><i class="fas fa-notes-medical"></i> Vaccination Logs</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccination-logs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Log Entry
                </a>
            </div>
        </div>

        {{-- Filtering Section (Optional) --}}
        {{-- Consider filters for Batch, Vaccine, Date Range --}}

        <div class="card">
            <div class="card-header">
                Vaccination Log List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="vaccination-logs-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Log ID</th>
                            <th>Record Date</th>
                            <th>Batch Code</th>
                            <th>Vaccine</th>
                            <th>Birds Vaccinated</th>
                            <th>Administered By</th>
                            <th>Next Due</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $vaccinationLogs passed from Controller (with eager loaded relations) --}}
                        @foreach($vaccinationLogs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }}</td>
                                <td>{{ $log->dailyRecord->batch->batch_code ?? 'N/A' }}</td>
                                <td>{{ $log->vaccine->name ?? 'N/A' }}</td>
                                <td>{{ $log->birds_vaccinated }}</td>
                                <td>{{ $log->administered_by ?? '-' }}</td>
                                <td>{{ $log->next_due_date ? $log->next_due_date->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Log Actions">
                                        <a href="{{ route('vaccination-logs.show', $log->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vaccination-logs.edit', $log->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $log->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Confirmation Modal (Unique ID per row) --}}
                                    <div class="modal fade" id="deleteModal_{{ $log->id }}" tabindex="-1" aria-labelledby="deleteModalLabel_{{ $log->id }}" aria-hidden="true">
                                        {{-- Modal Content Here --}}
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger" id="deleteModalLabel_{{ $log->id }}"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete Vaccination Log <strong>#{{ $log->id }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('vaccination-logs.destroy', $log->id) }}" method="POST" class="d-inline">
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
                    {{ $vaccinationLogs->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#vaccination-logs-table').DataTable({
                responsive: true,
                // Add Buttons extension configuration
                dom: 'Bfrtip', // Layout: Buttons, filtering, processing, table, info, pagination
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape' }, // Landscape PDF
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [ {
                    "targets": [7], // Target the "Actions" column (index 7)
                    "orderable": false,
                    "searchable": false
                } ],
                // Default order by Record Date descending
                order: [[ 1, 'desc' ]] // Order by Record Date column (index 1) descending
            });
        });
    </script>
@endpush
