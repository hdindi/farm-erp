<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model corresponding to the v_feed_consumption_summary SQL view.
 */
class VFeedConsumptionSummary extends Model
{
    /**
     * The table associated with the model (maps to the SQL view name).
     *
     * @var string
     */
    protected $table = 'v_feed_consumption_summary';

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
    protected $primaryKey = null; // Keyed by date/feed_type combo

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
        'feeding_day' => 'date',
        'total_quantity_kg' => 'decimal:2',
        'avg_cost_per_kg' => 'decimal:6', // Based on view calculation
    ];
}
