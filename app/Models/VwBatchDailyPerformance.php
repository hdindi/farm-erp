<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model corresponding to the vw_batch_daily_performance SQL view.
 */
class VwBatchDailyPerformance extends Model
{
    /**
     * The table associated with the model (maps to the SQL view name).
     *
     * @var string
     */
    protected $table = 'vw_batch_daily_performance';

    /**
     * Indicates if the model should be timestamped. Views generally don't have these.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table. Views might not have one.
     *
     * @var string|null
     */
    protected $primaryKey = null; // Or set if view has a unique identifier combination

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'record_date' => 'date',
        'day_in_stage' => 'integer',
        'alive_count' => 'integer',
        'dead_count' => 'integer',
        'culls_count' => 'integer',
        'daily_mortality_rate_percent' => 'decimal:2',
        'average_weight_grams' => 'integer',
    ];

    // Relationships back to original tables are generally not needed
    // as the view already contains the joined data.
}
