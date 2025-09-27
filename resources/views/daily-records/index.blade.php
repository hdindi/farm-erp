@extends('layouts.app')

@section('title', 'Daily Records')

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

        @include('partials.alerts')

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
                        @foreach ($dailyRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->record_date->format('Y-m-d') }}</td>
                                <td>{{ $record->batch->batch_code ?? 'N/A' }}</td>
                                <td>{{ $record->stage->name ?? 'N/A' }}</td>
                                <td class="numeric">{{ $record->day_in_stage }}</td>
                                <td class="numeric">{{ number_format($record->alive_count) }}</td>
                                <td class="numeric">{{ number_format($record->dead_count) }}</td>
                                <td class="numeric">{{ number_format($record->culls_count) }}</td>
                                <td class="numeric">{{ $record->average_weight_grams }}</td>
                                <td class="numeric">{{ number_format($record->mortality_rate, 2) }}</td>
                                <td>
                                    {{-- Actions: View, Edit, Delete Modals --}}
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
                                    <div class="modal fade" id="deleteModal_{{ $record->id }}" tabindex="-1" aria-labelledby="deleteModalLabel_{{ $record->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger" id="deleteModalLabel_{{ $record->id }}"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete Daily Record <strong>#{{ $record->id }}</strong>? This action cannot be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('daily-records.destroy', $record->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
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

                {{-- Removed manual pagination - DataTables handles it --}}
                {{--
                <div class="d-flex justify-content-center mt-4">
                    {{ $dailyRecords->links('pagination::bootstrap-5') }}
                </div>
                 --}}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Add spacing for DataTables controls */
        .dataTables_wrapper .row:first-child {
            margin-bottom: 1rem;
        }
        .dt-buttons .btn {
            margin-right: 0.5rem;
        }
        .numeric { 
            text-align: right; 
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Debug: Check table structure before initializing DataTables
            var table = $('#daily-records-table');
            var headerCols = table.find('thead tr:first th').length;
            var firstRowCols = table.find('tbody tr:first td').length;
            console.log('Header columns:', headerCols, 'First row columns:', firstRowCols);
            
            if (headerCols !== firstRowCols && table.find('tbody tr').length > 0) {
                console.error('Column count mismatch! Header:', headerCols, 'Row:', firstRowCols);
            }
            
            try {
                $('#daily-records-table').DataTable({
                    responsive: true,
                    dom: 'Bfrtip', // B=Buttons, f=filtering, r=processing, t=table, i=info, p=pagination
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            text: '<i class="fas fa-copy"></i> Copy',
                            titleAttr: 'Copy to clipboard',
                            className: 'btn btn-secondary'
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            titleAttr: 'Export to Excel',
                            className: 'btn btn-success'
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="fas fa-file-csv"></i> CSV',
                            titleAttr: 'Export to CSV',
                            className: 'btn btn-info'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            titleAttr: 'Export to PDF',
                            className: 'btn btn-danger'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            titleAttr: 'Print table',
                            className: 'btn btn-warning'
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="fas fa-eye-slash"></i> Column Visibility',
                            titleAttr: 'Show/hide columns',
                            className: 'btn btn-light'
                        }
                    ],
                    // Set default order by Record Date (column index 1) descending
                    order: [[ 1, 'desc' ]],
                    // Disable ordering and searching on the 'Actions' column
                    "columnDefs": [ {
                        "targets": [10], // Target the "Actions" column (index 10)
                        "orderable": false, // Disable sorting
                        "searchable": false // Disable searching
                    } ]
                });
                console.log('DataTables initialized successfully');
            } catch (error) {
                console.error('DataTables initialization error:', error);
            }
        });
    </script>
@endpush
