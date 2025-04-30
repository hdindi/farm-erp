@extends('layouts.app')

@section('title', 'Batch Summary Report')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        #summary-table th.numeric, #summary-table td.numeric { text-align: right; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col"><h1><i class="fas fa-boxes"></i> Batch Summary Report</h1></div>
            <div class="col-auto">
                {{-- Link back to main reports index or dashboard --}}
                <a href="{{ route('home') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-filter"></i> Filter by Status</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.batch-summary') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Batch Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="culled" {{ request('status') == 'culled' ? 'selected' : '' }}>Culled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('reports.batch-summary') }}" class="btn btn-secondary ms-2"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Batch Summaries</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="summary-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Batch Code</th>
                            <th>Bird Type</th>
                            <th>Breed</th>
                            <th class="numeric">Initial Pop.</th>
                            <th class="numeric">Current Pop.</th>
                            <th class="numeric">Reduction #</th>
                            <th class="numeric">Reduction %</th>
                            <th class="numeric">Age (Days)</th> {{-- Assuming this is current age based on view logic --}}
                            <th>Received</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $summaries passed from ReportController@batchSummary --}}
                        @foreach($summaries as $summary)
                            <tr>
                                {{-- Assuming batch_code is unique and can link to the batch show page --}}
                                {{-- If batch_code isn't the primary key, adjust link generation --}}
                                <td><a href="{{ route('batches.show', $summary->batch_id) }}">{{ $summary->batch_code }}</a></td>
                                <td>{{ $summary->bird_type }}</td>
                                <td>{{ $summary->breed }}</td>
                                <td class="numeric">{{ $summary->initial_population }}</td>
                                <td class="numeric">{{ $summary->current_population }}</td>
                                <td class="numeric">{{ $summary->reduction_in_population }}</td>
                                <td class="numeric">{{ $summary->reduction_rate_percent }}</td>
                                <td class="numeric">{{ $summary->bird_age_days }}</td>
                                <td>{{ $summary->date_received ? $summary->date_received->format('Y-m-d') : '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($summary->status) {
                                            'active' => 'success',
                                            'completed' => 'secondary',
                                            'culled' => 'danger',
                                            default => 'light',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($summary->status) }}</span>
                                </td>
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
    {{-- Ensure DataTables assets are included in layouts.app --}}
    <script>
        $(document).ready(function() {
            $('#summary-table').DataTable({
                responsive: true,
                dom: 'Bfrtip', // Layout with Buttons
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success', title: 'Batch Summary Report' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info', title: 'Batch Summary Report' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape', title: 'Batch Summary Report'},
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning', title: 'Batch Summary Report'},
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                columnDefs: [
                    { targets: [3,4,5,6,7], className: 'numeric' } // Align numeric columns
                ],
                order: [[0, 'asc']] // Order by Batch Code default
            });
        });
    </script>
@endpush
