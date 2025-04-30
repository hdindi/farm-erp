<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BirdType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bird_types'; // Explicitly define if needed, though Laravel convention matches

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'egg_production_cycle',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'egg_production_cycle' => 'integer',
    ];

    /**
     * Get the batches for the bird type.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    // Add relationship for sales_prices if item_id refers to bird_types
    // public function salesPrices(): HasMany { ... }
}
