<?php

namespace App\Livewire;

use App\Jobs\ProcessVideoJob;
use App\Models\Document;
use App\Models\Entry;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithFileUploads;

class VideoUpload extends Component
{
    use WithFileUploads;

    public $video = null;
    public $isUploading = false;
    public $uploadComplete = false;
    public $entryId = null;

    // Processing progress
    public string $processingStep = 'uploading';
    public bool $processingDone = false;
    public bool $processingFailed = false;

    // Results — supports multiple entries
    public array $processedEntries = [];

    public function updatedVideo()
    {
        $this->validate([
            'video' => 'required|file|max:204800|mimes:mp4,webm,mov,mkv',
        ]);
    }

    public function removeVideo()
    {
        $this->video = null;
    }

    public function uploadVideo()
    {
        $this->validate([
            'video' => 'required|file|max:204800|mimes:mp4,webm,mov,mkv',
        ]);

        $this->isUploading = true;

        $workspace = auth()->user()->currentWorkspace();

        $entry = Entry::create([
            'workspace_id' => $workspace->id,
            'type' => 'video',
            'status' => 'processing',
            'entry_date' => now()->toDateString(),
        ]);

        $path = $this->video->store(
            "videos/{$workspace->id}",
            'local'
        );

        Document::create([
            'documentable_type' => Entry::class,
            'documentable_id' => $entry->id,
            'type' => 'video',
            'disk' => 'local',
            'path' => $path,
            'original_name' => $this->video->getClientOriginalName(),
            'mime_type' => $this->video->getMimeType(),
            'size' => $this->video->getSize(),
        ]);

        Cache::put("video_processing_{$entry->id}", 'uploading', now()->addMinutes(30));

        ProcessVideoJob::dispatch($entry);

        $this->entryId = $entry->id;
        $this->isUploading = false;
        $this->uploadComplete = true;
        $this->processingStep = 'uploading';
    }

    public function pollStatus()
    {
        if (!$this->entryId || $this->processingDone) {
            return;
        }

        $step = Cache::get("video_processing_{$this->entryId}", 'uploading');
        $this->processingStep = $step;

        if ($step === 'done') {
            $this->processingDone = true;
            $this->loadProcessedEntries();
            Cache::forget("video_processing_{$this->entryId}");
        } elseif ($step === 'failed') {
            $this->processingFailed = true;
            Cache::forget("video_processing_{$this->entryId}");
        } else {
            // Fallback: check if entry status changed to draft
            $entry = Entry::find($this->entryId);
            if ($entry && $entry->status === 'draft' && $entry->title) {
                $this->processingDone = true;
                $this->loadProcessedEntries();
                Cache::forget("video_processing_{$this->entryId}");
            }
        }
    }

    private function loadProcessedEntries(): void
    {
        $workspace = auth()->user()->currentWorkspace();

        // Load the original entry
        $originalEntry = Entry::with('client')->find($this->entryId);
        if (!$originalEntry) {
            return;
        }

        $entries = collect([$originalEntry]);

        // Find any additional entries created from this video
        // (same workspace, type=video, created within 2 min of original, same transcript)
        if ($originalEntry->raw_transcript) {
            $additionalEntries = Entry::with('client')
                ->where('workspace_id', $workspace->id)
                ->where('type', 'video')
                ->where('id', '!=', $originalEntry->id)
                ->where('raw_transcript', $originalEntry->raw_transcript)
                ->where('created_at', '>=', $originalEntry->created_at)
                ->where('created_at', '<=', $originalEntry->created_at->addMinutes(2))
                ->get();

            $entries = $entries->merge($additionalEntries);
        }

        $this->processedEntries = $entries->map(fn ($e) => [
            'id' => $e->id,
            'title' => $e->title ?? 'Werkbon',
            'total' => $e->total_amount ? number_format((float) $e->total_amount, 2, ',', '.') : null,
            'client' => $e->client ? [
                'name' => $e->client->name,
                'is_new' => $e->client->created_at->diffInMinutes(now()) < 5,
            ] : null,
        ])->toArray();
    }

    public function resetUpload()
    {
        $this->video = null;
        $this->isUploading = false;
        $this->uploadComplete = false;
        $this->entryId = null;
        $this->processingStep = 'uploading';
        $this->processingDone = false;
        $this->processingFailed = false;
        $this->processedEntries = [];
    }

    public function render()
    {
        return view('livewire.video-upload');
    }
}
