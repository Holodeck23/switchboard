<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Services\Triage\TriageService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Support Team',
            'email' => 'demo@switchboard.test',
            'password' => bcrypt('switchboard'),
        ]);

        $this->seedHelpArticles();

        $properties = collect([
            ['name' => 'Alpine Loft', 'wifi_network' => 'AlpineLoft_5G', 'access' => 'The lockbox is to the right of the main entrance. Your 4-digit code is in your check-in email, sent 24h before arrival.'],
            ['name' => 'River Studio', 'wifi_network' => 'RiverGuest', 'access' => 'Smart lock on the front door — enter the code from your check-in email followed by the # key.'],
            ['name' => 'Old Town Apartment', 'wifi_network' => 'OldTown_Guest', 'access' => 'Key collection from the lockbox by the courtyard gate. Code in your arrival instructions.'],
        ])->map(fn ($p) => Property::create([
            'name' => $p['name'],
            'address' => fake()->streetAddress().', Linz, Austria',
            'timezone' => 'Europe/Vienna',
            'wifi_network' => $p['wifi_network'],
            'access_notes' => $p['access'],
        ]));

        // A spread of reservations: active, upcoming, recent past.
        $guests = [
            ['Anna Gruber', 'anna@example.com', 'active'],
            ['Ben Hofer', 'ben@example.com', 'active'],
            ['Clara Weiss', 'clara@example.com', 'upcoming'],
            ['David Moser', 'david.guest@example.com', 'upcoming'],
            ['Elena Rossi', 'elena@example.com', 'recent'],
            ['Felix Bauer', 'felix@example.com', 'recent'],
        ];

        foreach ($guests as [$name, $email, $when]) {
            $factory = Reservation::factory()->for($properties->random());
            $factory = match ($when) {
                'active' => $factory->activeNow(),
                'upcoming' => $factory->upcoming(),
                'recent' => $factory->past(8),
            };
            $factory->create(['guest_name' => $name, 'guest_email' => $email]);
        }

        $this->seedTicketsViaTriage();
    }

    private function seedHelpArticles(): void
    {
        $articles = [
            ['wifi', 'Connecting to the WiFi', 'The network name and password are printed on the card beside the router. If the connection drops, switch the router off at the wall for ten seconds and back on.'],
            ['access', 'Getting into your rental', 'Your access code arrives by email 24 hours before check-in. Enter it on the lockbox or smart lock exactly as shown. If it fails, message us and we will reset it remotely.'],
            ['billing', 'Understanding your charges', 'Your booking total covers the nightly rate, cleaning fee, and any local tourist tax. Security deposits are pre-authorised, not charged, and released after checkout.'],
            ['cleaning', 'Housekeeping standards', 'Every stay begins with a professional clean. If anything is not right on arrival, tell us the same day and we will send housekeeping back.'],
            ['noise', 'Quiet hours', 'Quiet hours are 10pm to 7am to respect neighbours. If noise outside is disturbing your stay, let us know and we will contact the property manager.'],
        ];

        foreach ($articles as [$category, $title, $body]) {
            HelpArticle::create(compact('category', 'title', 'body'));
        }
    }

    private function seedTicketsViaTriage(): void
    {
        $triage = app(TriageService::class);

        $incoming = [
            ['anna@example.com', 'Hi! What is the wifi password? I cannot get the router to connect.', 'airbnb'],
            ['ben@example.com', 'I am locked out! The lockbox code is not working and it is late here.', 'booking'],
            ['clara@example.com', 'Just checking — what time can I check in on Friday?', 'airbnb'],
            ['elena@example.com', 'I think I was charged twice, there is a second deposit fee on my card.', 'email'],
            ['felix@example.com', 'The towels were dirty and there was a stain on the sheets when we arrived.', 'direct'],
            ['stranger@example.com', 'Do you allow early check-in and late checkout for longer stays?', 'email'],
            ['david.guest@example.com', 'The neighbours were having a loud party until 2am, very hard to sleep.', 'booking'],
        ];

        foreach ($incoming as [$from, $message, $channel]) {
            $triage->triage($from, $message, $channel);
        }
    }
}
