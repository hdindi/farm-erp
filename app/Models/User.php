<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import BelongsToMany
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions; // <-- Add this use statement


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number', // Added based on your schema
        'is_active',    // Added based on your schema
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean', // Added cast
        ];
    }

    /**
     * The roles that belong to the user.
     * Assumes a many-to-many relationship using a 'role_user' pivot table.
     * If a user can only have one role, change this to belongsTo(Role::class)
     * and add a 'role_id' column to the 'users' table.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user'); // Assumes 'role_user' pivot table name
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string|array $roleName
     * @return bool
     */
    public function hasRole(string|array $roleName): bool
    {
        // Ensure roles relationship is loaded or load it
        $userRoles = $this->relationLoaded('roles') ? $this->roles : $this->roles()->get();

        if (is_array($roleName)) {
            return $userRoles->pluck('name')->intersect($roleName)->isNotEmpty();
        }

        return $userRoles->contains('name', $roleName);
    }

    /**
     * Check if the user has a specific permission through their roles.
     * Requires roles.modulePermissions.permission relationships to be loaded for efficiency.
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        // Ensure roles and their permissions are loaded or load them
        $userRoles = $this->relationLoaded('roles') ? $this->roles : $this->roles()->with('modulePermissions.permission')->get();

        foreach ($userRoles as $role) {
            // Use the hasPermissionTo method defined on the Role model
            if ($role->hasPermissionTo($permissionName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user is an administrator (example helper).
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin'); // Assumes an 'Admin' role exists
    }



    // // Remove these static properties if you define them in getActivitylogOptions
    // protected static $logAttributes = ['name', 'email'];
    // protected static $logOnlyDirty = true;
    // protected static $submitEmptyLogs = false;

    // Add this method
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone_number', 'is_active']) // Log changes to these attributes
            ->logOnlyDirty() // Only log changes (vs logging entire model on update)
            ->useLogName('User') // Optional: Set a custom log name
            ->setDescriptionForEvent(fn(string $eventName) => "User {$this->name} was {$eventName}") // Customize log description
            ->dontSubmitEmptyLogs(); // Prevent logging if nothing changed
    }

    // Add other relationships if needed (e.g., roles, salesRecords)
    // public function roles() { ... }
    // public function salesRecords() { ... }
}
