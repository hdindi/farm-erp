@extends('layouts.app')

@section('title', 'Sales by Salesperson Report')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #sales-table th.numeric, #sales-table td.numeric { text-align: right; }
        /* Style for positive balance (if needed, though less common in summary) */
        .balance-positive { color: red; font-weight: bold; }
        .balance-zero-or-negative { color: green; } /* Or default color */
    </style>
@endpush

@php $currencySymbol = config('app.currency_symbol', '$'); @endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col"><h1><i class="fas fa-user-tag"></i> Sales by Salesperson Report</h1></div>
            <div class="col-auto">
                {{-- Link back to main reports index or dashboard --}}
                <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>

        {{-- No filters needed for this specific view as it aggregates all data from the SQL view --}}
        {{-- If you wanted to filter by salesperson, you'd add a filter form here and adjust the controller --}}

        <div class="card">
            <div class="card-header">Sales Summary per Salesperson</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sales-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Salesperson</th>
                            <th class="numeric"># of Sales</th>
                            <th class="numeric">Total Sales Amount</th>
                            <th class="numeric">Total Amount Paid</th>
                            <th class="numeric">Total Balance Due</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $salesData passed from ReportController@salesBySalesperson --}}
                        @forelse($salesData as $data)
                            <tr>
                                <td>{{ $data->salesperson_name ?? 'Unknown/System' }}</td>
                                <td class="numeric">{{ $data->number_of_sales }}</td>
                                <td class="numeric">{{ $currencySymbol }}{{ number_format($data->total_sales_amount, 2) }}</td>
                                <td class="numeric">{{ $currencySymbol }}{{ number_format($data->total_amount_paid, 2) }}</td>
                                {{-- Calculate balance using accessor if defined, or manually --}}
                                @php $balance = $data->balance ?? ($data->total_sales_amount - $data->total_amount_paid); @endphp
                                <td class="numeric {{ $balance > 0.005 ? 'balance-positive' : 'balance-zero-or-negative' }}"> {{-- Added small tolerance for float comparison --}}
                                    {{ $currencySymbol }}{{ number_format($balance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No sales data found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                        {{-- Optional: Add a footer for overall totals --}}
                        <tfoot>
                        <tr class="table-light">
                            <th>Overall Total</th>
                            <th class="numeric">{{ number_format($salesData->sum('number_of_sales')) }}</th>
                            <th class="numeric">{{ $currencySymbol }}{{ number_format($salesData->sum('total_sales_amount'), 2) }}</th>
                            <th class="numeric">{{ $currencySymbol }}{{ number_format($salesData->sum('total_amount_paid'), 2) }}</th>
                            <th class="numeric">{{ $currencySymbol }}{{ number_format($salesData->sum('total_sales_amount') - $salesData->sum('total_amount_paid'), 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Ensure DataTables assets are included in layouts.app --}}
    <script>
        $(document).ready(function() {
            $('#sales-table').DataTable({
                responsive: true,
                dom: 'Bfrtip', // Layout with Buttons
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success', title: 'Sales by Salesperson', footer: true }, // Include footer in export
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info', title: 'Sales by Salesperson', footer: true }, // Include footer in export
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', title: 'Sales by Salesperson', footer: true }, // Include footer in export
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning', title: 'Sales by Salesperson', footer: true }, // Include footer in export
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                columnDefs: [
                    { targets: [1,2,3,4], className: 'numeric' } // Align numeric columns right
                ],
                order: [[2, 'desc']], // Order by Total Sales Amount desc default
                // Disable pagination/searching if showing all data
                // paging: false,
                // searching: false,
                // info: false
            });
        });
    </script>
@endpush
