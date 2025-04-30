@extends('layouts.app')

@section('title', 'Vaccine Schedule')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
        .status-scheduled { background-color: #e2f3f5 !important; } /* Light blue */
        .status-administered { background-color: #d4edda !important; } /* Light green */
        .status-missed { background-color: #f8d7da !important; } /* Light red */
        .overdue { border-left: 5px solid #dc3545 !important; } /* Red border for overdue */
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-calendar-alt"></i> Vaccine Schedule</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vaccine-schedule.calendar') }}" class="btn btn-info me-1" title="Calendar View">
                    <i class="fas fa-calendar-day"></i> Calendar
                </a>
                <a href="{{ route('vaccine-schedule.timetable') }}" class="btn btn-warning me-1" title="Timetable View">
                    <i class="fas fa-list-alt"></i> Timetable
                </a>
                <a href="{{ route('vaccine-schedule.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Schedule Item
                </a>
            </div>
        </div>

        {{-- Filtering Section (Optional) --}}
        {{-- Consider filters for Batch, Vaccine, Status, Date Range --}}

        <div class="card">
            <div class="card-header">
                Vaccine Schedule List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="vaccine-schedule-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>Batch</th>
                            <th>Vaccine</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Administered On</th>
                            <th>Linked Log</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $vaccineSchedules passed from Controller (with eager loaded relations) --}}
                        @foreach($vaccineSchedules as $schedule)
                            @php
                                $isOverdue = $schedule->status == 'scheduled' && $schedule->date_due->isPast();
                                $rowClass = 'status-' . $schedule->status;
                                if ($isOverdue) {
                                    $rowClass .= ' overdue';
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $schedule->batch->batch_code ?? 'N/A' }}</td>
                                <td>{{ $schedule->vaccine->name ?? 'N/A' }}</td>
                                <td>{{ $schedule->date_due ? $schedule->date_due->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($schedule->status) {
                                            'administered' => 'success',
                                            'missed' => 'danger',
                                            default => 'info', // scheduled
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($schedule->status) }}</span>
                                    @if($isOverdue)
                                        <span class="badge bg-warning text-dark ms-1">Overdue</span>
                                    @endif
                                </td>
                                <td>{{ $schedule->administered_date ? $schedule->administered_date->format('Y-m-d') : '-' }}</td>
                                <td>
                                    @if($schedule->vaccination_log_id)
                                        <a href="{{ route('vaccination-logs.show', $schedule->vaccination_log_id) }}" title="View Log #{{$schedule->vaccination_log_id}}">
                                            <i class="fas fa-clipboard-check"></i> #{{ $schedule->vaccination_log_id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Schedule Actions">
                                        <a href="{{ route('vaccine-schedule.show', $schedule->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vaccine-schedule.edit', $schedule->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Quick Mark Administered Button --}}
                                        @if($schedule->status == 'scheduled')
                                            <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#markAdministeredModal_{{ $schedule->id }}" title="Mark Administered">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $schedule->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Confirmation Modal --}}
                                    <div class="modal fade" id="deleteModal_{{ $schedule->id }}" tabindex="-1"> {{-- Content Here --}}
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this schedule item?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('vaccine-schedule.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Mark Administered Modal --}}
                                    @if($schedule->status == 'scheduled')
                                        <div class="modal fade" id="markAdministeredModal_{{ $schedule->id }}" tabindex="-1"> {{-- Content Here --}}
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('vaccine-schedule.mark-administered', $schedule->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title"><i class="fas fa-check-circle"></i> Mark as Administered</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Confirm admin details for <strong>{{ $schedule->vaccine->name }}</strong> on Batch <strong>{{ $schedule->batch->batch_code }}</strong>.</p>
                                                            <div class="mb-3">
                                                                <label for="modal_administered_date_{{ $schedule->id }}" class="form-label">Admin Date<span class="text-danger">*</span></label>
                                                                <input type="date" class="form-control" id="modal_administered_date_{{ $schedule->id }}" name="administered_date" value="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="modal_vaccination_log_id_{{ $schedule->id }}" class="form-label">Link Log Entry<span class="text-danger">*</span></label>
                                                                <select class="form-select" id="modal_vaccination_log_id_{{ $schedule->id }}" name="vaccination_log_id" required>
                                                                    <option value="">Select Log Entry</option>
                                                                    {{-- Populate with $vaccinationLogs, potentially filtered --}}
                                                                    @foreach($vaccinationLogs ?? [] as $log)
                                                                        @if($log->batch_id == $schedule->batch_id && $log->vaccine_id == $schedule->vaccine_id) {{-- Basic Filter Example --}}
                                                                        <option value="{{ $log->id }}">
                                                                            Log #{{ $log->id }} ({{ $log->dailyRecord->record_date->format('Y-m-d') ?? 'N/A' }})
                                                                        </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                                <small class="form-text text-muted">A <a href="{{ route('vaccination-logs.create') }}?batch_id={{$schedule->batch_id}}&vaccine_id={{$schedule->vaccine_id}}" target="_blank">Log entry</a> must exist.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Confirm</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $vaccineSchedules->links() }} {{-- Keep if NOT using DataTables or need server-side links --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#vaccine-schedule-table').DataTable({
                responsive: true,
                // Add Buttons extension configuration
                dom: 'Bfrtip', // Layout: Buttons, filtering, processing, table, info, pagination
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', orientation: 'landscape' },
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning' },
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [ {
                    "targets": [6], // Target the "Actions" column (index 6)
                    "orderable": false,
                    "searchable": false
                } ],
                // Default order by Due Date ascending
                order: [[ 2, 'asc' ]] // Order by Due Date column (index 2) ascending
            });
        });
    </script>
@endpush
