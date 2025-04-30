<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaccineSchedule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vaccine_schedule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'vaccine_id',
        'date_due',
        'status',
        'administered_date',
        'vaccination_log_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_due' => 'date:Y-m-d',
        'administered_date' => 'date:Y-m-d',
        // 'status' => VaccineScheduleStatusEnum::class, // If using Enums
    ];

    /**
     * Get the batch associated with this schedule.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the vaccine scheduled.
     */
    public function vaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class);
    }

    /**
     * Get the vaccination log entry if this schedule item was administered and logged.
     */
    public function vaccinationLog(): BelongsTo
    {
        return $this->belongsTo(VaccinationLog::class);
    }
}
