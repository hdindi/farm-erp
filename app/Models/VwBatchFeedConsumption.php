<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model corresponding to the vw_batch_feed_consumption SQL view.
 */
class VwBatchFeedConsumption extends Model
{
    /**
     * The table associated with the model (maps to the SQL view name).
     *
     * @var string
     */
    protected $table = 'vw_batch_feed_consumption';

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
    protected $primaryKey = null;

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
        'quantity_kg' => 'decimal:2',
        'cost_per_kg' => 'decimal:2',
        'total_feed_cost' => 'decimal:4', // Based on view calculation
        // 'feeding_time' is a TIME type
    ];
}
