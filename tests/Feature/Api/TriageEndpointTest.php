<?php

use App\Models\HelpArticle;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Services\Triage\TriageService;

use function Pest\Laravel\postJson;

it('triages a wifi question from a guest with an active stay', function () {
    $property = Property::factory()->create(['wifi_network' => 'Guest_Loft']);
    $reservation = Reservation::factory()->activeNow()->for($property)->create([
        'guest_email' => 'anna@example.com',
        'guest_name' => 'Anna Gruber',
    ]);
    HelpArticle::factory()->create(['category' => 'wifi', 'title' => 'Connecting to WiFi']);

    $response = postJson('/api/triage', [
        'from' => 'anna@example.com',
        'message' => 'Hi, what is the wifi password? I cannot find the router details.',
        'channel' => 'airbnb',
    ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.category', 'wifi')
        ->assertJsonPath('data.needs_escalation', false)
        ->assertJsonPath('data.reservation.guest_name', 'Anna Gruber');

    expect($response->json('data.confidence'))->toBeGreaterThanOrEqual(TriageService::ESCALATION_THRESHOLD)
        ->and($response->json('data.draft_reply'))->toContain('Guest_Loft');

    $ticket = Ticket::findOrFail($response->json('data.ticket_id'));
    expect($ticket->status)->toBe('triaged')
        ->and($ticket->reservation_id)->toBe($reservation->id)
        ->and($ticket->events()->pluck('type')->all())->toContain('created', 'auto_triaged');
});

it('marks a locked-out guest as urgent', function () {
    Reservation::factory()->activeNow()->create(['guest_email' => 'ben@example.com']);

    $response = postJson('/api/triage', [
        'from' => 'ben@example.com',
        'message' => 'I am locked out! The lockbox code is not working and it is late.',
        'channel' => 'booking',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.category', 'access')
        ->assertJsonPath('data.priority', 'urgent');
});

it('escalates when the sender matches no reservation', function () {
    $response = postJson('/api/triage', [
        'from' => 'stranger@example.com',
        'message' => 'I was charged twice for my stay, please refund one payment.',
        'channel' => 'email',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.needs_escalation', true)
        ->assertJsonPath('data.draft_reply', null)
        ->assertJsonPath('data.reservation', null);

    $ticket = Ticket::findOrFail($response->json('data.ticket_id'));
    expect($ticket->status)->toBe('new')
        ->and($ticket->events()->pluck('type')->all())->toContain('escalated');
});

it('rejects invalid payloads', function (array $payload, string $badField) {
    postJson('/api/triage', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($badField);
})->with([
    'missing email' => [['message' => 'help', 'channel' => 'airbnb'], 'from'],
    'bad email' => [['from' => 'not-an-email', 'message' => 'help me', 'channel' => 'airbnb'], 'from'],
    'unknown channel' => [['from' => 'a@b.com', 'message' => 'help me', 'channel' => 'pigeon'], 'channel'],
    'empty message' => [['from' => 'a@b.com', 'message' => '', 'channel' => 'direct'], 'message'],
]);
