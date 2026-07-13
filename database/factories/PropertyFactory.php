<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Alpine Loft', 'River Studio', 'Old Town Apartment']).' '.fake()->buildingNumber(),
            'address' => fake()->streetAddress().', Linz, Austria',
            'timezone' => 'Europe/Vienna',
            'wifi_network' => 'Guest_'.fake()->word(),
            'access_notes' => 'The lockbox is left of the main door; the code is in your check-in email.',
        ];
    }
}
