<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model corresponding to the v_employee_activity_summary SQL view.
 */
class VEmployeeActivitySummary extends Model
{
    /**
     * The table associated with the model (maps to the SQL view name).
     *
     * @var string
     */
    protected $table = 'v_employee_activity_summary';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string|null
     */
    protected $primaryKey = 'user_name'; // Assuming name is unique for this summary view
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_actions_logged' => 'integer',
        'last_action_time' => 'datetime',
    ];
}
