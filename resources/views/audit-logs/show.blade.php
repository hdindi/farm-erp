@extends('layouts.app')

@section('title', 'Audit Log Details')

@push('styles')
    <style>
        .details-card .card-body { background-color: #f8f9fa; border-radius: 0.25rem; }
        .details-card dt { font-weight: bold; }
        .details-card dd { font-family: monospace; white-space: pre-wrap; word-break: break-all; }
        .diff { padding: 0.5rem; border-radius: 0.25rem; }
        .diff-old { background-color: #f8d7da; border: 1px solid #f5c6cb; } /* Light red */
        .diff-new { background-color: #d4edda; border: 1px solid #c3e6cb; } /* Light green */
        pre { background-color: #e9ecef; padding: 10px; border-radius: 4px; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1><i class="fas fa-history"></i> Audit Log Details #{{ $auditLog->id }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary" title="Back to List">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                {{-- No Edit/Delete actions --}}
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Log Summary
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Log ID</dt>
                    <dd class="col-sm-9">{{ $auditLog->id }}</dd>

                    <dt class="col-sm-3">Timestamp</dt>
                    <dd class="col-sm-9">{{ $auditLog->event_time ? $auditLog->event_time->format('Y-m-d H:i:s T') : 'N/A' }}</dd>

                    <dt class="col-sm-3">User</dt>
                    <dd class="col-sm-9">{{ $auditLog->user->name ?? 'System/Unknown' }} {{ $auditLog->user_id ? " (ID: {$auditLog->user_id})" : '' }}</dd>

                    <dt class="col-sm-3">Action</dt>
                    <dd class="col-sm-9"><span class="badge bg-secondary fs-6">{{ $auditLog->action ?? 'N/A' }}</span></dd>

                    <dt class="col-sm-3">Affected Table</dt>
                    <dd class="col-sm-9">{{ $auditLog->table_name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Affected Record ID</dt>
                    <dd class="col-sm-9">{{ $auditLog->record_id ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">IP Address</dt>
                    <dd class="col-sm-9">{{ $auditLog->ip_address ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">User Agent</dt>
                    <dd class="col-sm-9"><small>{{ $auditLog->user_agent ?? 'N/A' }}</small></dd>
                </dl>
            </div>
        </div>

        {{-- Properties / Changed Data --}}
        @if($auditLog->properties instanceof \Illuminate\Support\Collection && $auditLog->properties->isNotEmpty())
            <div class="card mb-4 details-card">
                <div class="card-header"><i class="fas fa-exchange-alt"></i> Changed Data / Properties</div>
                <div class="card-body">
                    @php
                        $old = $auditLog->properties->get('old');
                        $new = $auditLog->properties->get('attributes'); // Spatie uses 'attributes' for new values
                        $otherProps = $auditLog->properties->except(['old', 'attributes']);
                    @endphp

                    @if($old || $new)
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-danger">Old Values</h6>
                                @if($old)
                                    <pre class="diff diff-old"><code>{{ json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                @else
                                    <p class="text-muted">N/A</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">New Values</h6>
                                @if($new)
                                    <pre class="diff diff-new"><code>{{ json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                @else
                                    <p class="text-muted">N/A</p>
                                @endif
                            </div>
                        </div>
                        <hr>
                    @endif

                    @if($otherProps->isNotEmpty())
                        <h6>Other Properties</h6>
                        <pre><code>{{ json_encode($otherProps, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    @elseif(!$old && !$new)
                        <p class="text-muted">No detailed properties recorded for this event.</p>
                    @endif

                </div>
            </div>
        @else
            <div class="alert alert-secondary" role="alert">
                No detailed properties were recorded for this log entry.
            </div>
        @endif

    </div>
@endsection
