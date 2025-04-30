@extends('layouts.app')

@section('title', 'Disease Management Report')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col"><h1><i class="fas fa-notes-medical"></i> Disease Management Report</h1></div>
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-filter"></i> Filter Report</div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.disease-management') }}" class="row g-3 align-items-end">
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
                        <label for="disease_id" class="form-label">Disease</label>
                        <select name="disease_id" id="disease_id" class="form-select">
                            <option value="">All Diseases</option>
                            @foreach($diseases as $disease)
                                <option value="{{ $disease->id }}" {{ request('disease_id') == $disease->id ? 'selected' : '' }}>
                                    {{ $disease->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Observation Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Observation Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Generate</button>
                        <a href="{{ route('reports.disease-management') }}" class="btn btn-secondary w-100 mt-1"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <div class="card-header">Disease Management Data</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="disease-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Observed On</th>
                            <th>Batch</th>
                            <th>Disease</th>
                            <th>Drug Used</th>
                            <th>Affected #</th>
                            <th>Treatment Start</th>
                            <th>Treatment End</th>
                            <th>Notes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($diseaseData as $data)
                            <tr>
                                <td data-sort="{{ $data->observation_date->timestamp }}">{{ $data->observation_date->format('Y-m-d') }}</td>
                                <td>{{ $data->batch_code }}</td>
                                <td>{{ $data->disease_name }}</td>
                                <td>{{ $data->drug_name ?? '-' }}</td>
                                <td>{{ $data->affected_count ?? '-' }}</td>
                                <td>{{ $data->treatment_start_date ? $data->treatment_start_date->format('Y-m-d') : '-' }}</td>
                                <td>{{ $data->treatment_end_date ? $data->treatment_end_date->format('Y-m-d') : '-' }}</td>
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
    <script>
        $(document).ready(function() {
            $('#disease-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success', title: 'Disease Management Report' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info', title: 'Disease Management Report' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape', title: 'Disease Management Report'},
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning', title: 'Disease Management Report'},
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                order: [[0, 'desc']] // Order by Observation Date desc default
            });
        });
    </script>
@endpush
