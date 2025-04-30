<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feed_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daily_record_id',
        'feed_type_id',
        'quantity_kg',
        'cost_per_kg',
        'feeding_time',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_kg' => 'decimal:2',
        'cost_per_kg' => 'decimal:2',
        // 'feeding_time' => 'datetime:H:i:s', // Cast if needed
    ];

    /**
     * Get the daily record that owns the feed record.
     */
    public function dailyRecord(): BelongsTo
    {
        return $this->belongsTo(DailyRecord::class);
    }

    /**
     * Get the type of feed used.
     */
    public function feedType(): BelongsTo
    {
        return $this->belongsTo(FeedType::class);
    }
}
