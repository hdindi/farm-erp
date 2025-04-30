@extends('layouts.app')

@section('title', 'Batch Performance Report')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #performance-table th.numeric,
        #performance-table td.numeric { text-align: right; }
        .chart-container { height: 350px; } /* Adjust as needed */
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-chart-bar"></i> Batch Performance Report</h1>
            </div>
            {{-- Add links to other reports if desired --}}
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-filter"></i> Filter Report</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.batch-performance') }}" class="row g-3 align-items-end">
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
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Generate Report</button>
                        <a href="{{ route('reports.batch-performance') }}" class="btn btn-secondary w-100 mt-1"><i class="fas fa-times"></i> Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Chart (Example: Mortality Rate) --}}
        @if(request('batch_id') && !empty($chartLabels)) {{-- Only show chart if a specific batch is selected --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-chart-line"></i> Daily Mortality Rate (%)</div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="mortalityChart"></canvas>
                </div>
            </div>
        </div>
        @endif


        {{-- Data Table --}}
        <div class="card">
            <div class="card-header">Performance Data</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="performance-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Batch Code</th>
                            <th>Stage</th>
                            <th class="numeric">Day #</th>
                            <th class="numeric">Alive</th>
                            <th class="numeric">Dead</th>
                            <th class="numeric">Culls</th>
                            <th class="numeric">Mortality (%)</th>
                            <th class="numeric">Avg Wt(g)</th>
                            <th>Notes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($performanceData as $data)
                            <tr>
                                <td data-sort="{{ $data->record_date->timestamp }}">{{ $data->record_date->format('Y-m-d') }}</td>
                                <td>{{ $data->batch_code }}</td>
                                <td>{{ $data->stage_name }}</td>
                                <td class="numeric">{{ $data->day_in_stage }}</td>
                                <td class="numeric">{{ $data->alive_count }}</td>
                                <td class="numeric">{{ $data->dead_count }}</td>
                                <td class="numeric">{{ $data->culls_count }}</td>
                                <td class="numeric">{{ $data->daily_mortality_rate_percent ?? '0.00' }}</td>
                                <td class="numeric">{{ $data->average_weight_grams ?? '-' }}</td>
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
    {{-- DataTables and Chart.js needed --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#performance-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success', title: 'Batch Performance Report' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info', title: 'Batch Performance Report' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape', pageSize: 'A4', title: 'Batch Performance Report' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning', title: 'Batch Performance Report' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [
                    { "targets": [3,4,5,6,7,8], "className": "numeric" } // Align numeric columns
                ],
                order: [[ 0, 'asc' ]] // Order by Date ascending by default
            });

            // --- Mortality Chart ---
            const mortalityCtx = document.getElementById('mortalityChart')?.getContext('2d');
            if (mortalityCtx && @json($chartLabels).length > 0) { // Check if context and labels exist
                const mortalityChart = new Chart(mortalityCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Daily Mortality (%)',
                            data: @json($chartMortalityData),
                            borderColor: '#dc3545', // Red line
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            fill: false, // Don't fill under line
                            tension: 0.1
                        }]
                    },
                    options: { // Simple options for this chart
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Mortality Rate (%)' } }
                        }
                    }
                });
            }

        });
    </script>
@endpush
