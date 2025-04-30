<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EggProduction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'egg_production';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daily_record_id',
        'total_eggs',
        'good_eggs',
        'cracked_eggs',
        'damaged_eggs',
        'collection_time',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_eggs' => 'integer',
        'good_eggs' => 'integer',
        'cracked_eggs' => 'integer',
        'damaged_eggs' => 'integer',
        // 'collection_time' => 'datetime:H:i:s', // Cast to time object if needed
    ];

    /**
     * Get the daily record that owns the egg production entry.
     */
    public function dailyRecord(): BelongsTo
    {
        return $this->belongsTo(DailyRecord::class);
    }
}
