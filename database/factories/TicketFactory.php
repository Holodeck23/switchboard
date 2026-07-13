<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'guest_email' => fake()->safeEmail(),
            'channel' => fake()->randomElement(['airbnb', 'booking', 'direct', 'email']),
            'message' => fake()->sentence(12),
            'category' => fake()->randomElement(\App\Models\Ticket::CATEGORIES),
            'priority' => fake()->randomElement(\App\Models\Ticket::PRIORITIES),
            'status' => fake()->randomElement(\App\Models\Ticket::STATUSES),
            'confidence' => fake()->numberBetween(20, 95),
            'needs_escalation' => false,
        ];
    }
}
