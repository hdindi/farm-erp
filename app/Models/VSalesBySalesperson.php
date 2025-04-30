<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VSalesBySalesperson extends Model
{
    protected $table = 'v_sales_by_salesperson';
    public $timestamps = false;
    protected $primaryKey = 'salesperson_name'; // Assuming name is unique for this summary view
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'number_of_sales' => 'integer',
        'total_sales_amount' => 'decimal:2',
        'total_amount_paid' => 'decimal:2',
    ];

    // Optional accessor for balance
    public function getBalanceAttribute() {
        return $this->total_sales_amount - $this->total_amount_paid;
    }
}
