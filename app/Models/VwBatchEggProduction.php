<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model corresponding to the vw_batch_egg_production SQL view.
 */
class VwBatchEggProduction extends Model
{
    /**
     * The table associated with the model (maps to the SQL view name).
     *
     * @var string
     */
    protected $table = 'vw_batch_egg_production';

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
    protected $primaryKey = null; // View likely keyed by date/batch combo

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
        'total_eggs' => 'integer',
        'good_eggs' => 'integer',
        'cracked_eggs' => 'integer',
        'damaged_eggs' => 'integer',
        'laying_rate_percent' => 'decimal:2',
        // 'collection_time' is a TIME type, cast as string or custom time object if needed
    ];
}
