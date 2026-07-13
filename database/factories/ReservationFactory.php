<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-2 weeks', '+3 weeks');

        return [
            'property_id' => Property::factory(),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->unique()->safeEmail(),
            'check_in' => $checkIn,
            'check_out' => (clone $checkIn)->modify('+'.fake()->numberBetween(2, 7).' days'),
            'channel' => fake()->randomElement(['airbnb', 'booking', 'direct']),
            'status' => 'confirmed',
        ];
    }

    public function activeNow(): static
    {
        return $this->state(fn () => [
            'check_in' => now()->subDays(2),
            'check_out' => now()->addDays(3),
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn () => [
            'check_in' => now()->addDays(5),
            'check_out' => now()->addDays(9),
        ]);
    }

    public function past(int $daysAgo = 10): static
    {
        return $this->state(fn () => [
            'check_in' => now()->subDays($daysAgo + 4),
            'check_out' => now()->subDays($daysAgo),
        ]);
    }
}
