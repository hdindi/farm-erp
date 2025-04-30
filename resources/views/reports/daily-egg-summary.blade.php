@extends('layouts.app')

@section('title', 'Daily Egg Summary Report')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #egg-summary-table th.numeric, #egg-summary-table td.numeric { text-align: right; }
        .chart-container { height: 350px; position: relative; } /* Added position relative */
        .chart-message { /* Style for the 'no data' message */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6c757d;
            font-style: italic;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col"><h1><i class="fas fa-chart-pie"></i> Daily Egg Summary Report</h1></div>
            <div class="col-auto">
                {{-- Link back to main reports index or dashboard --}}
                <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-filter"></i> Filter Report by Date Range</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.daily-egg-summary') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">Date From</label>
                        {{-- Default to 30 days ago if not set in request --}}
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from', now()->subDays(29)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">Date To</label>
                        {{-- Default to today if not set in request --}}
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Generate Report</button>
                        <a href="{{ route('reports.daily-egg-summary') }}" class="btn btn-secondary w-100 mt-1"><i class="fas fa-times"></i> Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Chart Container --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-chart-line"></i> Total Eggs Collected Trend</div>
            <div class="card-body">
                {{-- Always render the container, JS will handle message/chart --}}
                <div class="chart-container">
                    <canvas id="eggTrendChart"></canvas>
                    {{-- Message will be added by JS if needed --}}
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header">Daily Egg Summary Data</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="egg-summary-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="numeric">Total Collected</th>
                            <th class="numeric">Good Eggs</th>
                            <th class="numeric">Cracked/Damaged</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $eggSummaries passed from ReportController@dailyEggSummary --}}
                        @forelse($eggSummaries as $summary)
                            <tr>
                                <td data-sort="{{ $summary->record_date->timestamp }}">{{ $summary->record_date->format('Y-m-d') }}</td>
                                <td class="numeric fw-bold">{{ $summary->total_eggs_collected }}</td>
                                <td class="numeric">{{ $summary->good_eggs }}</td>
                                <td class="numeric">{{ $summary->bad_eggs }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No egg summary data found for the selected criteria.</td>
                            </tr>
                        @endforelse
                        </tbody>
                        {{-- Optional: Add a footer for totals if needed --}}
                        {{-- <tfoot>
                           <tr>
                               <th>Total</th>
                               <th class="numeric">{{ number_format($eggSummaries->sum('total_eggs_collected')) }}</th>
                               <th class="numeric">{{ number_format($eggSummaries->sum('good_eggs')) }}</th>
                               <th class="numeric">{{ number_format($eggSummaries->sum('bad_eggs')) }}</th>
                           </tr>
                        </tfoot> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Ensure Chart.js and DataTables are included in layouts.app --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        {{--// Pass PHP variables to JS using @json directive--}}
        // Default to empty arrays if the variables are null or not passed
        const chartLabels = @json($chartLabels ?? []); // <<< Added semicolon
        const chartEggData = @json($chartEggData ?? []); // <<< Added semicolon

        // --- Debugging Logs ---
        // console.log('Chart Labels Received:', chartLabels);
        // console.log('Chart Egg Data Received:', chartEggData);
        // console.log('Chart Labels Length:', chartLabels.length);
        // --- End Debugging Logs ---

        $(document).ready(function() {
            // Initialize DataTable
            $('#egg-summary-table').DataTable({
                responsive: true,
                dom: 'Bfrtip', // Layout includes Buttons
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success', title: 'Daily Egg Summary' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info', title: 'Daily Egg Summary' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', title: 'Daily Egg Summary'},
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning', title: 'Daily Egg Summary'},
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                columnDefs: [
                    { targets: [1,2,3], className: 'numeric' } // Align numeric columns right
                ],
                order: [[0, 'desc']], // Order by Date descending by default
                // searching: false, // Uncomment if you prefer no search
                // paging: false,    // Uncomment if you prefer no pagination
                // info: false,      // Uncomment if you prefer no info display
            });

            // --- Egg Trend Chart Initialization ---
            const eggCtx = document.getElementById('eggTrendChart')?.getContext('2d');
            const chartContainer = document.querySelector('.chart-container'); // Get the container

            // Check if context and chart data variables exist and have data
            if (eggCtx && chartLabels && chartLabels.length > 0 && chartEggData && chartEggData.length > 0) {
                // console.log('Attempting to render chart...'); // Uncomment for debugging
                const eggChart = new Chart(eggCtx, {
                    type: 'line',
                    data: {
                        labels: chartLabels, // Use JS variable
                        datasets: [{
                            label: 'Total Eggs Collected',
                            data: chartEggData, // Use JS variable
                            borderColor: '#ffc107', // Warning color
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { autoSkip: true, maxTicksLimit: 10 } }, // Limit x-axis labels
                            y: { beginAtZero: true, title: { display: true, text: 'Number of Eggs' } }
                        }
                    }
                });
            } else if(chartContainer) { // Check if container exists
                // console.log('No chart data or context found. Displaying message.'); // Uncomment for debugging
                // Add the 'no data' message dynamically if needed
                const noDataMsg = document.createElement('p');
                noDataMsg.textContent = 'No chart data available for selected range.';
                noDataMsg.className = 'chart-message'; // Apply styling
                // Clear previous message if any before appending
                const existingMsg = chartContainer.querySelector('.chart-message');
                if (existingMsg) {
                    existingMsg.remove();
                }
                chartContainer.appendChild(noDataMsg);
                if(eggCtx) eggCtx.canvas.style.display = 'none'; // Hide canvas if context exists but no data
            } else {
                console.error('Chart canvas container not found.');
            }

        });
    </script>
@endpush
