<?php

namespace App\Jobs;

use App\Models\AiJob;
use App\Models\Entry;
use App\Models\LineItem;
use App\Services\ClaudeService;
use App\Services\FFmpegService;
use App\Services\WhisperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
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
        $this->setStep('extracting_audio');

        $aiJob = AiJob::create([
            'entry_id' => $this->entry->id,
            'type' => 'video_processing',
            'status' => 'processing',
            'provider' => 'ffmpeg+openai+anthropic',
            'started_at' => now(),
        ]);

        $audioPath = null;
        $audioStoragePath = null;
        $frames = [];

        try {
            $document = $this->entry->documents()
                ->where('type', 'video')
                ->firstOrFail();

            $videoPath = $document->fullPath();
            $workspace = $this->entry->workspace;

            // Step 1: Extract audio track
            $audioPath = $ffmpeg->extractAudio($videoPath);

            // Step 2: Transcribe audio via Whisper
            $this->setStep('transcribing');
            $audioStoragePath = "videos/{$workspace->id}/audio_" . uniqid() . '.mp3';
            Storage::disk('local')->put(
                $audioStoragePath,
                file_get_contents($audioPath)
            );

            $transcription = $whisper->transcribe($audioStoragePath);
            $transcript = $transcription['text'] ?? '';
            $segments = $transcription['segments'] ?? [];

            // Step 3: Extract keyframes (with timestamps)
            $this->setStep('extracting_frames');
            $frames = $ffmpeg->extractKeyframes($videoPath);

            // Step 4: Analyze keyframes with Claude Vision + transcript correlation
            $this->setStep('analyzing_frames');
            $visualAnalysis = '';
            $visionTokens = 0;

            if (!empty($frames)) {
                $result = $claude->analyzeVideoFrames($frames, $segments);
                $visualAnalysis = $result['text'];
                $visionTokens = $result['usage']['total_tokens'] ?? 0;
            }

            // Step 5: Build combined transcript and extract structured data
            $this->setStep('generating_entry');
            $combinedTranscript = $this->buildCombinedTranscript($transcript, $visualAnalysis);

            $this->entry->update([
                'raw_transcript' => $combinedTranscript,
            ]);

            // Step 6: Extract structured entry data (same as voice process-entry)
            $extractResult = $claude->extractEntryData($combinedTranscript);
            $entriesData = $extractResult['extracted']['entries'] ?? [];
            $extractionTokens = $extractResult['usage']['total_tokens'] ?? 0;

            if (empty($entriesData)) {
                // Fallback: no structured data extracted, just save as draft
                $this->entry->update(['status' => 'draft']);
            } else {
                // Process all extracted entries
                $this->createEntries($entriesData, $workspace, $combinedTranscript);
            }

            // Store metadata on document
            $keyframePaths = array_column($frames, 'path');
            $document->update([
                'meta' => [
                    'duration' => $transcription['duration'] ?? null,
                    'keyframe_count' => count($frames),
                    'has_audio' => !empty(trim($transcript)),
                    'has_visual' => !empty(trim($visualAnalysis)),
                ],
            ]);

            $aiJob->update([
                'status' => 'completed',
                'output' => [
                    'transcript_length' => strlen($transcript),
                    'visual_analysis_length' => strlen($visualAnalysis),
                    'keyframe_count' => count($frames),
                    'duration' => $transcription['duration'] ?? 0,
                    'entries_created' => count($entriesData),
                ],
                'tokens_used' => $visionTokens + $extractionTokens,
                'completed_at' => now(),
            ]);

            // Cleanup temp files
            $this->cleanup($audioPath, $keyframePaths, $audioStoragePath);

            // Done
            $this->setStep('done');

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

            $this->setStep('failed');
            $this->entry->update(['status' => 'draft']);

            // Cleanup temp files even on failure
            $keyframePaths = array_column($frames, 'path');
            $this->cleanup($audioPath, $keyframePaths, $audioStoragePath);

            throw $e;
        }
    }

    /**
     * Create entries with client/project matching — same logic as process-entry API.
     */
    private function createEntries(array $entriesData, $workspace, string $combinedTranscript): void
    {
        foreach ($entriesData as $idx => $entryData) {
            $clientId = null;
            $projectId = null;

            // Auto-match or create client
            $clientHint = $entryData['client_hint'] ?? null;
            if ($clientHint && trim($clientHint) !== '') {
                $client = $workspace->clients()
                    ->whereRaw('LOWER(name) = ?', [strtolower($clientHint)])
                    ->first();

                if ($client) {
                    $clientId = $client->id;
                } else {
                    $client = $workspace->clients()->create(['name' => $clientHint]);
                    $clientId = $client->id;
                }
            }

            // Auto-match project
            $projectHint = $entryData['project_hint'] ?? null;
            if ($projectHint && trim($projectHint) !== '') {
                $project = $workspace->projects()
                    ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(trim($projectHint)) . '%'])
                    ->first();

                if ($project) {
                    $projectId = $project->id;
                    if (!$clientId && $project->client_id) {
                        $clientId = $project->client_id;
                    }
                }
            }

            if ($idx === 0) {
                // Update the original entry (keeps video document link)
                $this->entry->update([
                    'client_id' => $clientId,
                    'project_id' => $projectId,
                    'title' => $entryData['title'] ?? 'Zonder titel',
                    'ai_extracted_data' => $entryData,
                    'entry_date' => $entryData['entry_date'] ?? now()->toDateString(),
                    'status' => 'draft',
                ]);

                $this->createLineItems($this->entry, $entryData['line_items'] ?? []);
            } else {
                // Create additional entries for extra jobs
                $entry = Entry::create([
                    'workspace_id' => $workspace->id,
                    'client_id' => $clientId,
                    'project_id' => $projectId,
                    'type' => 'video',
                    'status' => 'draft',
                    'entry_date' => $entryData['entry_date'] ?? now()->toDateString(),
                    'raw_transcript' => $combinedTranscript,
                    'title' => $entryData['title'] ?? 'Zonder titel',
                    'ai_extracted_data' => $entryData,
                ]);

                $this->createLineItems($entry, $entryData['line_items'] ?? []);
            }
        }
    }

    private function createLineItems(Entry $entry, array $items): void
    {
        $totalAmount = 0;

        foreach ($items as $i => $item) {
            $lineTotal = (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
            $totalAmount += $lineTotal;

            LineItem::create([
                'entry_id' => $entry->id,
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit' => $item['unit'] ?? 'stuk',
                'unit_price' => $item['unit_price'] ?? 0,
                'btw_rate' => $item['btw_rate'] ?? 21.00,
                'total' => $lineTotal,
                'sort_order' => $i + 1,
            ]);
        }

        $entry->update(['total_amount' => $totalAmount]);
    }

    private function setStep(string $step): void
    {
        Cache::put("video_processing_{$this->entry->id}", $step, now()->addMinutes(30));
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
