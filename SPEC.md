# Switchboard — spec

A small Laravel + Filament app: the back office a short-term-rental support team
would actually use. Built as a working proof artifact for the Hospitable
Technical Support Engineer application.

## What it demonstrates (mapped to the posting)

| Posting requirement | Switchboard feature |
|---|---|
| "Design, build, maintain internal tools using Laravel (Nova)" | Filament admin panel (open-source analog of Nova — Nova is licensed) |
| "Laravel endpoints that power AI support tools (Intercom Fin)" | `POST /api/triage` — takes a raw guest message, resolves the reservation, returns context + drafted reply + confidence |
| "Automated workflows across teams" | Ticket auto-triage: category, priority, SLA clock, escalation flag |
| "Technical documentation / self-service" | README + seeded help-article model surfaced in triage responses |
| "Diagnose complex issues" | Investigation notes + event timeline per ticket |

## Domain model

- **Property** — name, address, timezone
- **Reservation** — property, guest name/email, check-in/out, channel (airbnb/booking/direct), status
- **Ticket** — reservation (nullable until matched), raw message, channel, category
  (access, wifi, billing, cleaning, noise, other), priority, status
  (new/triaged/investigating/waiting/resolved), confidence score, drafted reply
- **TicketEvent** — timeline entries (created, auto-triaged, escalated, replied, note)
- **HelpArticle** — title, body, category (matched into triage responses)

## Triage endpoint

`POST /api/triage` `{ "from": "guest email", "message": "text", "channel": "airbnb" }`

1. Validate input (form request).
2. Match reservation by guest email (active stay first, else upcoming, else recent).
3. Classify category + priority from keyword rules (deterministic, no API keys —
   the seam where an LLM call would slot in is a single interface).
4. Attach matching help article if one exists.
5. Draft a reply from templates + reservation context; compute confidence
   (match quality x classification certainty). Below threshold → escalate flag.
6. Create Ticket + TicketEvents, return JSON envelope
   `{ success, data: { ticket_id, category, priority, confidence, draft_reply, escalate }, error: null }`.

## Admin (Filament)

- Ticket queue with badges (priority, confidence, SLA), filters by status/category
- Ticket detail: message, reservation context, event timeline, draft reply editor
- Reservations + Properties + Help Articles CRUD
- Dashboard widgets: open tickets by priority, avg confidence, escalation rate

## Engineering bar

- SQLite, seeded demo data (3 properties, ~15 reservations, ~20 tickets)
- Pest feature tests on the triage endpoint (match paths, classification, low-confidence escalation, validation errors)
- Small classes: TriageService, ReservationMatcher, MessageClassifier, ReplyDrafter
- CI: GitHub Actions running the test suite

## Deploy

Live URL (host TBD: Fly.io or Railway, SQLite volume), public repo
`Holodeck23/switchboard`, README written in David's voice: what this is,
why Filament not Nova, what was learned.
