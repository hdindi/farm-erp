<?php

namespace Database\Factories;

use App\Models\FeedType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedTypeFactory extends Factory
{
    protected $model = FeedType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true) . ' Feed',
            'description' => $this->faker->sentence,
        ];
    }
}
