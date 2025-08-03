@extends('layouts.app')

@section('title', 'Daily Egg Summary Report')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #egg-summary-table th, #egg-summary-table td { text-align: right; }
        #egg-summary-table th:first-child, #egg-summary-table td:first-child { text-align: left; }
        .chart-container { height: 350px; position: relative; }
        .chart-message { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #6c757d; font-style: italic; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <h1><i class="fas fa-chart-pie"></i> Daily Egg Summary Report</h1>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-filter"></i> Filter Report</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.daily-egg-summary') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from', now()->subDays(29)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Chart and Data Table in a row for better layout --}}
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header"><i class="fas fa-chart-line"></i> Total Eggs Collected Trend</div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="eggTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">Daily Egg Summary Data</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="egg-summary-table" class="table table-striped table-hover table-bordered" style="width:100%">
                                <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Total Collected</th>
                                    <th>Good Eggs</th>
                                    <th>Cracked/Damaged</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{-- ✅ CORRECTED: Use @foreach. If empty, the body will be empty, which is what DataTables expects. --}}
                                @foreach($displaySummaries as $summary)
                                    <tr>
                                        <td>{{ $summary->record_date->format('Y-m-d') }}</td>
                                        <td class="fw-bold">{{ number_format($summary->total_eggs_collected) }}</td>
                                        <td>{{ number_format($summary->good_eggs) }}</td>
                                        <td>{{ number_format($summary->bad_eggs) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                <tr>
                                    <th>Total for Period</th>
                                    <th>{{ number_format($totals['total_collected']) }}</th>
                                    <th>{{ number_format($totals['total_good']) }}</th>
                                    <th>{{ number_format($totals['total_bad']) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Add your DataTables JS includes here --}}

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#egg-summary-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['copy', 'excel', 'csv', 'pdf', 'print', 'colvis'],
                order: [[0, 'desc']],
            });

            // Initialize Chart
            const chartLabels = @json($chartLabels ?? []);
            const chartEggData = @json($chartEggData ?? []);
            const eggCtx = document.getElementById('eggTrendChart')?.getContext('2d');

            if (eggCtx && chartLabels.length > 0) {
                new Chart(eggCtx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Total Eggs Collected',
                            data: chartEggData,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            } else if (eggCtx) {
                const container = eggCtx.canvas.parentNode;
                container.innerHTML = '<p class="chart-message">No chart data available for the selected range.</p>';
            }
        });
    </script>
@endpush
