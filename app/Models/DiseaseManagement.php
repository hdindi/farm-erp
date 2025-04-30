<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiseaseManagement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'disease_management';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'disease_id',
        'drug_id',
        'observation_date',
        'affected_count',
        'treatment_start_date',
        'treatment_end_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'observation_date' => 'date:Y-m-d',
        'affected_count' => 'integer',
        'treatment_start_date' => 'date:Y-m-d',
        'treatment_end_date' => 'date:Y-m-d',
    ];

    /**
     * Get the batch associated with this disease record.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the disease recorded.
     */
    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    /**
     * Get the drug used for treatment (if any).
     */
    public function drug(): BelongsTo
    {
        // Use withDefault() if you want a default Drug model when drug_id is null
        // return $this->belongsTo(Drug::class)->withDefault();
        return $this->belongsTo(Drug::class);
    }
}
