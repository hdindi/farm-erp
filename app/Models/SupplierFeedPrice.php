<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierFeedPrice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'supplier_feed_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_id',
        'feed_type_id',
        'purchase_unit_id',
        'supplier_price',
        'effective_date',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'supplier_price' => 'decimal:2',
        'effective_date' => 'date:Y-m-d',
    ];

    /**
     * Get the supplier offering this price.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the feed type this price applies to.
     */
    public function feedType(): BelongsTo
    {
        return $this->belongsTo(FeedType::class);
    }

    /**
     * Get the purchase unit this price applies to.
     */
    public function purchaseUnit(): BelongsTo
    {
        return $this->belongsTo(PurchaseUnit::class);
    }
}
