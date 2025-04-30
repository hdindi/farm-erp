@extends('layouts.app')

@section('title', 'Batches')

{{-- Add any specific styles for this page if needed --}}
@push('styles')
    <style>
        /* Add spacing for DataTables controls */
        .dataTables_wrapper .row:first-child {
            margin-bottom: 1rem;
        }
        .dt-buttons .btn {
            margin-right: 0.5rem;
        }
    </style>
@endpush


@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-kiwi-bird"></i> Batches</h1> {{-- Added icon --}}
            </div>
            <div class="col-auto">
                <a href="{{ route('batches.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Batch
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Batch List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    {{-- Added ID to the table --}}
                    <table id="batches-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Batch Code</th>
                            <th>Bird Type</th>
                            <th>Breed</th>
                            <th>Current Population</th>
                            <th>Status</th>
                            <th>Date Received</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>{{ $batch->batch_code }}</td>
                                <td>{{ $batch->birdType->name }}</td>
                                <td>{{ $batch->breed->name }}</td>
                                <td>{{ $batch->current_population }}</td>
                                <td>
                                <span class="badge bg-{{ $batch->status == 'active' ? 'success' : ($batch->status == 'completed' ? 'info' : 'danger') }}">
                                    {{ ucfirst($batch->status) }}
                                </span>
                                </td>
                                <td>{{ $batch->date_received->format('Y-m-d') }}</td>
                                <td>
                                    {{-- Added tooltips and slightly more spacing --}}
                                    <div class="btn-group" role="group" aria-label="Batch Actions">
                                        <a href="{{ route('batches.show', $batch->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- DataTables handles the "No data" message, but keep for clarity if JS fails --}}
                            <tr>
                                <td colspan="7" class="text-center">No batches found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Removed manual pagination - DataTables handles it --}}
                {{--
                <div class="d-flex justify-content-center mt-4">
                    {{ $batches->links() }}
                </div>
                 --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#batches-table').DataTable({
                // Enable pagination, searching, ordering (defaults)
                responsive: true, // Optional: Makes table responsive
                // Add Buttons extension configuration
                dom: 'Bfrtip', // Define the elements in the control layout (B=Buttons, f=filtering input, r=processing display, t=table, i=information summary, p=pagination)
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fas fa-copy"></i> Copy',
                        titleAttr: 'Copy to clipboard',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        titleAttr: 'Export to Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        titleAttr: 'Export to CSV',
                        className: 'btn btn-info'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        titleAttr: 'Export to PDF',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        titleAttr: 'Print table',
                        className: 'btn btn-warning'
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fas fa-eye-slash"></i> Column Visibility',
                        titleAttr: 'Show/hide columns',
                        className: 'btn btn-light'
                    }
                ],
                // Optional: Specify which columns actions should not apply to (e.g., sorting, searching)
                "columnDefs": [ {
                    "targets": [6], // Target the "Actions" column (index 6)
                    "orderable": false, // Disable sorting
                    "searchable": false // Disable searching
                } ]
            });
        });
    </script>
@endpush
