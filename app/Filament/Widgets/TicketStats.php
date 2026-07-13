<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $open = Ticket::where('status', '!=', 'resolved')->count();
        $urgent = Ticket::where('priority', 'urgent')->where('status', '!=', 'resolved')->count();
        $escalated = Ticket::where('needs_escalation', true)->count();
        $total = Ticket::count();
        $avgConfidence = (int) round(Ticket::avg('confidence') ?? 0);
        $autoRate = $total > 0
            ? (int) round(Ticket::where('needs_escalation', false)->count() / $total * 100)
            : 0;

        return [
            Stat::make('Open tickets', $open)
                ->description($urgent > 0 ? "{$urgent} urgent" : 'none urgent')
                ->color($urgent > 0 ? 'danger' : 'primary'),
            Stat::make('Auto-triaged', "{$autoRate}%")
                ->description('handled without escalation')
                ->color('success'),
            Stat::make('Needs a human', $escalated)
                ->description('held below confidence threshold')
                ->color($escalated > 0 ? 'warning' : 'gray'),
            Stat::make('Avg. confidence', "{$avgConfidence}%")
                ->description('across all triaged tickets')
                ->color('primary'),
        ];
    }
}
