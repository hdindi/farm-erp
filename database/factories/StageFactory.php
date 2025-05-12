<?php

namespace Database\Factories;

use App\Models\Stage;
use Illuminate\Database\Eloquent\Factories\Factory;

class StageFactory extends Factory
{
    protected $model = Stage::class;

    public function definition(): array
    {
        $minAge = $this->faker->numberBetween(1, 30);
        return [
            'name' => $this->faker->unique()->word . ' Stage',
            'description' => $this->faker->sentence,
            'min_age_days' => $minAge,
            'max_age_days' => $this->faker->numberBetween($minAge + 7, $minAge + 60),
            'target_weight_grams' => $this->faker->optional()->numberBetween(500, 2500),
        ];
    }
}
