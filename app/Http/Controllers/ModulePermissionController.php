<?php

namespace App\Http\Controllers; // Adjust namespace if needed

use App\Models\ModulePermission;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModulePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load module and permission for display
        $modulePermissions = ModulePermission::with(['module', 'permission'])
            ->orderBy('module_id')->orderBy('permission_id')
            ->paginate(20);
        return view('module-permissions.index', compact('modulePermissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Module::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $permissions = Permission::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('module-permissions.create', compact('modules', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_id' => [
                'required',
                'exists:modules,id',
                // Unique combination check
                Rule::unique('module_permissions')->where(function ($query) use ($request) {
                    return $query->where('module_id', $request->module_id)
                        ->where('permission_id', $request->permission_id);
                })
            ],
            'permission_id' => 'required|exists:permissions,id',
            'is_active' => 'required|boolean',
        ], [
            'module_id.unique' => 'This permission is already assigned to this module.'
        ]);

        ModulePermission::create($validated);

        return redirect()->route('module-permissions.index')
            ->with('success', 'Module permission link created successfully.');
    }

    /**
     * Display the specified resource. (Optional - Index might be sufficient)
     */
    public function show(ModulePermission $modulePermission)
    {
        $modulePermission->load(['module', 'permission', 'roles']); // Load related data
        return view('module-permissions.show', compact('modulePermission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModulePermission $modulePermission)
    {
        $modules = Module::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $permissions = Permission::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        return view('module-permissions.edit', compact('modulePermission', 'modules', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModulePermission $modulePermission)
    {
        $validated = $request->validate([
            'module_id' => [
                'required',
                'exists:modules,id',
                // Unique combination check, ignoring current record
                Rule::unique('module_permissions')->where(function ($query) use ($request) {
                    return $query->where('module_id', $request->module_id)
                        ->where('permission_id', $request->permission_id);
                })->ignore($modulePermission->id)
            ],
            'permission_id' => 'required|exists:permissions,id',
            'is_active' => 'required|boolean',
        ], [
            'module_id.unique' => 'This permission is already assigned to this module.'
        ]);

        $modulePermission->update($validated);

        return redirect()->route('module-permissions.index')
            ->with('success', 'Module permission link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModulePermission $modulePermission)
    {
        try {
            // Detach from roles before deleting the link itself
            $modulePermission->roles()->detach();
            $modulePermission->delete();
            return redirect()->route('module-permissions.index')
                ->with('success', 'Module permission link deleted successfully.');
        } catch (\Exception $e) {
            \Log::error("Error deleting module permission ID {$modulePermission->id}: " . $e->getMessage());
            return redirect()->route('module-permissions.index')
                ->with('error', 'Failed to delete module permission link. Check logs.');
        }
    }
}
