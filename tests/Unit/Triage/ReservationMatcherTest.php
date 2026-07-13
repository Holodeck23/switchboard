<?php

use App\Models\Reservation;
use App\Services\Triage\ReservationMatcher;

it('prefers an active stay over an upcoming one', function () {
    Reservation::factory()->upcoming()->create(['guest_email' => 'g@example.com']);
    $active = Reservation::factory()->activeNow()->create(['guest_email' => 'g@example.com']);

    $result = app(ReservationMatcher::class)->match('g@example.com');

    expect($result->reservation->id)->toBe($active->id)
        ->and($result->basis)->toBe('active_stay')
        ->and($result->quality)->toBe(100);
});

it('falls back to the nearest upcoming stay', function () {
    Reservation::factory()->upcoming()->create(['guest_email' => 'g@example.com']);

    $result = app(ReservationMatcher::class)->match('g@example.com');

    expect($result->basis)->toBe('upcoming_stay');
});

it('matches a recent past stay within 30 days but not older', function () {
    Reservation::factory()->past(10)->create(['guest_email' => 'recent@example.com']);
    Reservation::factory()->past(45)->create(['guest_email' => 'old@example.com']);

    $matcher = app(ReservationMatcher::class);

    expect($matcher->match('recent@example.com')->basis)->toBe('recent_stay')
        ->and($matcher->match('old@example.com'))->toBeNull();
});

it('ignores cancelled reservations', function () {
    Reservation::factory()->activeNow()->create(['guest_email' => 'g@example.com', 'status' => 'cancelled']);

    expect(app(ReservationMatcher::class)->match('g@example.com'))->toBeNull();
});
