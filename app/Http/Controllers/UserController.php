<?php

namespace App\Http\Controllers; // Adjust namespace if needed

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Validation\Rule; // For unique validation
use Illuminate\Validation\Rules\Password; // For password rules

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load roles to display them in the table efficiently
        $users = User::with('roles')->orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active roles for the assignment dropdown/checkboxes
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class], // Unique email
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($request->user_id)], // Allow null, unique if provided
            'password' => ['required', 'confirmed', Password::defaults()], // Requires password_confirmation field
            'is_active' => ['required', 'boolean'],
            'roles' => ['nullable', 'array'], // Roles should be an array
            'roles.*' => ['integer', 'exists:roles,id'], // Each item in roles array must be valid role ID
        ]);

        // Hash the password before creating the user
        // The 'hashed' cast in the User model handles this automatically if you pass 'password' directly
        // $validated['password'] = Hash::make($validated['password']); // No longer needed with cast

        // Create the user
        $user = User::create($validated);

        // Assign roles if provided
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']); // Sync roles using the relationship
        } else {
            $user->roles()->detach(); // Remove all roles if none are selected
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles'); // Eager load roles
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles'); // Load current roles for the form
        $roles = Role::where('is_active', true)->orderBy('name')->get(); // Get all active roles
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Ensure email is unique, ignoring the current user's ID
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            // Password is optional on update
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['required', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        // Only update password if a new one was provided
        if (!empty($validated['password'])) {
            // The 'hashed' cast in the User model handles hashing automatically
            // $validated['password'] = Hash::make($validated['password']); // No longer needed
        } else {
            unset($validated['password']); // Remove password from array if empty, so it's not updated
        }

        // Update user details
        $user->update($validated);

        // Sync roles
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        } else {
            $user->roles()->detach(); // Remove all roles if none are selected
        }

        return redirect()->route('users.index') // Redirect to index after update
        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Optional: Prevent deleting the currently logged-in user or the last admin
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }
        // Add more complex logic if needed to ensure at least one admin remains

        try {
            // Detach roles before deleting user (optional, depends on foreign key constraints)
            $user->roles()->detach();
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error deleting user ID {$user->id}: " . $e->getMessage());
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete user. Check logs.');
        }
    }
}
