<?php

namespace Database\Factories;

use App\Enums\ComplexityEnum;
use App\Models\ServiceSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceSchedule>
 */
class ServiceScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hall' => $this->faker->randomElement([ServiceSchedule::HALL_LIGHT, ServiceSchedule::HALL_DARK]),
            'service_id' => $this->faker->numberBetween(1, 6),
            'start_date' => $this->faker->dateTimeBetween('-1 day', '+10 days')->format('Y-m-d'),
            'start_time' => $this->faker->time(),
            'places_count_left' => $this->faker->numberBetween(1, 10),
            'complexity' => $this->faker->randomElement([
                ComplexityEnum::EASY->value,
                ComplexityEnum::MEDIUM->value,
                ComplexityEnum::HARD->value,
            ]),
        ];
    }
}
