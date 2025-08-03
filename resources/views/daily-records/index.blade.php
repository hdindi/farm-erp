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
                        @forelse ($dailyRecords as $record)
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
                                <td class="numeric">{{ number_format($record->mortality_percentage, 2) }}</td>
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
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No daily records found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ✅ CORRECTED BOOTSTRAP 5 PAGINATION --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $dailyRecords->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        .numeric { text-align: right; }
    </style>
@endpush

@push('scripts')
    {{-- DataTables JavaScript --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#daily-records-table').DataTable({
                responsive: true,
                // ✅ CORRECTED: 'p' (pagination) is removed from dom, and paging is set to false.
                paging: false,
                dom: 'Bfrti', // B=Buttons, f=filtering, r=processing, t=table, i=info
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                // Set default order by Record Date (column index 1) descending
                order: [[ 1, 'desc' ]],
                // Disable ordering and searching on the 'Actions' column
                "columnDefs": [
                    { "orderable": false, "searchable": false, "targets": 10 }
                ]
            });
        });
    </script>
@endpush
