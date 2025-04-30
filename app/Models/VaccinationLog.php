<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VaccinationLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vaccination_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daily_record_id',
        'vaccine_id',
        'birds_vaccinated',
        'administered_by',
        'next_due_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birds_vaccinated' => 'integer',
        'next_due_date' => 'date:Y-m-d',
    ];

    /**
     * Get the daily record associated with this vaccination log.
     */
    public function dailyRecord(): BelongsTo
    {
        return $this->belongsTo(DailyRecord::class);
    }

    /**
     * Get the vaccine used.
     */
    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    /**
     * Get the vaccine schedule entry associated with this log (if linked).
     */
    public function vaccineSchedule(): HasOne // Assuming one log per schedule item completion
    {
        return $this->hasOne(VaccineSchedule::class);
    }
}
