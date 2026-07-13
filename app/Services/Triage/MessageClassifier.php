<?php

namespace App\Services\Triage;

/**
 * Deterministic keyword classifier. This is deliberately the dumbest thing
 * that works: it gives the pipeline a classification seam that an LLM call
 * can replace later without touching the rest of the flow.
 */
class MessageClassifier
{
    private const CATEGORY_KEYWORDS = [
        'access' => ['key', 'lock', 'lockbox', 'code', 'door', 'check in', 'check-in', 'get in', 'entrance', 'locked out'],
        'wifi' => ['wifi', 'wi-fi', 'internet', 'password', 'router', 'connection'],
        'billing' => ['charge', 'charged', 'refund', 'invoice', 'payment', 'deposit', 'price', 'fee'],
        'cleaning' => ['clean', 'dirty', 'towel', 'sheets', 'trash', 'smell', 'stain'],
        'noise' => ['noise', 'loud', 'neighbour', 'neighbor', 'party', 'music'],
    ];

    private const URGENT_KEYWORDS = ['locked out', 'cannot get in', "can't get in", 'emergency', 'no heating', 'flood', 'leak', 'urgent'];

    public function classify(string $message): Classification
    {
        $normalized = mb_strtolower($message);

        $scores = collect(self::CATEGORY_KEYWORDS)
            ->map(fn (array $keywords) => collect($keywords)
                ->filter(fn (string $kw) => str_contains($normalized, $kw))
                ->count())
            ->filter(fn (int $hits) => $hits > 0);

        $category = $scores->isEmpty() ? 'other' : $scores->sortDesc()->keys()->first();
        $topHits = $scores->max() ?? 0;

        $isUrgent = collect(self::URGENT_KEYWORDS)
            ->contains(fn (string $kw) => str_contains($normalized, $kw));

        $priority = match (true) {
            $isUrgent => 'urgent',
            $category === 'access' => 'high',
            $category === 'other' => 'low',
            default => 'normal',
        };

        $certainty = match (true) {
            $topHits >= 2 => 90,
            $topHits === 1 => 70,
            default => 30,
        };

        return new Classification($category, $priority, $certainty);
    }
}
