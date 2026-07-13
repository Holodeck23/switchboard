<?php

namespace App\Services\Triage;

use App\Models\Reservation;

/** Immutable reservation match with provenance. */
final readonly class MatchResult
{
    public function __construct(
        public Reservation $reservation,
        public string $basis,  // active_stay, upcoming_stay, recent_stay
        public int $quality,   // 0-100
    ) {}
}
