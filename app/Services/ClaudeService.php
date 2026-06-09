<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ClaudeService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.anthropic.com/v1';
    protected string $model = 'claude-sonnet-4-20250514';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key', '');
    }

    /**
     * Clean up raw speech recognition text into proper Dutch sentences.
     * Preserves meaning and structure, only fixes grammar/punctuation.
     */
    public function cleanTranscript(string $rawText): string
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $response = Http::timeout(15)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'system' => <<<'SYS'
Je bent een transcriptie-editor voor een Nederlandse bouwvakker/aannemer. Je ontvangt ruwe spraakherkenning-output en maakt er correcte, leesbare tekst van.

Wat je MOET doen:
- Corrigeer spraakherkenningsfouten (bijv. "vloerbecke" → "vloerbedekking", "kitte" → "kitten", "stuc" → "stucwerk")
- Voeg interpunctie toe: punten, komma's, hoofdletters
- Maak er volledige zinnen van die logisch lezen
- Bouwvakjargon is prima, maar het moet correct gespeld zijn

Wat je NIET mag doen:
- Informatie toevoegen die niet in de originele tekst staat
- De volgorde van de inhoud veranderen
- Uitleg of commentaar geven

Retourneer ALLEEN de opgeschoonde tekst.
SYS,
                'messages' => [
                    ['role' => 'user', 'content' => "Ruwe spraakherkenning:\n\n{$rawText}"],
                ],
            ]);

        if ($response->failed()) {
            // On failure, return original text — don't block the flow
            return $rawText;
        }

        return trim($response->json('content.0.text', $rawText));
    }

    /**
     * Apply a spoken correction to an existing transcript.
     * E.g. original: "...vijf vierkante meter..." + correction: "nee 50 m²" → replaces in-place.
     */
    public function applyCorrection(string $original, string $correction): string
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $response = Http::timeout(15)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'system' => <<<'SYS'
Je bent een tekst-editor. Je ontvangt een tekst en een gesproken correctie van de gebruiker.

De correctie kan dingen bevatten als:
- "nee, dat moet 50 zijn" → pas het relevante getal aan
- "niet vloerbedekking maar laminaat" → vervang het woord
- "verwijder het stuk over de keuken" → verwijder die passage
- "voeg toe: ook de gang gedaan" → voeg het toe op de logische plek

Pas de correctie toe op de originele tekst. Behoud de rest van de tekst exact. Retourneer ALLEEN de aangepaste tekst, geen uitleg.
SYS,
                'messages' => [
                    ['role' => 'user', 'content' => "Originele tekst:\n{$original}\n\nCorrectie:\n{$correction}"],
                ],
            ]);

        if ($response->failed()) {
            return $original;
        }

        return trim($response->json('content.0.text', $original));
    }

    /**
     * Extract structured entry data from a transcript.
     * Supports multiple entries when the user describes multiple jobs.
     *
     * @param string $transcript Raw transcript text
     * @return array{extracted: array{entries: array}, usage: array}
     */
    public function extractEntryData(string $transcript): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $today = now()->toDateString();

        $systemPrompt = <<<PROMPT
Je bent een administratie-assistent voor een Nederlandse aannemer/ZZP'er in de bouw.
Je ontvangt een transcript van een ingesproken werkregistratie.
Extraheer gestructureerde gegevens en retourneer ALLEEN geldige JSON.

BELANGRIJK: De aannemer kan meerdere klussen of werkdagen beschrijven in één opname.
Maak voor ELKE aparte klus of werkdag een apart entry-object aan.
Voorbeelden van meerdere entries:
- "Ik heb drie dagen gewerkt" → 3 entries
- "Maandag deed ik X en dinsdag deed ik Y" → 2 entries
- "Ik heb vloerbedekking en schilderwerk gedaan" op dezelfde dag → 1 entry met 2 regelitems (tenzij expliciet apart gefactureerd)

Regels:
- Alle bedragen in euro's
- BTW-tarief standaard 21% tenzij anders vermeld
- Eenheden: "uur", "stuk", "m2", "m1", "dag", "post"
- Splits werkzaamheden en materialen in aparte regelitems
- Als de aannemer een klant of project noemt, geef dat mee als hint
- Genereer een beknopte titel per entry
- entry_date: gebruik de genoemde datum of null als geen datum genoemd wordt. Vandaag is {$today}.

