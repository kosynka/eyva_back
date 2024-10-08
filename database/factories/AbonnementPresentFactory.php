<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AbonnementPresent>
 */
class AbonnementPresentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'abonnement_id' => $this->faker->numberBetween(1, 7),
            'visits' => $this->faker->numberBetween(1, 10),
            'text' => $this->faker->text(),
            'service_id' => $this->faker->numberBetween(1, 6),
        ];
    }
}
