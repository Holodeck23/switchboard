<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\Widget;

class DemoGuide extends Widget
{
    protected string $view = 'filament.widgets.demo-guide';

    protected int|string|array $columnSpan = 'full';

    // Render with the page instead of lazy-loading over Livewire, which is
    // unreliable on serverless (the lazy fetch leaves an empty placeholder).
    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $total = Ticket::count();

        return [
            'total' => $total,
            'open' => Ticket::where('status', '!=', 'resolved')->count(),
            'escalated' => Ticket::where('needs_escalation', true)->count(),
            'avgConfidence' => (int) round(Ticket::avg('confidence') ?? 0),
            'autoRate' => $total > 0
                ? (int) round(Ticket::where('needs_escalation', false)->count() / $total * 100)
                : 0,
        ];
    }
}