Retourneer dit exacte JSON-formaat:
{
  "entries": [
    {
      "title": "korte titel van het werk",
      "description": "samenvatting van het werk",
      "entry_date": "YYYY-MM-DD of null",
      "line_items": [
        {
          "description": "omschrijving",
          "quantity": 1.0,
          "unit": "uur",
          "unit_price": 0.00,
          "btw_rate": 21.00
        }
      ],
      "client_hint": "naam klant of null",
      "project_hint": "naam project of null"
    }
  ]
}
PROMPT;

        $response = Http::timeout(60)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => $this->model,
                'max_tokens' => 4096,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Verwerk dit transcript:\n\n{$transcript}",
                    ],
                ],
            ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error');
            throw new RuntimeException("Claude API failed: {$error}");
        }

        $data = $response->json();
        $content = $data['content'][0]['text'] ?? '';

        // Extract JSON from the response (handle markdown code blocks)
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $parsed = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Claude response was not valid JSON: ' . json_last_error_msg());
        }

        // Backward compatibility: wrap old format (no 'entries' key) in array
        if (!isset($parsed['entries'])) {
            $parsed = ['entries' => [$parsed]];
        }

        // Calculate usage for cost tracking
        $inputTokens = $data['usage']['input_tokens'] ?? 0;
        $outputTokens = $data['usage']['output_tokens'] ?? 0;

        return [
            'extracted' => $parsed,
            'usage' => [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $inputTokens + $outputTokens,
            ],
        ];
    }

    /**
     * Extract text content from a photo using Claude's vision API.
     * Reads the image, sends as base64 to Claude, and returns OCR text.
     *
     * @param string $imagePath Absolute path to the image file
     * @param string $mimeType MIME type (image/jpeg, image/png, etc.)
     * @return array{text: string, usage: array}
     */
    public function extractFromPhoto(string $imagePath, string $mimeType): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        // Claude vision supports jpeg, png, gif, webp — convert unsupported types
        $supportedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $supportedTypes)) {
            $mimeType = 'image/jpeg';
        }

        $base64 = base64_encode(file_get_contents($imagePath));

        $response = Http::timeout(60)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => $this->model,
                'max_tokens' => 4096,
                'system' => <<<'SYS'
Je bent een OCR-assistent voor een Nederlandse aannemer/ZZP'er in de bouw.
Je ontvangt een foto van een werkbon, bon, materiaallijst, pakbon, offerte, of ander werkdocument.

Jouw taak:
- Lees ALLE tekst op de foto nauwkeurig
- Structureer de informatie logisch (klant, datum, items, bedragen, hoeveelheden, etc.)
- Geef de tekst terug als leesbare, gestructureerde tekst in het Nederlands
- Behoud alle getallen, bedragen, hoeveelheden en eenheden exact zoals ze op de foto staan
- Als je een tabel of lijst ziet, behoud de structuur
- Als je iets niet kunt lezen, geef dat aan met [onleesbaar]

Retourneer ALLEEN de geëxtraheerde tekst, geen uitleg of commentaar.
SYS,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'image',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => $mimeType,
                                    'data' => $base64,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'text' => 'Lees alle tekst op deze foto en structureer de informatie.',
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error');
            throw new RuntimeException("Claude Vision API failed: {$error}");
        }

        $data = $response->json();
        $text = $data['content'][0]['text'] ?? '';
        $inputTokens = $data['usage']['input_tokens'] ?? 0;
        $outputTokens = $data['usage']['output_tokens'] ?? 0;

        return [
            'text' => trim($text),
            'usage' => [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $inputTokens + $outputTokens,
            ],
        ];
    }

    /**
     * Analyze video keyframes using Claude Vision.
     * Sends multiple frame images in a single API call and returns
     * a combined visual analysis of the project site.
     *
     * @param array<string> $framePaths Absolute paths to JPEG keyframe images
     * @return array{text: string, usage: array}
     */
    public function analyzeVideoFrames(array $framePaths): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        if (empty($framePaths)) {
            return ['text' => '', 'usage' => ['input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0]];
        }

        $content = [];

        foreach ($framePaths as $path) {
            if (!file_exists($path)) {
                continue;
            }

            $content[] = [
                'type' => 'image',
                'source' => [
                    'type' => 'base64',
                    'media_type' => 'image/jpeg',
                    'data' => base64_encode(file_get_contents($path)),
                ],
            ];
        }

        if (empty($content)) {
            return ['text' => '', 'usage' => ['input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0]];
        }

        $content[] = [
            'type' => 'text',
            'text' => 'Analyseer deze beelden uit een video-opname van een bouwproject. Beschrijf wat je ziet.',
        ];

        $response = Http::timeout(120)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => $this->model,
                'max_tokens' => 4096,
                'system' => <<<'SYS'
Je bent een visuele analyse-assistent voor een Nederlandse aannemer/ZZP'er in de bouw.
Je ontvangt keyframes (beelden) uit een video-opname van een bouwproject of werklocatie.

Jouw taak:
- Beschrijf wat je ziet op de werklocatie: ruimtes, materialen, gereedschap, staat van het werk
- Let op bouwkundige details: type vloer, wanden, plafond, installaties, leidingen
- Herken materialen: hout, tegels, stuc, verf, isolatie, laminaat, etc.
- Noteer afmetingen, merken of labels als je die kunt lezen
- Beschrijf de voortgang van het werk: wat is klaar, wat moet nog gedaan worden
- Noem zichtbare problemen: schade, lekkage, scheuren, slijtage
- Geef een gestructureerd overzicht, niet per frame maar als geheel

Schrijf in het Nederlands. Wees beknopt maar volledig. Focus op informatie die relevant is voor een werkbon of offerte.
SYS,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $content,
                    ],
                ],
            ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error');
            throw new RuntimeException("Claude Vision API failed: {$error}");
        }

        $data = $response->json();
        $text = $data['content'][0]['text'] ?? '';
        $inputTokens = $data['usage']['input_tokens'] ?? 0;
        $outputTokens = $data['usage']['output_tokens'] ?? 0;

        return [
            'text' => trim($text),
            'usage' => [
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $inputTokens + $outputTokens,
            ],
        ];
    }

    /**
     * Classify the intent of a voice transcript.
     * Determines whether the user wants to create an entry, client, or project.
     */
    public function classifyVoiceIntent(string $transcript): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $response = Http::timeout(10)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 256,
                'system' => <<<'SYS'
