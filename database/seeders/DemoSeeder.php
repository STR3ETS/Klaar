<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Entry;
use App\Models\LineItem;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user
        $user = User::create([
            'name' => 'Jan de Vries',
            'email' => 'jan@klaar.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '06-12345678',
            'company_name' => 'De Vries Bouw',
            'kvk_number' => '12345678',
            'btw_number' => 'NL123456789B01',
            'address_street' => 'Bouwstraat',
            'address_housenumber' => '42',
            'address_postcode' => '1234 AB',
            'address_city' => 'Amsterdam',
            'plan' => 'starter',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Workspace
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'name' => 'De Vries Bouw',
        ]);

        // Demo clients
        $client1 = Client::create([
            'workspace_id' => $workspace->id,
            'name' => 'Pieter Bakker',
            'email' => 'pieter@bakker.nl',
            'phone' => '06-98765432',
            'company' => 'Bakker Vastgoed',
            'address_street' => 'Keizersgracht',
            'address_housenumber' => '100',
            'address_postcode' => '1015 AA',
            'address_city' => 'Amsterdam',
        ]);

        $client2 = Client::create([
            'workspace_id' => $workspace->id,
            'name' => 'Maria Jansen',
            'email' => 'maria@jansen.nl',
            'phone' => '06-11223344',
            'address_street' => 'Herengracht',
            'address_housenumber' => '55',
            'address_postcode' => '1015 BG',
            'address_city' => 'Amsterdam',
        ]);

        // Demo project
        $project = Project::create([
            'workspace_id' => $workspace->id,
            'client_id' => $client1->id,
            'name' => 'Badkamer renovatie Keizersgracht',
            'description' => 'Complete badkamerrenovatie inclusief tegels, sanitair en leidingwerk.',
            'status' => 'active',
            'address' => 'Keizersgracht 100, Amsterdam',
        ]);

        // Demo entry (voice, completed)
        $entry = Entry::create([
            'workspace_id' => $workspace->id,
            'project_id' => $project->id,
            'type' => 'voice',
            'status' => 'final',
            'title' => 'Dagwerk badkamer 3 juni',
            'raw_transcript' => 'Vandaag gewerkt bij Bakker op de Keizersgracht. Badkamer tegels gezet, 12 vierkante meter. Gebruikt 3 zakken tegellijm. Ook de doucheafvoer aangesloten, dat was anderhalf uur extra werk. Materiaalkosten: tegellijm 45 euro, afvoerset 85 euro.',
            'ai_extracted_data' => [
                'beschrijving' => 'Dagwerk badkamerrenovatie - tegels zetten en doucheafvoer',
                'klant' => 'Bakker',
                'locatie' => 'Keizersgracht',
            ],
            'total_amount' => 695.00,
            'entry_date' => now()->toDateString(),
        ]);

        // Demo line items
        LineItem::create([
            'entry_id' => $entry->id,
            'description' => 'Tegels zetten badkamer',
            'quantity' => 12,
            'unit' => 'm2',
            'unit_price' => 35.00,
            'btw_rate' => 21.00,
            'total' => 420.00,
            'sort_order' => 1,
        ]);

        LineItem::create([
            'entry_id' => $entry->id,
            'description' => 'Doucheafvoer aansluiten',
            'quantity' => 1.5,
            'unit' => 'uur',
            'unit_price' => 55.00,
            'btw_rate' => 21.00,
            'total' => 82.50,
            'sort_order' => 2,
        ]);

        LineItem::create([
            'entry_id' => $entry->id,
            'description' => 'Tegellijm (3 zakken)',
            'quantity' => 3,
            'unit' => 'stuk',
            'unit_price' => 15.00,
            'btw_rate' => 21.00,
            'total' => 45.00,
            'sort_order' => 3,
        ]);

        LineItem::create([
            'entry_id' => $entry->id,
            'description' => 'Afvoerset douche',
            'quantity' => 1,
            'unit' => 'stuk',
            'unit_price' => 85.00,
            'btw_rate' => 21.00,
            'total' => 85.00,
            'sort_order' => 4,
        ]);
    }
}
