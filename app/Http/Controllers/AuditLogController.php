<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $auditLogs = AuditLog::with(['user'])
            ->latest()
            ->paginate(20);

        return view('audit-logs.index', compact('auditLogs'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user']);
        return view('audit-logs.show', compact('auditLog'));
    }

    public function filter(Request $request)
    {
        $query = AuditLog::query()->with(['user']);

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('table_name') && $request->table_name) {
            $query->where('table_name', $request->table_name);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('event_time', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('event_time', '<=', $request->date_to);
        }

        $auditLogs = $query->latest()->paginate(20);
        $users = User::all();

        return view('audit-logs.index', compact('auditLogs', 'users'));
    }
}
