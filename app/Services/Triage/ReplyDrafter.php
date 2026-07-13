<?php

namespace App\Services\Triage;

use App\Models\HelpArticle;

/**
 * Drafts a guest-facing reply from reservation context and the matched
 * help article. Templates, not magic — every draft is reviewed by a human
 * in the admin panel before sending.
 */
class ReplyDrafter
{
    public function draft(Classification $classification, ?MatchResult $match, ?HelpArticle $article): string
    {
        $greeting = $match
            ? "Hi {$match->reservation->guest_name},"
            : 'Hi there,';

        $context = $this->contextLine($classification->category, $match);
        $articleLine = $article ? "\n\nThis guide may help right away: \"{$article->title}\" — {$this->firstSentence($article->body)}" : '';

        return $greeting . "\n\n" . $context . $articleLine .
            "\n\nIf that doesn't sort it, reply here and a member of the team will step in straight away.";
    }

    private function contextLine(string $category, ?MatchResult $match): string
    {
        $property = $match?->reservation->property;

        return match ($category) {
            'access' => $property && $property->access_notes
                ? "Sorry you're having trouble getting in at {$property->name}. {$property->access_notes}"
                : "Sorry you're having trouble getting in — we're checking the access details for your stay now.",
            'wifi' => $property && $property->wifi_network
                ? "The WiFi at {$property->name} is \"{$property->wifi_network}\" — the password is on the card beside the router."
                : "We're getting your WiFi details now and will send them over in a moment.",
            'billing' => "Thanks for flagging this — we're reviewing the charge against your booking and will come back with a clear breakdown.",
            'cleaning' => "That's not the standard we aim for — we've alerted the housekeeping team and will make it right today.",
            'noise' => "Sorry the noise is disturbing your stay. We've contacted the property manager and will follow up with you shortly.",
            default => "Thanks for your message — we're looking into it and will get back to you shortly.",
        };
    }

    private function firstSentence(string $text): string
    {
        $pos = mb_strpos($text, '.');

        return $pos === false ? $text : mb_substr($text, 0, $pos + 1);
    }
}
