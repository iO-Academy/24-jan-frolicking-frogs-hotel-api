<?php

namespace Database\Factories;

use App\Models\Friend;
use App\Models\Room;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->text(255),
            'rate' => rand(1, 100),
            'image' => $this->faker->imageUrl(400, 400),
            'min_capacity' => 3,
            'max_capacity' => 5,
            'description' => $this->faker->text(255),
            'type_id' => Type::factory(),
        ];
    }
}
