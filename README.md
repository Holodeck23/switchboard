# Switchboard

A support-triage back office for short-term rental teams, built with Laravel and Filament.

Guest messages arrive from every channel — Airbnb, Booking.com, direct email — and a
support agent has to answer each one with the right context: which stay is this, is the
guest here right now, what's the WiFi password for *that* apartment, is this urgent. Switchboard
does the first pass automatically so the agent starts from a drafted answer instead of a blank box.

## What it does

A guest message comes in through one endpoint. Switchboard:

1. **Matches the reservation** from the sender's email — preferring an active stay over an
   upcoming one over a recent past one, each with a confidence weight.
2. **Classifies the message** into a category (access, wifi, billing, cleaning, noise, other)
   and a priority, flagging genuine emergencies like a locked-out guest as urgent.
3. **Attaches the relevant help article** if one exists for that category.
4. **Drafts a guest-facing reply** grounded in the real reservation — the actual property's
   WiFi network, the actual lockbox instructions — never a generic template.
5. **Scores its own confidence** and, when that's too low to trust, holds the ticket
   untouched for a human instead of sending a guess.

Every ticket carries an event timeline recording exactly how it was triaged, so an agent
can see the machine's reasoning at a glance.

## The API

```
POST /api/triage
{ "from": "anna@example.com", "message": "what's the wifi password?", "channel": "airbnb" }
```

returns

```json
{
  "success": true,
  "data": {
    "ticket_id": 8,
    "category": "wifi",
    "priority": "normal",
    "status": "triaged",
    "confidence": 90,
    "needs_escalation": false,
    "draft_reply": "Hi Anna Gruber,\n\nThe WiFi at Alpine Loft is \"AlpineLoft_5G\" ...",
    "reservation": { "guest_name": "Anna Gruber", "property": "Alpine Loft", "check_in": "2026-07-11", "check_out": "2026-07-16" }
  },
  "error": null
}
```

This is the seam an AI support tool (Intercom Fin, a custom agent) would call: it hands over
a raw message and gets back structured context plus a review-ready draft. The classifier
today is deliberately a deterministic keyword pass — a single `MessageClassifier` interface
that an LLM call slots behind without touching the rest of the pipeline. Keyword rules carry
the collisions you'd expect ("password" leans wifi, "code" leans access); the confidence
score and human-escalation path exist precisely so a shaky classification never auto-sends.

The endpoint is intentionally open in this demo so it can be tried with a single `curl`.
In production it would sit behind a Sanctum token or signed-webhook check — `laravel/sanctum`
is already installed for that.

## The admin panel

A Filament panel at `/admin` gives the support team the daily view: a ticket queue with
priority, status, and confidence badges, filters by category and escalation, reservation
context, and the per-ticket event timeline. Filament rather than Laravel Nova because Nova
is a paid license and this is a public demo — the two are close analogs.

## Architecture

The triage pipeline is four small, single-purpose classes behind one service:

| Class | Responsibility |
|---|---|
| `ReservationMatcher` | Resolve a guest email to the most relevant reservation |
| `MessageClassifier` | Category + priority + certainty from message text |
| `ReplyDrafter` | Compose a context-grounded guest reply |
| `TriageService` | Orchestrate the above, score confidence, persist ticket + timeline |

Immutable value objects (`Classification`, `MatchResult`) carry results between stages.

## Running it

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Admin login (seeded): `demo@switchboard.test` / `switchboard`

## Tests

```bash
./vendor/bin/pest
```

18 tests covering the endpoint (each reservation-match path, classification, low-confidence
escalation, validation errors) and the services in isolation.

## Deployment

Deployed on Vercel via the community `vercel-php` runtime (`vercel.json` + the root
`index.php` entrypoint), backed by a Postgres database. SQLite is used only for local
development and the test suite; the read-only serverless filesystem means cache and
compiled-view paths are redirected to `/tmp` at boot.

## Stack

Laravel 13 · PHP 8.3 (Vercel runtime) · Filament 4 · Postgres / SQLite · Pest
