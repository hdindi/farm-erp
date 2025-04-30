<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * The module permissions assigned to this role via the pivot table.
     */
    public function modulePermissions(): BelongsToMany
    {
        return $this->belongsToMany(ModulePermission::class, 'role_permissions')
            ->withTimestamps();
    }

    /**
     * Accessor to get a collection of Permission models assigned to this role.
     * Requires 'modulePermissions.permission' to be eager loaded.
     */
    public function getPermissionsAttribute(): Collection
    {
        if (! $this->relationLoaded('modulePermissions.permission')) {
            return collect();
        }
        return $this->modulePermissions->map->permission->filter()->unique('id');
    }

    /**
     * The users that belong to the role (assuming 'role_user' pivot).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Check if the role has a specific permission, optionally within a specific module.
     * Requires 'modulePermissions.permission' and 'modulePermissions.module' to be loaded.
     */
    public function hasPermissionTo(string $permissionName, ?string $moduleName = null): bool
    {
        if (! $this->relationLoaded('modulePermissions.permission') || ($moduleName && !$this->relationLoaded('modulePermissions.module'))) {
            $this->loadMissing(['modulePermissions.permission', 'modulePermissions.module']);
        }

        return $this->modulePermissions->contains(function ($modulePermission) use ($permissionName, $moduleName) {
            $permissionMatch = $modulePermission->permission && $modulePermission->permission->name === $permissionName;
            $moduleMatch = !$moduleName || ($modulePermission->module && $modulePermission->module->name === $moduleName);
            return $permissionMatch && $moduleMatch;
        });
    }
}
