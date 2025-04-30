<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * Get the modulePermission links associated with this module.
     */
    public function modulePermissions(): HasMany
    {
        return $this->hasMany(ModulePermission::class);
    }

    /**
     * The permissions associated with this module, via module_permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'module_permissions');
    }
}

