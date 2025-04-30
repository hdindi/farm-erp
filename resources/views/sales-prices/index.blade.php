@extends('layouts.app')

@section('title', 'Sales Prices')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #sales-prices-table td:nth-child(4) { font-weight: bold; text-align: right;} /* Price column */
    </style>
@endpush

@php
    $currencySymbol = config('app.currency_symbol', '$');
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-dollar-sign"></i> Sales Prices</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('sales-prices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Sales Price
                </a>
            </div>
        </div>

        {{-- Add Filtering Options Here (Optional) --}}
        {{-- Similar to purchase orders filter --}}

        <div class="card">
            <div class="card-header">
                Sales Price List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sales-prices-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Item Type</th>
                            <th>Item Details</th> {{-- e.g., Batch Code --}}
                            <th>Sales Unit</th>
                            <th>Price / Unit</th>
                            <th>Effective Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $salesPrices passed from Controller --}}
                        @foreach($salesPrices as $price)
                            <tr>
                                <td>{{ ucfirst($price->item_type) }}</td>
                                <td>
                                    @if($price->item_type === 'bird' && $price->batch)
                                        Batch: {{ $price->batch->batch_code }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $price->salesUnit->name ?? 'N/A' }}</td>
                                <td class="text-success">{{ $currencySymbol }}{{ number_format($price->price, 2) }}</td>
                                <td>{{ $price->effective_date ? $price->effective_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    @if($price->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Price Actions">
                                        <a href="{{ route('sales-prices.show', $price->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales-prices.edit', $price->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $price->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Confirmation Modal (Unique ID per row) --}}
                                    <div class="modal fade" id="deleteModal_{{ $price->id }}" tabindex="-1" aria-labelledby="deleteModalLabel_{{ $price->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger" id="deleteModalLabel_{{ $price->id }}"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this sales price record (ID: <strong>{{ $price->id }}</strong>)?
                                                    <p class="mt-2 small text-muted">
                                                        Item: {{ ucfirst($price->item_type) }} |
                                                        Price: {{ $currencySymbol }}{{ number_format($price->price, 2) }} / {{ $price->salesUnit->name ?? 'Unit' }}
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('sales-prices.destroy', $price->id) }}" method="POST" class="d-inline">
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
                    {{ $salesPrices->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#sales-prices-table').DataTable({
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
                    "targets": [6], // Target the "Actions" column (index 6)
                    "orderable": false,
                    "searchable": false
                } ],
                // Default order by Effective Date descending
                order: [[ 4, 'desc' ]] // Order by Effective Date column (index 4) descending
            });
        });
    </script>
@endpush
