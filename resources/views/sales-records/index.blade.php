@extends('layouts.app')

@section('title', 'Sales Records')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        /* Align numeric columns to the right */
        #sales-records-table th.numeric,
        #sales-records-table td.numeric { text-align: right; }
        .balance-positive { color: red; font-weight: bold; }
        .balance-zero { color: green; }
    </style>
@endpush

@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-receipt"></i> Sales Records</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-records.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Record New Sale
                </a>
            </div>
        </div>

        {{-- Filtering Section (Optional) --}}
        {{-- Consider adding filters for date range, sales person, item type etc. --}}

        <div class="card">
            <div class="card-header">
                Sales Record List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sales-records-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Sale Date</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th class="numeric">Total Amt</th>
                            <th class="numeric">Amt Paid</th>
                            <th class="numeric">Balance</th>
                            <th>Customer</th>
                            <th>Sales Person</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $salesRecords passed from Controller --}}
                        @foreach($salesRecords as $record)
                            @php
                                $balance = $record->total_amount - $record->amount_paid;
                                $balanceClass = $balance > 0 ? 'balance-positive' : 'balance-zero';
                            @endphp
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td>{{ $record->sale_date ? $record->sale_date->format('Y-m-d') : 'N/A' }}</td>
                                <td> {{-- Item Details --}}
                                    @if($record->salesPrice)
                                        @if($record->salesPrice->item_type === 'bird' && $record->salesPrice->batch)
                                            {{ ucfirst($record->salesPrice->item_type) }} ({{ $record->salesPrice->batch->batch_code }})
                                        @else
                                            {{ ucfirst($record->salesPrice->item_type) }}
                                        @endif
                                        / {{ $record->salesPrice->salesUnit->name ?? 'Unit' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $record->quantity }}</td>
                                <td class="numeric">{{ $currencySymbol }}{{ number_format($record->total_amount, 2) }}</td>
                                <td class="numeric">{{ $currencySymbol }}{{ number_format($record->amount_paid, 2) }}</td>
                                <td class="numeric {{ $balanceClass }}">{{ $currencySymbol }}{{ number_format($balance, 2) }}</td>
                                <td>{{ $record->customer_name ?? '-' }}</td>
                                <td>{{ $record->salesPerson->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Record Actions">
                                        <a href="{{ route('sales-records.show', $record->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales-records.edit', $record->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $record->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Confirmation Modal --}}
                                    <div class="modal fade" id="deleteModal_{{ $record->id }}" tabindex="-1" aria-labelledby="deleteModalLabel_{{ $record->id }}" aria-hidden="true">
                                        {{-- Modal Content Here --}}
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger" id="deleteModalLabel_{{ $record->id }}"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete Sales Record <strong>#{{ $record->id }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('sales-records.destroy', $record->id) }}" method="POST" class="d-inline">
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
                    {{ $salesRecords->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#sales-records-table').DataTable({
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
                    { "targets": [4, 5, 6], "className": "numeric" }, // Align numeric columns right
                    { "targets": [9], "orderable": false, "searchable": false } // Actions column
                ],
                // Default order by Sale Date descending
                order: [[ 1, 'desc' ]] // Order by Sale Date column (index 1) descending
            });
        });
    </script>
@endpush
