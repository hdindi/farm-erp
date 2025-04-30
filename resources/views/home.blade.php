@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Existing styles... */
        .kpi-card .card-body { padding: 1rem; }
        .kpi-card .card-title { font-size: 0.9rem; margin-bottom: 0.25rem; }
        .kpi-card .kpi-value { font-size: 1.75rem; font-weight: bold; margin-bottom: 0.25rem; line-height: 1.2;}
        .kpi-card .card-text a { font-size: 0.8rem; text-decoration: none; }
        .kpi-card .card-text a:hover { text-decoration: underline; }
        .trend-chart { height: 300px; }
        .action-panel .list-group-item { padding: 0.5rem 1rem; font-size: 0.9rem; }
        .action-panel .list-group-item small { color: #6c757d; }
        .activity-details { font-size: 0.8em; word-break: break-all; }
        /* Filter form spacing */
        .filter-form .form-label { margin-bottom: 0.25rem; font-size: 0.85rem; }
    </style>
@endpush

@php
    $currencySymbol = config('app.currency_symbol', '$');
    // Format dates passed from controller for display
    // Use Carbon directly if $dateFrom/$dateTo are Carbon instances
    // Default to today if variables aren't set (should be set by controller)
    $carbonDateFrom = isset($dateFrom) ? \Carbon\Carbon::parse($dateFrom) : \Carbon\Carbon::today();
    $carbonDateTo = isset($dateTo) ? \Carbon\Carbon::parse($dateTo) : \Carbon\Carbon::today();

    $displayDateFrom = $carbonDateFrom->format('M d, Y');
    $displayDateTo = $carbonDateTo->format('M d, Y');
    $displayRange = ($carbonDateFrom->isSameDay($carbonDateTo)) ? $displayDateFrom : $displayDateFrom . ' - ' . $displayDateTo;
    $chartRangeLabel = ($isDateRange ?? false) ? " (" . $carbonDateFrom->format('M d') . " - " . $carbonDateTo->format('M d') . ")" : " (Last 7 Days)";
@endphp

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Farm Dashboard</h1>
            {{-- Optional: Add a general report button --}}
            {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
        </div>

        {{-- Date Filter Form --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('home') }}" class="row g-3 align-items-end filter-form">
                    <div class="col-md-4 col-lg-3">
                        <label for="date_from" class="form-label">Date From:</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $carbonDateFrom->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label for="date_to" class="form-label">Date To:</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $carbonDateTo->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <button type="submit" class="btn btn-info btn-sm w-100"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm w-100" title="Reset to Today"><i class="fas fa-times"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>


        {{-- KPI Row 1 (Less Date Sensitive) --}}
        <div class="row">
            {{-- Active Batches --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Batches</div>
                                {{-- Use null coalescing operator for safety --}}
                                <div class="kpi-value">{{ $activeBatchesCount ?? 0 }}</div>
                                <a href="{{ route('batches.index') }}?status=active">View Active Batches &rarr;</a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-kiwi-bird fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Eggs Produced (Total Lifetime) --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Eggs Produced (Total)</div>
                                <div class="kpi-value">{{ number_format($totalEggsProduced ?? 0) }}</div>
                                <a href="{{ route('egg-production.index') }}">View Egg Records &rarr;</a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-egg fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pending POs --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Purchase Orders</div>
                                <div class="kpi-value">{{ $pendingOrdersCount ?? 0 }}</div>
                                <a href="{{ route('purchase-orders.index') }}?status=pending">View Pending Orders &rarr;</a> {{-- Adjust filter param if needed --}}
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upcoming Vaccinations (Based on Today) --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Upcoming Vaccinations (Next 7 days)</div>
                                <div class="kpi-value">{{ $upcomingVaccinationsCount ?? 0 }}</div>
                                <a href="{{ route('vaccine-schedule.index') }}?status=scheduled&upcoming=7">View Upcoming Schedule &rarr;</a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-syringe fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI Row 2 (Filtered KPIs) --}}
        <div class="row">
            {{-- Eggs Collected in Range --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Eggs Collected ({{ $displayRange }})</div>
                                <div class="kpi-value">{{ number_format($eggsInRange ?? 0) }}</div>
                                <span class="text-muted text-xs">
                                      @if(isset($layRateInRange) && $layRateInRange !== null)
                                        Lay Rate: {{ number_format($layRateInRange, 1) }}%
                                    @else
                                      (Rate shown for single day)
                                    @endif
                                  </span>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-egg fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feed Consumed in Range --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Feed Consumed ({{ $displayRange }})</div>
                                <div class="kpi-value">{{ number_format($feedInRange ?? 0, 1) }} kg</div>
                                <a href="{{ route('feed-records.index') }}?date_from={{ $carbonDateFrom->format('Y-m-d') }}&date_to={{ $carbonDateTo->format('Y-m-d') }}">View Feed Records &rarr;</a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-utensils fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mortality Rate in Range --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Avg Daily Mortality ({{ $displayRange }})</div>
                                <div class="kpi-value">{{ number_format($averageDailyMortalityRate ?? 0, 2) }}%</div>
                                <span class="text-muted text-xs">Average rate across batches</span>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-skull-crossbones fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Placeholder/Other KPI --}}
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-secondary shadow-sm h-100 kpi-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Another KPI</div>
                                <div class="kpi-value">...</div>
                                <span class="text-muted text-xs">Description</span>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Charts Row --}}
        <div class="row">
            {{-- Egg Production Trend Chart --}}
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Egg Production{{ $chartRangeLabel }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="eggProductionChart" class="trend-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feed Consumption Trend Chart --}}
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Feed Consumption (kg){{ $chartRangeLabel }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="feedConsumptionChart" class="trend-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Panels & Existing Sections Row --}}
        <div class="row">
            {{-- Overdue Vaccinations Panel --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100 action-panel">
                    <div class="card-header bg-danger text-white py-3">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-exclamation-triangle me-2"></i>Overdue Vaccinations ({{ $overdueVaccinationsCount ?? 0 }})</h6>
                    </div>
                    <div class="card-body">
                        {{-- Ensure $overdueVaccinations is passed and is iterable --}}
                        @if(!isset($overdueVaccinations) || $overdueVaccinations->isEmpty())
                            <p class="text-success"><i class="fas fa-check-circle"></i> No overdue vaccinations found.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($overdueVaccinations as $overdue)
                                    <li class="list-group-item">
                                        <a href="{{ route('vaccine-schedule.show', $overdue->id) }}" title="View Details">
                                            <i class="fas fa-syringe text-danger"></i>
                                            <strong>{{ $overdue->batch->batch_code ?? '?' }}</strong> - {{ $overdue->vaccine->name ?? '?' }}
                                        </a>
                                        <small class="d-block">Due: {{ $overdue->date_due->format('Y-m-d') }}</small>
                                    </li>
                                @endforeach
                                @if($overdueVaccinationsCount > 5)
                                    <li class="list-group-item text-center">
                                        <a href="{{ route('vaccine-schedule.index') }}?status=scheduled&overdue=1">View all {{ $overdueVaccinationsCount }} overdue...</a>
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Batch Status Chart (Existing) --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Batch Status Distribution</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center"> {{-- Center canvas --}}
                        <div style="position: relative; height:250px; width:250px"> {{-- Constrain size --}}
                            <canvas id="batchStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Recent Sales (Existing) --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100 action-panel">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Sales (Last 5)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Sales Person</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{-- Ensure $recentSales is passed and is iterable --}}
                                @forelse($recentSales ?? [] as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                                        <td>{{ $sale->salesPerson->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted text-center">No recent sales found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('sales-records.index') }}">View All Sales &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Recent Activities (Existing, with Updated @if condition) --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm action-panel">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                    </div>
                    <div class="card-body">
                        {{-- Ensure $latestActivities is passed and is iterable --}}
                        @if(!isset($latestActivities) || $latestActivities->isEmpty())
                            <p class="text-muted">No recent activities logged.</p>
                        @else
                            <ul class="list-group list-group-flush"> {{-- Changed to flush --}}
                                @foreach($latestActivities as $activity)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <i class="fas fa-user-clock text-muted me-2"></i>
                                                <strong>{{ $activity->user->name ?? 'System' }}</strong>
                                                <span class="badge bg-light text-dark ms-1">{{ $activity->action ?? $activity->log_name ?? 'Performed action' }}</span>
                                                @if($activity->table_name)
                                                    on <strong>{{ Str::singular(str_replace('_', ' ', $activity->table_name)) }}</strong>
                                                @endif
                                                @if($activity->record_id)
                                                    (ID: {{ $activity->record_id }})
                                                @endif
                                            </div>
                                            <small class="text-muted" title="{{ $activity->event_time }}">{{ $activity->event_time ? $activity->event_time->diffForHumans() : '' }}</small>
                                        </div>
                                        {{-- CORRECTED @if condition for properties --}}
                                        @if($activity->properties instanceof \Illuminate\Support\Collection && ($activity->properties->has('attributes') || $activity->properties->has('old')))
                                            <small class="d-block text-muted ms-4 ps-2 fst-italic activity-details">Details: {{ json_encode($activity->properties->except(['attributes', 'old'])) }}</small>
                                        @elseif($activity->properties instanceof \Illuminate\Support\Collection && $activity->properties->isNotEmpty())
                                            <small class="d-block text-muted ms-4 ps-2 fst-italic activity-details">Details: {{ $activity->properties->toJson() }}</small>
                                        @elseif(!empty($activity->properties))
                                            <small class="d-block text-muted ms-4 ps-2 fst-italic activity-details">Details: {{ json_encode($activity->properties) }}</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <div class="text-center mt-2">
                                <a href="{{ route('audit-logs.index') }}">View Full Audit Log &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>{{-- End Container Fluid --}}
@endsection

@push('scripts')
    {{-- Ensure Chart.js is included in layouts.app or here --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        {{--// Pass data for charts using @json, default to empty arrays--}}
        const eggTrendLabels = @json($eggTrendLabels ?? []);
        const eggTrendData = @json($eggTrendData ?? []);
        const feedTrendLabels = @json($feedTrendLabels ?? []);
        const feedTrendData = @json($feedTrendData ?? []);
        const batchStatusLabels = @json($batchStatusCounts->keys() ?? []);
        const batchStatusData = @json($batchStatusCounts->values() ?? []);


        document.addEventListener('DOMContentLoaded', function() {

            // Helper function for chart options
            const getChartOptions = (titleText) => ({
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false, text: titleText },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { autoSkip: true, maxTicksLimit: 7 } },
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                },
                interaction: { mode: 'index', intersect: false }
            });

            // --- Batch Status Chart ---
            const batchStatusCtx = document.getElementById('batchStatusChart')?.getContext('2d');
            if (batchStatusCtx && batchStatusLabels.length > 0) {
                const batchStatusChart = new Chart(batchStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: batchStatusLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)), // Capitalize labels
                        datasets: [{
                            data: batchStatusData,
                            backgroundColor: batchStatusLabels.map(label => { // Dynamic colors
                                if (label === 'active') return '#28a745'; // success
                                if (label === 'completed') return '#17a2b8'; // info
                                if (label === 'culled') return '#dc3545'; // danger
                                return '#6c757d'; // secondary default
                            }),
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Allow resizing
                        plugins: { legend: { position: 'top' } }
                    }
                });
            }

            // --- Egg Production Chart ---
            const eggCtx = document.getElementById('eggProductionChart')?.getContext('2d');
            if (eggCtx && eggTrendLabels.length > 0) {
                const eggChart = new Chart(eggCtx, {
                    type: 'line',
                    data: {
                        labels: eggTrendLabels,
                        datasets: [{
                            label: 'Eggs Produced',
                            data: eggTrendData,
                            borderColor: '#28a745', // Green line
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: getChartOptions('Daily Egg Production')
                });
            }

            // --- Feed Consumption Chart ---
            const feedCtx = document.getElementById('feedConsumptionChart')?.getContext('2d');
            if (feedCtx && feedTrendLabels.length > 0) {
                const feedChart = new Chart(feedCtx, {
                    type: 'bar',
                    data: {
                        labels: feedTrendLabels,
                        datasets: [{
                            label: 'Feed Consumed (kg)',
                            data: feedTrendData,
                            borderColor: '#0d6efd', // Blue border
                            backgroundColor: 'rgba(13, 110, 253, 0.5)',
                            borderWidth: 1
                        }]
                    },
                    options: getChartOptions('Daily Feed Consumption')
                });
            }

        });
    </script>
@endpush
