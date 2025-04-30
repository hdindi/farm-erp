<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VFarmKpi extends Model
{
    protected $table = 'v_farm_kpis';
    public $timestamps = false;
    protected $primaryKey = null; // No single primary key
    public $incrementing = false;

    protected $casts = [
        'total_sales_transactions' => 'integer',
        'total_revenue_generated' => 'decimal:2',
        'total_units_sold' => 'decimal:2', // Or integer if units are always whole
        'current_alive_birds' => 'integer',
        'total_deaths_culls_recorded' => 'integer',
    ];
}
