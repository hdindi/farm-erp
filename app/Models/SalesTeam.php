<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Removed HasMany relationship for SalesPrice as it was removed from sales_prices table
// use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesTeam extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_teams';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_no',
        'email',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship to SalesRecord might be needed if a team is responsible for records,
    // but the current schema links SalesRecord directly to a User (sales_person_id).
    // If sales_person_id should link here instead of users, update SalesRecord model.
}
