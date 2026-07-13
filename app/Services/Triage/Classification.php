<?php

namespace App\Services\Triage;

/** Immutable result of message classification. */
final readonly class Classification
{
    public function __construct(
        public string $category,
        public string $priority,
        public int $certainty, // 0-100
    ) {}
}
