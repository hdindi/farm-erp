<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * Get the modulePermission links associated with this permission.
     */
    public function modulePermissions(): HasMany
    {
        return $this->hasMany(ModulePermission::class);
    }

    /**
     * The modules that this permission is associated with, via module_permissions.
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'module_permissions');
    }
}
