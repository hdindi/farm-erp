<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VDailyEggSummary extends Model
{
    protected $table = 'v_daily_egg_summary';
    public $timestamps = false;
    protected $primaryKey = 'record_date'; // Assuming date is the primary grouping/key
    public $incrementing = false;
    protected $keyType = 'string'; // Date is treated as string key here

    protected $casts = [
        'record_date' => 'date',
        'total_eggs_collected' => 'integer',
        'good_eggs' => 'integer',
        'bad_eggs' => 'integer',
    ];
}
