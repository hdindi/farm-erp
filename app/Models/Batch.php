<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'batches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_code',
        'bird_type_id',
        'breed_id',
        'source_farm',
        'bird_age_days',
        'initial_population',
        'current_population', // Ensure logic updates this when daily records are added/updated
        'date_received',
        'hatch_date',
        'expected_end_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_received' => 'date:Y-m-d', // Specify format if needed
        'hatch_date' => 'date:Y-m-d',
        'expected_end_date' => 'date:Y-m-d',
        'initial_population' => 'integer',
        'current_population' => 'integer',
        'bird_age_days' => 'integer',
        // 'status' => StatusEnum::class, // Example if using Laravel Enum casting
    ];

    /**
     * Get the bird type that owns the batch.
     */
    public function birdType(): BelongsTo
    {
        return $this->belongsTo(BirdType::class);
    }

    /**
     * Get the breed that owns the batch.
     */
    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    /**
     * Get the daily records for the batch.
     */
    public function dailyRecords(): HasMany
    {
        return $this->hasMany(DailyRecord::class);
    }

    /**
     * Get the disease management records for the batch.
     */
    public function diseaseManagementRecords(): HasMany
    {
        return $this->hasMany(DiseaseManagement::class);
    }

    /**
     * Get the vaccine schedules for the batch.
     */
    public function vaccineSchedules(): HasMany
    {
        return $this->hasMany(VaccineSchedule::class);
    }

    // Consider adding an accessor to calculate current age based on hatch_date/date_received
    // public function getCurrentAgeAttribute(): ?int { ... }

    // Consider adding logic (Observer or Service) to update current_population
    // based on daily_records dead_count and culls_count
}
