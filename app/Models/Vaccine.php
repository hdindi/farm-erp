<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vaccine extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vaccines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'manufacturer',
        'minimum_age_days',
        'booster_interval_days',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'minimum_age_days' => 'integer',
        'booster_interval_days' => 'integer',
    ];

    /**
     * Get the vaccination logs for the vaccine.
     */
    public function vaccinationLogs(): HasMany
    {
        return $this->hasMany(VaccinationLog::class);
    }

    /**
     * Get the vaccine schedules for the vaccine.
     */
    public function vaccineSchedules(): HasMany
    {
        return $this->hasMany(VaccineSchedule::class);
    }
}
