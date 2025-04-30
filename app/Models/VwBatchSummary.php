<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwBatchSummary extends Model
{
    // Point the model to the SQL view name
    protected $table = 'vw_batch_summary';

    // Views might not have standard auto-incrementing primary keys
    // You might need to set this or define a composite key if applicable,
    // but for read-only reports, it's often not essential.
    // public $incrementing = false;
    // protected $primaryKey = null; // Or specify if view has a unique key

    // Prevent Eloquent from trying to manage timestamps if the view doesn't have them
    public $timestamps = false;

    // Define casts if needed for types (e.g., dates, numbers)
    protected $casts = [
        'reduction_rate_percent' => 'decimal:2',
        'date_received' => 'date',
        'hatch_date' => 'date',
        'expected_end_date' => 'date',
    ];

    // You generally wouldn't define relationships *from* the view model,
    // but you might define accessors or helper methods here.
}
