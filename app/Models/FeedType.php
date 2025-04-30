<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'feed_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed
    ];

    /**
     * Get the feed records for the feed type.
     */
    public function feedRecords(): HasMany
    {
        return $this->hasMany(FeedRecord::class);
    }

    /**
     * Get the supplier feed prices for the feed type.
     */
    public function supplierFeedPrices(): HasMany
    {
        return $this->hasMany(SupplierFeedPrice::class);
    }

    /**
     * Get the purchase orders for the feed type.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