Je classificeert Nederlandse spraak-input voor een administratie-app voor vakmensen/aannemers.

Bepaal ALLE acties die de gebruiker wil uitvoeren. Een zin kan meerdere acties bevatten.

Actietypes:
- "create_entry": de gebruiker wil NIEUW werk registreren (werkbon aanmaken). DEFAULT als iemand werk beschrijft.
- "create_client": nieuwe klant/relatie aanmaken met contactgegevens
- "create_project": nieuw project/opdracht aanmaken
- "command": een actie uitvoeren op BESTAANDE of ZOJUIST AANGEMAAKTE items (definitief maken, factureren, verwijderen, heropenen)

BELANGRIJK — voorbeelden van meerdere intents:
- "Maak een werkbon voor schilderwerk" → ["create_entry"]
- "Maak een werkbon en zet hem definitief" → ["create_entry", "command"]
- "Ik heb geschilderd bij Jansen, maak definitief en factureer" → ["create_entry", "command"]
- "Maak een werkbon, zet definitief, factureer en maak een project aan" → ["create_entry", "command", "create_project"]
- "Zet de laatste werkbon om naar factuur" → ["command"]
- "Maak een klant aan: Jan de Boer" → ["create_client"]
- "Ik heb geverfd, maak ook een project aan" → ["create_entry", "create_project"]

Retourneer ALLEEN geldige JSON:
{"intents": ["create_entry"]}

De array moet in uitvoeringsvolgorde staan:
1. Eerst create_entry of create_client (items aanmaken)
2. Dan command (acties op bestaande/zojuist aangemaakte items)
3. Dan create_project (koppelen aan werkbon)

Retourneer minimaal 1 intent. Meerdere intents alleen als de gebruiker ECHT meerdere acties wil.
SYS,
                'messages' => [
                    ['role' => 'user', 'content' => $transcript],
                ],
            ]);

        if ($response->failed()) {
            return ['intent' => 'create_entry'];
        }

        $content = trim($response->json('content.0.text', ''));
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $parsed = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['intent' => 'create_entry'];
        }

        // Support both old format {"intent": "..."} and new format {"intents": [...]}
        if (isset($parsed['intents']) && is_array($parsed['intents']) && !empty($parsed['intents'])) {
            return ['intents' => $parsed['intents']];
        }

        return ['intents' => [$parsed['intent'] ?? 'create_entry']];
    }

    /**
     * Extract client data from a voice transcript.
     */
    public function extractClientData(string $transcript): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $response = Http::timeout(15)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'system' => <<<'SYS'
Je extraheert klantgegevens uit een Nederlands spraaktranscript voor een administratie-app voor vakmensen.

Extraheer alle genoemde informatie en retourneer ALLEEN geldige JSON:
{
  "type": "particulier of zakelijk",
  "name": "volledige naam",
  "email": "e-mailadres of null",
  "phone": "telefoonnummer of null",
  "company": "bedrijfsnaam of null",
  "address_street": "straatnaam of null",
  "address_housenumber": "huisnummer of null",
  "address_postcode": "postcode of null",
  "address_city": "plaatsnaam of null",
  "kvk_number": "KVK-nummer of null",
  "btw_number": "BTW-nummer of null",
  "notes": "eventuele extra informatie of null"
}

