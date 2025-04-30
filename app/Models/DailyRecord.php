<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // If only one egg record per day

class DailyRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'record_date',
        'stage_id',
        'day_in_stage',
        'alive_count', // Ensure logic updates this based on previous day/mortality
        'dead_count',
        'culls_count',
        'mortality_rate', // Consider calculating this via accessor or in reports
        'average_weight_grams',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'record_date' => 'date:Y-m-d',
        'alive_count' => 'integer',
        'dead_count' => 'integer',
        'culls_count' => 'integer',
        'day_in_stage' => 'integer',
        'average_weight_grams' => 'integer',
        'mortality_rate' => 'decimal:2',
    ];

    /**
     * Get the batch that owns the daily record.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the stage associated with the daily record.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * Get the egg production record(s) for the daily record.
     * Use HasOne if you enforce only one egg record per daily record.
     */
    public function eggProduction(): HasMany // or HasOne
    {
        return $this->hasMany(EggProduction::class);
        // return $this->hasOne(EggProduction::class);
    }

    /**
     * Get the feed records for the daily record.
     */
    public function feedRecords(): HasMany
    {
        return $this->hasMany(FeedRecord::class);
    }

    /**
     * Get the vaccination logs for the daily record.
     */
    public function vaccinationLogs(): HasMany
    {
        return $this->hasMany(VaccinationLog::class);
    }

    // Consider adding an Observer to automatically update Batch->current_population
    // when a DailyRecord is created or updated.
    // Consider an accessor to calculate mortality rate on the fly.
    // public function getCalculatedMortalityRateAttribute(): ?float { ... }
}
