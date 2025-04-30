@extends('layouts.app')

@section('title', 'Daily Records')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #daily-records-table th.numeric,
        #daily-records-table td.numeric { text-align: right; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-clipboard-list"></i> Daily Records</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('daily-records.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Daily Record
                </a>
            </div>
        </div>

        {{-- Filtering Section (Optional) --}}
        {{-- Consider filters for Batch, Stage, Date Range --}}

        <div class="card">
            <div class="card-header">
                Daily Record List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="daily-records-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Record Date</th>
                            <th>Batch</th>
                            <th>Stage</th>
                            <th class="numeric">Day #</th>
                            <th class="numeric">Alive</th>
                            <th class="numeric">Dead</th>
                            <th class="numeric">Culls</th>
                            <th class="numeric">Avg Wt(g)</th>
                            <th class="numeric">Mortality(%)</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $dailyRecords passed from Controller (with eager loaded relations) --}}
                        @foreach($dailyRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->record_date ? $record->record_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $record->batch->batch_code ?? 'N/A' }}</td>
                                <td>{{ $record->stage->name ?? 'N/A' }}</td>
                                <td class="numeric">{{ $record->day_in_stage }}</td>
                                <td class="numeric">{{ $record->alive_count }}</td>
                                <td class="numeric">{{ $record->dead_count }}</td>
                                <td class="numeric">{{ $record->culls_count }}</td>
                                <td class="numeric">{{ $record->average_weight_grams ?? '-' }}</td>
                                <td class="numeric">{{ $record->mortality_rate !== null ? number_format($record->mortality_rate, 2) : '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Record Actions">
                                        <a href="{{ route('daily-records.show', $record->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('daily-records.edit', $record->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
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
                                                    Are you sure you want to delete Daily Record <strong>#{{ $record->id }}</strong>? Associated feed, egg, and vaccination records for this day will also be deleted.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('daily-records.destroy', $record->id) }}" method="POST" class="d-inline">
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
                    {{ $dailyRecords->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#daily-records-table').DataTable({
                responsive: true,
                // Add Buttons extension configuration
                dom: 'Bfrtip', // Layout: Buttons, filtering, processing, table, info, pagination
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape', pageSize: 'A4' }, // Landscape PDF
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [
                    { "targets": [4, 5, 6, 7, 8, 9], "className": "numeric" }, // Align numeric columns right
                    { "targets": [10], "orderable": false, "searchable": false } // Actions column
                ],
                // Default order by Record Date descending
                order: [[ 1, 'desc' ]] // Order by Record Date column (index 1) descending
            });
        });
    </script>
@endpush
