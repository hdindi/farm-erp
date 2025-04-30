<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Breed extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'breeds';

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
        // No specific casts needed based on schema
    ];

    /**
     * Get the batches for the breed.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    // Add relationship for sales_prices if item_id refers to breeds
    // public function salesPrices(): HasMany { ... }
}
