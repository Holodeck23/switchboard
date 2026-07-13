<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display:flex; flex-wrap:wrap; gap:2rem;">
            @foreach ([
                ['label' => 'Open tickets', 'value' => $open],
                ['label' => 'Auto-triaged', 'value' => $autoRate . '%'],
                ['label' => 'Needs a human', 'value' => $escalated],
                ['label' => 'Avg. confidence', 'value' => $avgConfidence . '%'],
            ] as $stat)
                <div style="min-width:8rem;">
                    <div style="font-size:1.6rem; font-weight:700; line-height:1.2;">{{ $stat['value'] }}</div>
                    <div style="font-size:.8rem; text-transform:uppercase; letter-spacing:.04em; opacity:.55; margin-top:.15rem;">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:1.5rem; padding-top:1.25rem; border-top:1px solid rgba(255,255,255,.08); font-size:.9rem; opacity:.7; line-height:1.55;">
            <div style="font-weight:600; opacity:.9; margin-bottom:.25rem;">You're in a live demo.</div>
            Open <strong>Tickets</strong> to see the triage queue &mdash; each row is a guest message the system matched to a
            reservation, classified, and either drafted a reply for or held for a human. Every ticket was created by sending a
            message to the <code style="background:rgba(255,255,255,.1); padding:.05rem .3rem; border-radius:4px;">POST /api/triage</code> endpoint.
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
