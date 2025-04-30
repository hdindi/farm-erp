@extends('layouts.app')

@section('title', 'Permissions')

@push('styles')
    <style>
        .dataTables_wrapper .row:first-child { margin-bottom: 1rem; }
        .dt-buttons .btn { margin-right: 0.5rem; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-auto">
                <h1><i class="fas fa-key"></i> Permissions Management</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Permission
                </a>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-user-shield"></i> Manage Roles
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Permissions List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="permissions-table" class="table table-striped table-hover table-bordered" style="width:100%">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name (Code)</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th> {{-- Added Actions --}}
                        </tr>
                        </thead>
                        <tbody>
                        {{-- $permissions passed from PermissionController@index --}}
                        @foreach($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td><code>{{ $permission->name }}</code></td>
                                <td>{{ Str::limit($permission->description, 60) }}</td>
                                <td>
                                    @if($permission->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Permission Actions">
                                        <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-sm btn-info me-1" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Prevent deleting core permissions if needed --}}
                                        @if(!in_array($permission->name, ['create', 'read', 'update', 'delete'])) {{-- Example --}}
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal_{{ $permission->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled title="Cannot delete core permission">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Delete Confirmation Modal --}}
                                    <div class="modal fade" id="deleteModal_{{ $permission->id }}" tabindex="-1"> {{-- Content Here --}}
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the Permission: <strong>{{ $permission->name }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{-- Render pagination links if using Laravel pagination --}}
                    {{-- $permissions->links() --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Include DataTables if you want sorting/filtering/exporting --}}
    <script>
        $(document).ready(function() {
            $('#permissions-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copyHtml5', text: '<i class="fas fa-copy"></i> Copy', titleAttr: 'Copy', className: 'btn btn-secondary' },
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', titleAttr: 'Excel', className: 'btn btn-success', title: 'Permissions List' },
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv"></i> CSV', titleAttr: 'CSV', className: 'btn btn-info', title: 'Permissions List' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', titleAttr: 'PDF', className: 'btn btn-danger', title: 'Permissions List'},
                    { extend: 'print', text: '<i class="fas fa-print"></i> Print', titleAttr: 'Print', className: 'btn btn-warning', title: 'Permissions List'},
                    { extend: 'colvis', text: '<i class="fas fa-eye-slash"></i> Columns', titleAttr: 'Columns', className: 'btn btn-light' }
                ],
                "columnDefs": [ {
                    "targets": [4], // Target the "Actions" column
                    "orderable": false,
                    "searchable": false
                } ]
            });
        });
    </script>
@endpush
