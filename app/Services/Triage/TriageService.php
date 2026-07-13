<?php

namespace App\Services\Triage;

use App\Models\HelpArticle;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

/**
 * The full triage pipeline: match reservation -> classify -> attach help
 * article -> draft reply -> persist ticket + event timeline.
 */
class TriageService
{
    /** Below this combined confidence, a human gets the ticket untouched. */
    public const ESCALATION_THRESHOLD = 50;

    public function __construct(
        private readonly ReservationMatcher $matcher,
        private readonly MessageClassifier $classifier,
        private readonly ReplyDrafter $drafter,
    ) {}

    public function triage(string $guestEmail, string $message, string $channel): Ticket
    {
        $match = $this->matcher->match($guestEmail);
        $classification = $this->classifier->classify($message);
        $article = HelpArticle::query()->where('category', $classification->category)->first();

        $confidence = $this->confidence($match, $classification);
        $needsEscalation = $confidence < self::ESCALATION_THRESHOLD;
        $draft = $needsEscalation
            ? null
            : $this->drafter->draft($classification, $match, $article);

        return DB::transaction(function () use ($guestEmail, $message, $channel, $match, $classification, $article, $confidence, $needsEscalation, $draft) {
            $ticket = Ticket::create([
                'reservation_id' => $match?->reservation->id,
                'help_article_id' => $article?->id,
                'guest_email' => $guestEmail,
                'channel' => $channel,
                'message' => $message,
                'category' => $classification->category,
                'priority' => $classification->priority,
                'status' => $needsEscalation ? 'new' : 'triaged',
                'confidence' => $confidence,
                'needs_escalation' => $needsEscalation,
                'draft_reply' => $draft,
            ]);

            $ticket->events()->create(['type' => 'created', 'detail' => "Received via {$channel}"]);
            $ticket->events()->create([
                'type' => 'auto_triaged',
                'detail' => sprintf(
                    'Category %s (%s priority), confidence %d%%, reservation match: %s',
                    $classification->category,
                    $classification->priority,
                    $confidence,
                    $match?->basis ?? 'none',
                ),
            ]);

            if ($needsEscalation) {
                $ticket->events()->create([
                    'type' => 'escalated',
                    'detail' => sprintf('Confidence %d%% below %d%% threshold — held for human review', $confidence, self::ESCALATION_THRESHOLD),
                ]);
            }

            return $ticket;
        });
    }

    private function confidence(?MatchResult $match, Classification $classification): int
    {
        $matchQuality = $match?->quality ?? 20;

        return (int) round(($matchQuality * $classification->certainty) / 100);
    }
}
