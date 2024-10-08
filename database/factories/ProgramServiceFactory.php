<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramService>
 */
class ProgramServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => $this->faker->numberBetween(1, 8),
            'service_id' => $this->faker->numberBetween(1, 6),
            'visits' => $this->faker->numberBetween(3, 15),
        ];
    }
}
