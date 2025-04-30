<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo; // If using polymorphic item_id

class SalesPrice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sales_unit_id',
        'price',
        'item_type', // e.g., 'egg', 'bird', 'manure' or App\Models\BirdType::class
        'item_id',   // ID of the related item (optional)
        'effective_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'effective_date' => 'date:Y-m-d',
        // 'status' => SalesPriceStatusEnum::class, // If using Enums
        // 'item_type' => ItemTypeEnum::class, // If using Enums
    ];

    /**
     * Get the sales unit associated with the price.
     */
    public function salesUnit(): BelongsTo
    {
        return $this->belongsTo(SalesUnit::class);
    }

    /**
     * Get the sales records associated with this price.
     */
    public function salesRecords(): HasMany
    {
        return $this->hasMany(SalesRecord::class);
    }

    /**
     * Get the parent item model (BirdType, Breed, etc.) - Polymorphic Example.
     * This requires item_type column to store the class name (e.g., App\Models\BirdType).
     * Uncomment and adapt if using polymorphic relations.
     */
    // public function item(): MorphTo
    // {
    //     return $this->morphTo();
    // }

    // OR, if item_id always refers to a specific table based on item_type (less flexible):
    // public function birdType(): BelongsTo {
    //     return $this->belongsTo(BirdType::class, 'item_id')->where('item_type', 'bird'); // Example
    // }
}
