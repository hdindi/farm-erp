<?php

namespace Database\Factories;

use App\Models\DailyRecord;
use App\Models\Batch; // Required for batch_id
use App\Models\Stage; // Required for stage_id
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyRecordFactory extends Factory
{
    protected $model = DailyRecord::class;

    public function definition(): array
    {
        return [
            'batch_id' => Batch::factory(), // Creates a Batch if not provided
            'record_date' => $this->faker->unique()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'stage_id' => Stage::factory(), // Creates a Stage if not provided
            'day_in_stage' => $this->faker->numberBetween(1, 28),
            'alive_count' => $this->faker->numberBetween(400, 500),
            'dead_count' => $this->faker->numberBetween(0, 5),
            'culls_count' => $this->faker->numberBetween(0, 2),
            'mortality_rate' => $this->faker->optional()->randomFloat(2, 0, 5),
            'average_weight_grams' => $this->faker->optional()->numberBetween(100, 2000),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
