<?php

namespace App\Http\Controllers; // Adjust namespace if needed

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Modules are likely fixed, so just show a list.
     */
    public function index()
    {
        $modules = Module::orderBy('name')->get();
        return view('modules.index', compact('modules'));
    }

    // Typically, you wouldn't have create/store/edit/update/destroy
    // for fixed modules defined by the application logic.
    // If your modules ARE dynamic, uncomment and implement these methods
    // similar to the RoleController.

    // public function create() { ... }
    // public function store(Request $request) { ... }
    // public function show(Module $module) { ... }
    // public function edit(Module $module) { ... }
    // public function update(Request $request, Module $module) { ... }
    // public function destroy(Module $module) { ... }
}
