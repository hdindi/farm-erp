<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model corresponding to the v_daily_mortality_summary SQL view.
 */
class VDailyMortalitySummary extends Model
{
    /**
     * The table associated with the model (maps to the SQL view name).
     *
     * @var string
     */
    protected $table = 'v_daily_mortality_summary';

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
    protected $primaryKey = 'record_date'; // Assuming date is the primary grouping/key
    public $incrementing = false;
    protected $keyType = 'string'; // Date is treated as string key here

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'record_date' => 'date',
        'alive_birds' => 'integer',
        'dead_or_culled_birds' => 'integer',
        'avg_mortality_rate' => 'decimal:6', // Based on view calculation
    ];
}
