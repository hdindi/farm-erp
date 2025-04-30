@extends('layouts.app')

@section('title', 'Audit Logs')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        /* Ensure filter form elements have spacing */
        .filter-form .form-label { margin-bottom: 0.25rem; }
        .filter-form .col-md-auto { padding-bottom: 1rem; } /* Add bottom padding to columns */
        /* Style for details column content */
        .log-details { font-size: 0.8em; color: #6c757d; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-history"></i> Audit Logs</h1>
            </div>
            {{-- No "Add New" button for Audit Logs --}}
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter Logs
            </div>
            <div class="card-body">
                {{-- Assuming the filter route name is 'audit-logs.filter' or adjust if filtering the index route directly --}}
                <form method="GET" action="{{ route('audit-logs.filter') }}" class="row g-3 align-items-end filter-form">
                    <div class="col-md-3 col-sm-6">
                        <label for="filter_user" class="form-label">User</label>
                        <select name="user_id" id="filter_user" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            {{-- $users passed from controller's filter method --}}
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label for="filter_action" class="form-label">Action</label>
                        <input type="text" name="action" id="filter_action" class="form-control form-control-sm" value="{{ request('action') }}" placeholder="e.g., CREATED, LOGIN">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label for="filter_table" class="form-label">Table Name</label>
                        <input type="text" name="table_name" id="filter_table" class="form-control form-control-sm" value="{{ request('table_name') }}" placeholder="e.g., batches">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label for="filter_date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="filter_date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label for="filter_date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="filter_date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 col-sm-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-info btn-sm w-100"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="col-md-1 col-sm-6 d-flex align-items-end">
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary btn-sm w-100" title="Clear Filters"><i class="fas fa-times"></i></a>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                Log List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="audit-logs-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Table</th>
                            <th>Record ID</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $auditLogs passed from Controller --}}
                        @foreach($auditLogs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td data-sort="{{ $log->event_time->timestamp }}"> {{-- Sortable timestamp --}}
                                    {{ $log->event_time ? $log->event_time->format('Y-m-d H:i:s') : 'N/A' }}
                                </td>
                                <td>{{ $log->user->name ?? 'System/Unknown' }}</td>
                                <td><span class="badge bg-secondary">{{ $log->action ?? 'N/A' }}</span></td>
                                <td>{{ $log->table_name ?? '-' }}</td>
                                <td>{{ $log->record_id ?? '-' }}</td>
                                <td class="log-details" title="{{ $log->properties ? $log->properties->toJson() : '' }}"> {{-- Show full JSON on hover --}}
                                    {{-- Display summary of properties --}}
                                    @if($log->properties instanceof \Illuminate\Support\Collection && $log->properties->isNotEmpty())
                                        {{ $log->properties->except(['attributes', 'old'])->toJson() ?: '{}' }}
                                    @elseif(!empty($log->properties))
                                        {{ json_encode($log->properties) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                    {{-- No Edit/Delete for Audit Logs --}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{-- Add query string parameters to pagination links if filters are applied --}}
                    {{ $auditLogs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Keep DataTables includes from app.blade.php --}}
    <script>
        $(document).ready(function() {
            $('#audit-logs-table').DataTable({
                responsive: true,
                // Disable export buttons by default for logs, can be added if needed
                dom: 'lfrtip', // Basic layout: length, filtering, processing, table, info, pagination
                // Add Buttons extension configuration if needed:
                // dom: 'Bfrtip',
                // buttons: [ 'copy', 'excel', 'csv', 'pdf', 'print', 'colvis' ],
                "columnDefs": [ {
                    "targets": [8], // Target the "Actions" column
                    "orderable": false,
                    "searchable": false
                }, {
                    "targets": [6], // Target the "Details" column
                    "orderable": false // Usually don't sort by JSON details
                }],
                // Default order by Timestamp descending
                order: [[ 1, 'desc' ]] // Order by Timestamp column (index 1) descending
            });
        });
    </script>
@endpush
