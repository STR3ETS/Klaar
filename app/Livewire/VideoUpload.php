<?php

namespace App\Livewire;

use App\Jobs\ProcessVideoJob;
use App\Models\Document;
use App\Models\Entry;
use Livewire\Component;
use Livewire\WithFileUploads;

class VideoUpload extends Component
{
    use WithFileUploads;

    public $video = null;
    public $isUploading = false;
    public $uploadComplete = false;
    public $entryId = null;

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

        ProcessVideoJob::dispatch($entry);

        $this->entryId = $entry->id;
        $this->isUploading = false;
        $this->uploadComplete = true;
    }

    public function render()
    {
        return view('livewire.video-upload');
    }
}
