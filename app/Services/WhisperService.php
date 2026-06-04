<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class WhisperService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
    }

    /**
     * Transcribe an audio file using OpenAI Whisper API.
     *
     * @param string $filePath Path relative to storage disk
     * @param string $disk Storage disk name
     * @return array{text: string, language: string, duration: float, segments: array}
     */
    public function transcribe(string $filePath, string $disk = 'local'): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        $fullPath = Storage::disk($disk)->path($filePath);

        if (!file_exists($fullPath)) {
            throw new RuntimeException("Audio file not found: {$filePath}");
        }

        $response = Http::timeout(120)
            ->withToken($this->apiKey)
            ->attach(
                'file',
                file_get_contents($fullPath),
                basename($fullPath)
            )
            ->post("{$this->baseUrl}/audio/transcriptions", [
                'model' => 'whisper-1',
                'language' => 'nl',
                'response_format' => 'verbose_json',
                'timestamp_granularities' => ['segment'],
            ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Unknown error');
            throw new RuntimeException("Whisper API failed: {$error}");
        }

        $data = $response->json();

        return [
            'text' => $data['text'] ?? '',
            'language' => $data['language'] ?? 'nl',
            'duration' => $data['duration'] ?? 0,
            'segments' => $data['segments'] ?? [],
        ];
    }
}
