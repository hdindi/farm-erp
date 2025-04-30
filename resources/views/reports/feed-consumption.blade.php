@extends('layouts.app')

@section('title', 'Feed Consumption Report')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #feed-table th.numeric, #feed-table td.numeric { text-align: right; }
        .chart-container { height: 350px; }
    </style>
@endpush

@php $currencySymbol = config('app.currency_symbol', '$'); @endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col"><h1><i class="fas fa-utensils"></i> Feed Consumption Report</h1></div>
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-filter"></i> Filter Report</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.feed-consumption') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="batch_id" class="form-label">Batch</label>
                        <select name="batch_id" id="batch_id" class="form-select">
                            <option value="">All Active Batches</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->batch_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="feed_type_id" class="form-label">Feed Type</label>
                        <select name="feed_type_id" id="feed_type_id" class="form-select">
                            <option value="">All Feed Types</option>
                            @foreach($feedTypes as $feedType)
                                <option value="{{ $feedType->id }}" {{ request('feed_type_id') == $feedType->id ? 'selected' : '' }}>
                                    {{ $feedType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Generate</button>
                        <a href="{{ route('reports.feed-consumption') }}" class="btn btn-secondary w-100 mt-1"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Chart (Example: Total Feed Consumption Over Time) --}}
        @if(!empty($chartLabels))
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-chart-bar"></i> Daily Feed Consumption (kg) Trend</div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="feedChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">Feed Consumption Data</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="feed-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Batch Code</th>
                            <th>Feed Type</th>
                            <th class="numeric">Quantity (kg)</th>
                            <th class="numeric">Cost/kg</th>
                            <th class="numeric">Total Cost</th>
                            <th>Time</th>
                            <th>Notes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($feedData as $data)
                            <tr>
                                <td data-sort="{{ $data->record_date->timestamp }}">{{ $data->record_date->format('Y-m-d') }}</td>
                                <td>{{ $data->batch_code }}</td>
                                <td>{{ $data->feed_type }}</td>
                                <td class="numeric">{{ number_format($data->quantity_kg, 2) }}</td>
                                <td class="numeric">{{ $data->cost_per_kg !== null ? $currencySymbol . number_format($data->cost_per_kg, 2) : '-' }}</td>
                                <td class="numeric">{{ $data->total_feed_cost !== null ? $currencySymbol . number_format($data->total_feed_cost, 2) : '-' }}</td>
                                <td>{{ $data->feeding_time ? date('H:i', strtotime($data->feeding_time)) : '-' }}</td>
                                <td>{{ Str::limit($data->notes, 50) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#feed-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success', title: 'Feed Consumption Report' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info', title: 'Feed Consumption Report' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape', title: 'Feed Consumption Report'},
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning', title: 'Feed Consumption Report'},
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                columnDefs: [
                    { targets: [3,4,5], className: 'numeric' }
                ],
                order: [[0, 'desc']] // Order by Date descending default
            });

            // --- Feed Consumption Chart ---
            const feedCtx = document.getElementById('feedChart')?.getContext('2d');
            if (feedCtx && @json($chartLabels).length > 0) {
                const feedChart = new Chart(feedCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Total Feed Consumed (kg)',
                            data: @json($chartFeedData),
                            borderColor: '#17a2b8', // Info color
                            backgroundColor: 'rgba(23, 162, 184, 0.5)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, title: { display: true, text: 'Quantity (kg)' } } }
                    }
                });
            }

        });
    </script>
@endpush
