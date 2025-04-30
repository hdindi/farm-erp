@extends('layouts.app')

@section('title', 'Feed Records')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #feed-records-table th.numeric,
        #feed-records-table td.numeric { text-align: right; }
    </style>
@endpush

@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-utensils"></i> Feed Records</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('feed-records.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Feed Record
                </a>
            </div>
        </div>

        {{-- Filtering Section (Optional) --}}
        {{-- Consider filters for Batch, Feed Type, Date Range --}}

        <div class="card">
            <div class="card-header">
                Feed Record List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="feed-records-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Record Date</th>
                            <th>Batch Code</th>
                            <th>Feed Type</th>
                            <th class="numeric">Quantity (kg)</th>
                            <th class="numeric">Cost/kg</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $feedRecords passed from Controller (with eager loaded relations) --}}
                        @foreach($feedRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }}</td>
                                <td>{{ $record->dailyRecord->batch->batch_code ?? 'N/A' }}</td>
                                <td>{{ $record->feedType->name ?? 'N/A' }}</td>
                                <td class="numeric">{{ number_format($record->quantity_kg, 2) }}</td>
                                <td class="numeric">{{ $record->cost_per_kg !== null ? $currencySymbol . number_format($record->cost_per_kg, 2) : '-' }}</td>
                                <td>{{ $record->feeding_time ? date('H:i', strtotime($record->feeding_time)) : '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Record Actions">
                                        <a href="{{ route('feed-records.show', $record->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('feed-records.edit', $record->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
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
                                                    Are you sure you want to delete Feed Record <strong>#{{ $record->id }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('feed-records.destroy', $record->id) }}" method="POST" class="d-inline">
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
                    {{ $feedRecords->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#feed-records-table').DataTable({
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
                "columnDefs": [
                    { "targets": [4, 5], "className": "numeric" }, // Align numeric columns right
                    { "targets": [7], "orderable": false, "searchable": false } // Actions column
                ],
                // Default order by Record Date descending
                order: [[ 1, 'desc' ]] // Order by Record Date column (index 1) descending
            });
        });
    </script>
@endpush
