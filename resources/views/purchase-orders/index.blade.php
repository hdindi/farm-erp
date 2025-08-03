@extends('layouts.app')

@section('title', 'Purchase Orders')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #po-table td:nth-child(5) { font-weight: bold; text-align: right; } /* Style total price */
        #po-table td:nth-child(7) { text-align: center; } /* Center status badge */
    </style>
@endpush

@php
    // Define currency symbol (consider moving this to a config or helper)
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-shopping-cart"></i> Purchase Orders</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New PO
                </a>
            </div>
        </div>

        {{-- Add Filtering Options Here (Optional but helpful) --}}
        {{-- <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('purchase-orders.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filter_supplier" class="form-label">Filter by Supplier</label>
                        <select name="supplier_id" id="filter_supplier" class="form-select"> // Populate from controller </select>
                    </div>
                     <div class="col-md-3">
                        <label for="filter_status" class="form-label">Filter by Status</label>
                        <select name="status_id" id="filter_status" class="form-select"> // Populate from controller </select>
                    </div>
                     <div class="col-md-2">
                        <label for="filter_date_from" class="form-label">Order Date From</label>
                        <input type="date" name="date_from" id="filter_date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                     <div class="col-md-2">
                        <label for="filter_date_to" class="form-label">Order Date To</label>
                        <input type="date" name="date_to" id="filter_date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                       <button type="submit" class="btn btn-info w-100"><i class="fas fa-filter"></i> Filter</button>
                       <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary w-100 mt-1"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
       </div> --}}

        <div class="card">
            <div class="card-header">
                Purchase Order List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="po-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>PO #</th>
                            <th>Supplier</th>
                            <th>Feed Type</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $purchaseOrders passed from Controller --}}
                        @foreach($purchaseOrders as $po)
                            <tr>
                                <td>{{ $po->purchase_order_no }}</td>
                                <td>{{ $po->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $po->feedType->name ?? 'N/A' }}</td>
                                <td>{{ $po->quantity }} {{ $po->purchaseUnit->name ?? '' }}</td>
                                <td>{{ $currencySymbol }}{{ number_format($po->total_price, 2) }}</td>
                                <td>{{ $po->order_date ? $po->order_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $po->status->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="PO Actions">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('purchase-orders.edit', $po->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $po->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Confirmation Modal (Unique ID per row) --}}
                                    <div class="modal fade" id="deleteModal_{{ $po->id }}" tabindex="-1" aria-labelledby="deleteModalLabel_{{ $po->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger" id="deleteModalLabel_{{ $po->id }}"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete Purchase Order <strong>{{ $po->purchase_order_no }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" class="d-inline">
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
{{--                    {{ $purchaseOrders->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#po-table').DataTable({
                responsive: true,
                // Add Buttons extension configuration
                dom: 'Bfrtip', // Layout: Buttons, filtering, processing, table, info, pagination
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape' }, // Landscape PDF might be better
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [ {
                    "targets": [7], // Target the "Actions" column (index 7)
                    "orderable": false,
                    "searchable": false
                } ],
                // Default order by Order Date descending
                order: [[ 5, 'desc' ]] // Order by Order Date column (index 5) descending
            });
        });
    </script>
@endpush
