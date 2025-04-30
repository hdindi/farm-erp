<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'min_age_days',
        'max_age_days',
        'target_weight_grams',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_age_days' => 'integer',
        'max_age_days' => 'integer',
        'target_weight_grams' => 'integer',
    ];

    /**
     * Get the daily records associated with the stage.
     */
    public function dailyRecords(): HasMany
    {
        return $this->hasMany(DailyRecord::class);
    }
}
