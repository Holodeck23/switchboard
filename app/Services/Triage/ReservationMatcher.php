<?php

namespace App\Services\Triage;

use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Resolves a guest email to the most relevant reservation:
 * an active stay beats an upcoming one, which beats a recent past stay.
 */
class ReservationMatcher
{
    public function match(string $guestEmail, ?Carbon $now = null): ?MatchResult
    {
        $now = $now ?? now();

        // Email is case-insensitive: a guest writing from Anna@Example.com must
        // still match her reservation stored as anna@example.com.
        $candidates = Reservation::query()
            ->whereRaw('lower(guest_email) = ?', [Str::lower($guestEmail)])
            ->where('status', '!=', 'cancelled')
            ->with('property')
            ->orderBy('check_in')
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $active = $candidates->first(fn (Reservation $r) => $r->isActiveOn($now));
        if ($active) {
            return new MatchResult($active, 'active_stay', 100);
        }

        $upcoming = $candidates
            ->filter(fn (Reservation $r) => $r->check_in->gt($now))
            ->sortBy('check_in')
            ->first();
        if ($upcoming) {
            return new MatchResult($upcoming, 'upcoming_stay', 85);
        }

        $recent = $candidates
            ->filter(fn (Reservation $r) => $r->check_out->lt($now))
            ->sortByDesc('check_out')
            ->first();
        if ($recent && $recent->check_out->diffInDays($now) <= 30) {
            return new MatchResult($recent, 'recent_stay', 60);
        }

        return null;
    }
}
