@extends('layouts.app')

@section('title', 'Supplier Feed Prices')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #feed-prices-table td:nth-child(5) { font-weight: bold; } /* Style the price column */
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-tags"></i> Supplier Feed Prices</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('supplier-feed-prices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Price Record
                </a>
            </div>
        </div>

        {{-- Add Filtering Options Here (Optional but helpful) --}}
        {{-- <div class="card mb-4">
             <div class="card-body">
                 <form method="GET" action="{{ route('supplier-feed-prices.index') }}" class="row g-3 align-items-end">
                     <div class="col-md-3">
                         <label for="filter_supplier" class="form-label">Filter by Supplier</label>
                         <select name="supplier_id" id="filter_supplier" class="form-select">
                             <option value="">All Suppliers</option>
                             @foreach($suppliersForFilter ?? [] as $supplier) // Pass this from controller
                                 <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                     {{ $supplier->name }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                      <div class="col-md-3">
                         <label for="filter_feed_type" class="form-label">Filter by Feed Type</label>
                         <select name="feed_type_id" id="filter_feed_type" class="form-select">
                              <option value="">All Feed Types</option>
                             @foreach($feedTypesForFilter ?? [] as $feedType) // Pass this from controller
                                 <option value="{{ $feedType->id }}" {{ request('feed_type_id') == $feedType->id ? 'selected' : '' }}>
                                     {{ $feedType->name }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                     <div class="col-md-2">
                        <button type="submit" class="btn btn-info w-100"><i class="fas fa-filter"></i> Filter</button>
                     </div>
                      <div class="col-md-2">
                        <a href="{{ route('supplier-feed-prices.index') }}" class="btn btn-secondary w-100"><i class="fas fa-times"></i> Clear</a>
                     </div>
                 </form>
             </div>
        </div> --}}


        <div class="card">
            <div class="card-header">
                Feed Price List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="feed-prices-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Supplier</th>
                            <th>Feed Type</th>
                            <th>Unit</th>
                            <th>Price / Unit</th>
                            <th>Effective Date</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $supplierFeedPrices passed from Controller --}}
                        @foreach($supplierFeedPrices as $price)
                            <tr>
                                <td>{{ $price->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $price->feedType->name ?? 'N/A' }}</td>
                                <td>{{ $price->purchaseUnit->name ?? 'N/A' }}</td>
                                <td class="text-success">{{ config('app.currency_symbol', '$') }}{{ number_format($price->supplier_price, 2) }}</td>
                                <td>{{ $price->effective_date ? $price->effective_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ Str::limit($price->description, 30) }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Price Actions">
                                        <a href="{{ route('supplier-feed-prices.show', $price->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('supplier-feed-prices.edit', $price->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
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
                                                    Are you sure you want to delete this price record (ID: <strong>{{ $price->id }}</strong>)?
                                                    <p class="mt-2 small text-muted">
                                                        Supplier: {{ $price->supplier->name ?? 'N/A' }} |
                                                        Feed: {{ $price->feedType->name ?? 'N/A' }} |
                                                        Price: {{ config('app.currency_symbol', '$') }}{{ number_format($price->supplier_price, 2) }} / {{ $price->purchaseUnit->name ?? 'Unit' }}
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('supplier-feed-prices.destroy', $price->id) }}" method="POST" class="d-inline">
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
                    {{ $supplierFeedPrices->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#feed-prices-table').DataTable({
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
                // Optional: Default order (e.g., by effective date descending)
                order: [[ 4, 'desc' ]] // Order by Effective Date column (index 4) descending
            });
        });
    </script>
@endpush
