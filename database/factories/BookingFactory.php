<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer' => $this->faker->name(),
            'guests' => rand(1, 11),
            'start' => $this->faker->date('1900-12-08', '2055-12-08'),
            'end' => $this->faker->date('1900-12-08', '2055-12-08'),
        ];
    }
}
