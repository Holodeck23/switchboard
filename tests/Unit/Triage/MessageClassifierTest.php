<?php

use App\Services\Triage\MessageClassifier;

it('classifies messages into the expected category and priority', function (string $message, string $category, string $priority) {
    $result = (new MessageClassifier())->classify($message);

    expect($result->category)->toBe($category)
        ->and($result->priority)->toBe($priority);
})->with([
    'wifi' => ['The wifi password is not working on the router', 'wifi', 'normal'],
    'access urgent' => ['I am locked out, the door code fails', 'access', 'urgent'],
    'billing' => ['Why was my card charged a second deposit fee?', 'billing', 'normal'],
    'cleaning' => ['The towels are dirty and the sheets have a stain', 'cleaning', 'normal'],
    'noise' => ['The neighbours are having a loud party', 'noise', 'normal'],
    'other' => ['Can you recommend a good restaurant nearby?', 'other', 'low'],
]);

it('is more certain with multiple keyword hits', function () {
    $classifier = new MessageClassifier();

    $oneHit = $classifier->classify('the internet is down');
    $twoHits = $classifier->classify('the wifi password for the router');

    expect($twoHits->certainty)->toBeGreaterThan($oneHit->certainty);
});
