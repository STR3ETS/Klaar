<?php

namespace App\Jobs;

use App\Models\AiJob;
use App\Models\Entry;
use App\Services\ClaudeService;
use App\Services\FFmpegService;
use App\Services\WhisperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 600;

    public function __construct(
        public Entry $entry,
    ) {}

    public function handle(
        FFmpegService $ffmpeg,
        WhisperService $whisper,
        ClaudeService $claude,
    ): void {
        $aiJob = AiJob::create([
            'entry_id' => $this->entry->id,
            'type' => 'video_processing',
            'status' => 'processing',
            'provider' => 'ffmpeg+openai+anthropic',
            'started_at' => now(),
        ]);

        $audioPath = null;
        $audioStoragePath = null;
        $keyframePaths = [];

        try {
            $document = $this->entry->documents()
                ->where('type', 'video')
                ->firstOrFail();

            $videoPath = $document->fullPath();

            // Step 1: Extract audio track
            $audioPath = $ffmpeg->extractAudio($videoPath);

            // Step 2: Transcribe audio via Whisper
            $workspace = $this->entry->workspace_id;
            $audioStoragePath = "videos/{$workspace}/audio_" . uniqid() . '.mp3';
            Storage::disk('local')->put(
                $audioStoragePath,
                file_get_contents($audioPath)
            );

            $transcription = $whisper->transcribe($audioStoragePath);
            $transcript = $transcription['text'] ?? '';

            // Step 3: Extract keyframes
            $keyframePaths = $ffmpeg->extractKeyframes($videoPath);

            // Step 4: Analyze keyframes with Claude Vision
            $visualAnalysis = '';
            $totalTokens = 0;

            if (!empty($keyframePaths)) {
                $result = $claude->analyzeVideoFrames($keyframePaths);
                $visualAnalysis = $result['text'];
                $totalTokens = $result['usage']['total_tokens'] ?? 0;
            }

            // Step 5: Combine transcript + visual analysis
            $combinedTranscript = $this->buildCombinedTranscript($transcript, $visualAnalysis);

            $this->entry->update([
                'raw_transcript' => $combinedTranscript,
            ]);

            // Store metadata on document
            $document->update([
                'meta' => [
                    'duration' => $transcription['duration'] ?? null,
                    'keyframe_count' => count($keyframePaths),
                    'has_audio' => !empty(trim($transcript)),
                    'has_visual' => !empty(trim($visualAnalysis)),
                ],
            ]);

            $aiJob->update([
                'status' => 'completed',
                'output' => [
                    'transcript_length' => strlen($transcript),
                    'visual_analysis_length' => strlen($visualAnalysis),
                    'keyframe_count' => count($keyframePaths),
                    'duration' => $transcription['duration'] ?? 0,
                ],
                'tokens_used' => $totalTokens,
                'completed_at' => now(),
            ]);

            // Cleanup temp files
            $this->cleanup($audioPath, $keyframePaths, $audioStoragePath);

            // Step 6: Chain to extraction (existing pipeline)
            ExtractEntryDataJob::dispatch($this->entry);

        } catch (\Throwable $e) {
            Log::error('Video processing failed', [
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

            // Cleanup temp files even on failure
            $this->cleanup($audioPath, $keyframePaths, $audioStoragePath);

            throw $e;
        }
    }

    private function buildCombinedTranscript(string $transcript, string $visualAnalysis): string
    {
        $parts = [];

        if (!empty(trim($transcript))) {
            $parts[] = "== GESPROKEN TEKST ==\n{$transcript}";
        }

        if (!empty(trim($visualAnalysis))) {
            $parts[] = "== VISUELE ANALYSE ==\n{$visualAnalysis}";
        }

        return implode("\n\n", $parts);
    }

    private function cleanup(?string $audioPath, array $keyframePaths, ?string $audioStoragePath): void
    {
        if ($audioPath && file_exists($audioPath)) {
            @unlink($audioPath);
        }

        foreach ($keyframePaths as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        // Remove the parent directory if it exists and is empty
        if (!empty($keyframePaths)) {
            $dir = dirname($keyframePaths[0]);
            if (is_dir($dir)) {
                @rmdir($dir);
            }
        }

        if ($audioStoragePath) {
            Storage::disk('local')->delete($audioStoragePath);
        }
    }
}
