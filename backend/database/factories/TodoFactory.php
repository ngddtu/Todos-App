<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => rand(1,2),
            'title' => fake()->text(25),
            'is_done' => rand(0,1),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date(),
            'deadline' => fake()->date(),
        ];
    }
}