Regels:
- "type" is "zakelijk" als er een bedrijfsnaam, KVK of BTW wordt genoemd, anders "particulier"
- Telefoonnummers in Nederlands formaat (06-12345678)
- Postcodes in formaat 1234 AB
- Laat velden null als de info niet genoemd wordt
- "name" is verplicht — als alleen een bedrijfsnaam wordt genoemd, gebruik die als name
SYS,
                'messages' => [
                    ['role' => 'user', 'content' => "Transcript:\n\n{$transcript}"],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Claude API failed for client extraction.');
        }

        $content = trim($response->json('content.0.text', ''));
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $parsed = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON from Claude client extraction.');
        }

        return $parsed;
    }

    /**
     * Extract project data from a voice transcript.
     */
    public function extractProjectData(string $transcript, array $existingClients = []): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $clientsList = !empty($existingClients)
            ? "Bestaande klanten: " . implode(', ', $existingClients)
            : "Geen bestaande klanten.";

        $response = Http::timeout(15)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'system' => <<<SYS
Je extraheert projectgegevens uit een Nederlands spraaktranscript voor een administratie-app voor vakmensen.

{$clientsList}

Extraheer alle genoemde informatie en retourneer ALLEEN geldige JSON:
{
  "name": "projectnaam (kort, beschrijvend)",
  "description": "omschrijving van het project of null",
  "address": "projectlocatie/adres of null",
  "client_name": "naam van de klant (match met bestaande klant als mogelijk) of null"
}

Regels:
- "name" is verplicht — maak een korte beschrijvende naam (bijv. "Badkamer renovatie Jansen", "Dakisolatie Kerkstraat")
- Als een klant wordt genoemd, probeer te matchen met de bestaande klantenlijst
- "address" is het projectadres, niet het adres van de klant
SYS,
                'messages' => [
                    ['role' => 'user', 'content' => "Transcript:\n\n{$transcript}"],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Claude API failed for project extraction.');
        }

        $content = trim($response->json('content.0.text', ''));
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $parsed = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON from Claude project extraction.');
        }

        return $parsed;
    }

    /**
     * Interpret a voice command about existing entries.
     * Returns structured actions to perform (finalize, delete, reopen).
     */
    public function interpretCommand(string $command, array $entries): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $entriesJson = json_encode($entries, JSON_UNESCAPED_UNICODE);
        $today = now()->toDateString();

        $systemPrompt = <<<PROMPT
Je bent een assistent voor een Nederlandse aannemer. Je interpreteert gesproken commando's over werkbonnen (entries) en retourneert gestructureerde acties.

Beschikbare acties:
- "finalize" — markeer entries als definitief (alleen entries met status "draft")
- "delete" — verwijder entries
- "reopen" — zet definitieve entries terug naar concept (alleen entries met status "final")
- "convert_to_invoice" — maak een factuur van werkbon(nen) (alleen entries met status "final" EN een client_id)

De gebruiker kan entries aanduiden op basis van:
- Klantnaam (bijv. "werkbonnen van Marieke Jansen")
- Titel (bijv. "werkbon vloerbedekking")
- Datum (bijv. "werkbon van gisteren")
- Combinaties (bijv. "alle concepten van Marieke")

Vandaag is {$today}.

Retourneer ALLEEN geldige JSON in dit formaat:
{
  "actions": [
    {"type": "finalize", "entry_ids": [12, 15]},
    {"type": "delete", "entry_ids": [8]}
  ],
  "message": "Korte Nederlandse bevestiging van wat er gedaan wordt."
}

Regels:
- Koppel het commando aan de juiste entries op basis van de entry-lijst
- Als je geen match vindt, retourneer lege actions en leg uit waarom
- Gebruik ALLEEN entry IDs die in de lijst staan
- Wees voorzichtig: bij twijfel, vraag bevestiging via het message-veld
- "Definitief markeren" = finalize, "verwijderen" = delete, "terugzetten" = reopen
- Entries met "just_created: true" zijn ZOJUIST aangemaakt in dezelfde spraak-opdracht. Als de gebruiker "deze", "hem", "die", "het" zegt zonder specifieke naam, bedoelt hij/zij de zojuist aangemaakte entry(s)
- Retourneer acties in logische volgorde: finalize VÓÓR convert_to_invoice (een entry moet eerst definitief zijn voordat er een factuur van gemaakt kan worden)
PROMPT;

        $response = Http::timeout(15)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Huidige werkbonnen:\n{$entriesJson}\n\nCommando:\n{$command}",
                    ],
                ],
            ]);

        if ($response->failed()) {
            return [
                'actions' => [],
                'message' => 'Er ging iets mis bij het verwerken van je commando.',
            ];
        }

        $content = trim($response->json('content.0.text', ''));

        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $content, $matches)) {
            $content = trim($matches[1]);
        }

        $parsed = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'actions' => [],
                'message' => 'Ik begreep je commando niet. Probeer het opnieuw.',
            ];
        }

        return [
            'actions' => $parsed['actions'] ?? [],
            'message' => $parsed['message'] ?? 'Commando verwerkt.',
        ];
    }
}
