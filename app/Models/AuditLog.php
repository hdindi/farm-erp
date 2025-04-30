<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     * Audit logs usually have their own event_time.
     *
     * @var bool
     */
    //public $timestamps = false; // Disable default created_at/updated_at

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_time',
        'user_id',
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_time' => 'datetime',
        'old_values' => 'json', // Cast JSON strings to arrays/objects
        'new_values' => 'json', // Cast JSON strings to arrays/objects
        'properties' => 'collection', // <--- ADD THIS CAST
        // 'action' => AuditActionEnum::class, // If using Enums
    ];

    /**
     * Get the user who performed the action (if logged).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'System/Unknown', // Provide default if user_id is null
        ]);
    }

    /**
     * Get the model instance that was changed (polymorphic).
     * This requires 'table_name' to store the actual model class name (e.g., App\Models\Batch)
     * or a mapping defined elsewhere. 'record_id' is the ID.
     *
     * Note: This relationship might not work perfectly if 'table_name' stores
     * the raw table name instead of the model class. Adjust accordingly.
     */
    // public function auditable(): MorphTo
    // {
    //     // If table_name stores the raw table name, you might need a custom implementation
    //     // or ensure table_name stores the fully qualified class name.
    //     // This assumes table_name maps directly to a model's morphClass alias or class name.
    //     return $this->morphTo(__FUNCTION__, 'table_name', 'record_id');
    // }
}
