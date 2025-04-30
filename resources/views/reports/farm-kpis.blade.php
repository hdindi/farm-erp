@extends('layouts.app')

@section('title', 'Farm KPIs')

@push('styles')
    <style>
        /* Reusing KPI card styles from home */
        .kpi-card .card-body { padding: 1rem; }
        .kpi-card .card-title { font-size: 0.9rem; margin-bottom: 0.25rem; text-transform: uppercase; font-weight: bold; }
        .kpi-card .kpi-value { font-size: 2rem; font-weight: bold; margin-bottom: 0.25rem; line-height: 1.2;}
    </style>
@endpush

@php $currencySymbol = config('app.currency_symbol', '$'); @endphp

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col"><h1><i class="fas fa-tachometer-alt"></i> Farm Key Performance Indicators (KPIs)</h1></div>
            <div class="col-auto">
                {{-- Link back to main reports index or dashboard --}}
                <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>

        {{-- Check if the $kpis object exists (passed from controller) --}}
        @if(isset($kpis))
            <div class="row">
                {{-- Total Sales Revenue --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow-sm h-100 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="card-title text-info">Total Sales Revenue</div>
                                    <div class="kpi-value">{{ $currencySymbol }}{{ number_format($kpis->total_revenue_generated ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Total Sales Transactions --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow-sm h-100 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="card-title text-success">Total Sales Transactions</div>
                                    <div class="kpi-value">{{ number_format($kpis->total_sales_transactions ?? 0) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-receipt fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Current Alive Birds --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow-sm h-100 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="card-title text-primary">Current Alive Birds</div>
                                    <div class="kpi-value">{{ number_format($kpis->current_alive_birds ?? 0) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-kiwi-bird fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Total Deaths/Culls --}}
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow-sm h-100 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="card-title text-danger">Total Deaths/Culls</div>
                                    <div class="kpi-value">{{ number_format($kpis->total_deaths_culls_recorded ?? 0) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-skull-crossbones fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Add card for total_units_sold if meaningful --}}
                <div class="col-xl-3 col-md-6 mb-4">
                   <div class="card border-left-secondary shadow-sm h-100 kpi-card">
                       <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                   <div class="card-title text-secondary">Total Units Sold</div>
                                   <div class="kpi-value">{{ number_format($kpis->total_units_sold ?? 0) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-box fa-2x text-gray-300"></i></div>
                            </div>
                       </div>
                   </div>
               </div>

            </div>
        @else
            <div class="alert alert-warning">Could not retrieve farm KPIs data. Please ensure the database view `v_farm_kpis` exists and contains data.</div>
        @endif

        <div class="alert alert-secondary mt-3">
            <i class="fas fa-info-circle"></i> These Key Performance Indicators are derived from the <code>Key Performance Indicators </code> database view, providing an overall summary of farm activity.
        </div>

    </div>
@endsection
