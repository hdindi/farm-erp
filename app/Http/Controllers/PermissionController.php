<?php

namespace App\Http\Controllers; // Adjust namespace if needed

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     * Permissions are likely fixed, so just show a list.
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('permissions.index', compact('permissions'));
    }

    // Typically, you wouldn't have create/store/edit/update/destroy
    // for fixed permissions defined by the application logic.
    // If your permissions ARE dynamic, uncomment and implement these methods
    // similar to the RoleController.

    // public function create() { ... }
    // public function store(Request $request) { ... }
    // public function show(Permission $permission) { ... }
    // public function edit(Permission $permission) { ... }
    // public function update(Request $request, Permission $permission) { ... }
    // public function destroy(Permission $permission) { ... }
}
