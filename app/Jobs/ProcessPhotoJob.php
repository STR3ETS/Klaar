<?php

namespace App\Jobs;

use App\Models\AiJob;
use App\Models\Entry;
use App\Services\ClaudeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 180;

    public function __construct(
        public Entry $entry,
    ) {}

    public function handle(ClaudeService $claude): void
    {
        $aiJob = AiJob::create([
            'entry_id' => $this->entry->id,
            'type' => 'ocr',
            'status' => 'processing',
            'provider' => 'anthropic-claude',
            'started_at' => now(),
        ]);

        try {
            $documents = $this->entry->documents()
                ->where('type', 'photo')
                ->get();

            if ($documents->isEmpty()) {
                throw new \RuntimeException('No photo documents found for entry.');
            }

            $allText = [];
            $totalTokens = 0;

            foreach ($documents as $document) {
                $result = $claude->extractFromPhoto(
                    $document->fullPath(),
                    $document->mime_type
                );

                $allText[] = $result['text'];
                $totalTokens += $result['usage']['total_tokens'] ?? 0;
            }

            // Combine OCR text from all photos
            $combinedText = implode("\n\n---\n\n", $allText);

            // Store OCR text as raw_transcript (reuse existing field)
            $this->entry->update([
                'raw_transcript' => $combinedText,
            ]);

            $aiJob->update([
                'status' => 'completed',
                'output' => ['ocr_text' => $combinedText, 'photo_count' => $documents->count()],
                'tokens_used' => $totalTokens,
                'completed_at' => now(),
            ]);

            // Chain to extraction job (reuse existing pipeline)
            ExtractEntryDataJob::dispatch($this->entry);

        } catch (\Throwable $e) {
            Log::error('Photo OCR processing failed', [
                'entry_id' => $this->entry->id,
                'error' => $e->getMessage(),
            ]);

            $aiJob->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            // Move to draft so user can edit manually
            $this->entry->update(['status' => 'draft']);

            throw $e;
        }
    }
}
