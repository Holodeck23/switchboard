<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TriageRequest;
use App\Services\Triage\TriageService;
use Illuminate\Http\JsonResponse;

class TriageController extends Controller
{
    public function __invoke(TriageRequest $request, TriageService $triage): JsonResponse
    {
        $ticket = $triage->triage(
            guestEmail: $request->validated('from'),
            message: $request->validated('message'),
            channel: $request->validated('channel'),
        );

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_id' => $ticket->id,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
                'status' => $ticket->status,
                'confidence' => $ticket->confidence,
                'needs_escalation' => $ticket->needs_escalation,
                'draft_reply' => $ticket->draft_reply,
                'reservation' => $ticket->reservation ? [
                    'guest_name' => $ticket->reservation->guest_name,
                    'property' => $ticket->reservation->property->name,
                    'check_in' => $ticket->reservation->check_in->toDateString(),
                    'check_out' => $ticket->reservation->check_out->toDateString(),
                ] : null,
            ],
            'error' => null,
        ], 201);
    }
}
