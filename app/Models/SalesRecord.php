<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesRecord extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sales_person_id', // Assumes this links to User model
        'customer_name',
        'customer_phone',
        'sales_price_id',
        'quantity',
        'total_amount',
        'amount_paid',
        'sale_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'sale_date' => 'date:Y-m-d',
    ];

    /**
     * Get the user (sales person) who made the sale.
     */
    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    /**
     * Get the sales price details used for this record.
     */
    public function salesPrice(): BelongsTo
    {
        return $this->belongsTo(SalesPrice::class);
    }
}
