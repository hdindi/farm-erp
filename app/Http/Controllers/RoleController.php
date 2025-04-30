<?php

namespace App\Http\Controllers; // Adjust namespace if needed

use App\Models\Role;
use App\Models\Module;
use App\Models\ModulePermission; // Needed for validation
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('name')->paginate(15);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pass empty role for form consistency (optional)
        $role = new Role(['is_active' => true]); // Default to active
        return view('roles.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        $role = Role::create($validated);

        // Redirect to edit page to assign permissions immediately
        return redirect()->route('roles.edit', $role->id)
            ->with('success', 'Role created successfully. Now assign permissions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        // Eager load permissions grouped by module for display
        $role->load(['modulePermissions.module', 'modulePermissions.permission']);
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Fetch all active modules with their active permissions for the assignment form
        $modules = Module::with(['modulePermissions' => function ($query) {
            $query->where('is_active', true)->with('permission'); // Load active permissions within active modulePermissions
        }])
            ->where('is_active', true) // Only active modules
            ->orderBy('name')
            ->get();

        // Also load the modulePermissions currently assigned to *this* role
        $role->load('modulePermissions'); // Loads existing links

        return view('roles.edit', compact('role', 'modules'));
    }

    /**
     * Update the specified resource in storage (Role details only).
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
        ]);

        $role->update($validated);

        // Redirect back to the edit page
        return redirect()->route('roles.edit', $role->id)
            ->with('success', 'Role details updated successfully.');
    }

    /**
     * Update the permissions assigned to the specified role.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        // Validate that the input is an array (even if empty)
        // and each ID exists in the module_permissions table
        $validated = $request->validate([
            'module_permission_ids' => 'nullable|array',
            'module_permission_ids.*' => ['integer', Rule::exists(ModulePermission::class, 'id')],
        ]);

        // Get the IDs from the request, default to empty array if not present
        $modulePermissionIds = $validated['module_permission_ids'] ?? [];

        // Sync the pivot table 'role_permissions'.
        // This attaches newly selected IDs, detaches unselected IDs, and leaves existing ones.
        $role->modulePermissions()->sync($modulePermissionIds);

        return redirect()->route('roles.edit', $role->id)
            ->with('success', 'Role permissions updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Optional: Add checks to prevent deleting core roles
        if (in_array(strtolower($role->name), ['admin', 'manager', 'user'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete core system roles.');
        }

        try {
            // Detach all permissions and users before deleting the role
            $role->modulePermissions()->detach();
            $role->users()->detach(); // Assuming role_user pivot exists
            $role->delete();
            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            \Log::error("Error deleting role ID {$role->id}: " . $e->getMessage());
            return redirect()->route('roles.index')
                ->with('error', 'Failed to delete role. It might be assigned to users or have other constraints. Error: ' . $e->getMessage());
        }
    }
}
