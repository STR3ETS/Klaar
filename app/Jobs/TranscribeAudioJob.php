<?php

namespace App\Jobs;

use App\Models\AiJob;
use App\Models\Entry;
use App\Services\WhisperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranscribeAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 180;

    public function __construct(
        public Entry $entry,
        public string $audioPath,
    ) {}

    public function handle(WhisperService $whisper): void
    {
        $aiJob = AiJob::create([
            'entry_id' => $this->entry->id,
            'type' => 'transcription',
            'status' => 'processing',
            'provider' => 'openai-whisper',
            'input_path' => $this->audioPath,
            'started_at' => now(),
        ]);

        try {
            $result = $whisper->transcribe($this->audioPath);

            // Save transcript to entry
            $this->entry->update([
                'raw_transcript' => $result['text'],
            ]);

            // Update AI job record
            $aiJob->update([
                'status' => 'completed',
                'output' => $result,
                'completed_at' => now(),
            ]);

            // Dispatch extraction job
            ExtractEntryDataJob::dispatch($this->entry);

        } catch (\Throwable $e) {
            Log::error('Transcription failed', [
                'entry_id' => $this->entry->id,
                'error' => $e->getMessage(),
            ]);

            $aiJob->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            // Mark entry as failed so the user knows
            $this->entry->update(['status' => 'draft']);

            throw $e;
        }
    }
}
