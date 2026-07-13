<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid gap-6 md:grid-cols-4">
            @foreach ([
                ['label' => 'Open tickets', 'value' => $open],
                ['label' => 'Auto-triaged', 'value' => $autoRate . '%'],
                ['label' => 'Needs a human', 'value' => $escalated],
                ['label' => 'Avg. confidence', 'value' => $avgConfidence . '%'],
            ] as $stat)
                <div>
                    <div class="text-2xl font-bold text-gray-950 dark:text-white">{{ $stat['value'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 border-t border-gray-200 pt-4 text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
            <p class="font-medium text-gray-700 dark:text-gray-300">You're in a live demo.</p>
            <p class="mt-1">
                Open <span class="font-medium text-gray-700 dark:text-gray-300">Tickets</span> to see the triage queue &mdash;
                each row is a guest message the system matched to a reservation, classified, and either drafted a reply for or
                held for a human. Every ticket here was created by sending a message to the
                <code class="rounded bg-gray-100 px-1 dark:bg-white/10">POST /api/triage</code> endpoint.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
