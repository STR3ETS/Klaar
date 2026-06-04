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
     * Extract structured entry data from a transcript.
     *
     * @param string $transcript Raw transcript text
     * @return array{title: string, description: string, line_items: array, client_hint: string|null, project_hint: string|null}
     */
    public function extractEntryData(string $transcript): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Anthropic API key is not configured.');
        }

        $systemPrompt = <<<'PROMPT'
Je bent een administratie-assistent voor een Nederlandse aannemer/ZZP'er in de bouw.
Je ontvangt een transcript van een ingesproken werkregistratie.
Extraheer gestructureerde gegevens en retourneer ALLEEN geldige JSON.

Regels:
- Alle bedragen in euro's
- BTW-tarief standaard 21% tenzij anders vermeld
- Eenheden: "uur", "stuk", "m2", "m1", "dag", "post"
- Splits werkzaamheden en materialen in aparte regelitems
- Als de aannemer een klant of project noemt, geef dat mee als hint
- Genereer een beknopte titel

Retourneer dit exacte JSON-formaat:
{
  "title": "korte titel van het werk",
  "description": "samenvatting van het werk",
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
PROMPT;

        $response = Http::timeout(60)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post("{$this->baseUrl}/messages", [
                'model' => $this->model,
                'max_tokens' => 2048,
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
}
