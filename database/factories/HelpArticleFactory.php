<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HelpArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'category' => fake()->randomElement(\App\Models\Ticket::CATEGORIES),
            'body' => fake()->paragraph(),
        ];
    }
}
