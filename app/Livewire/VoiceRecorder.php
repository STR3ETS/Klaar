<?php

namespace App\Livewire;

use App\Jobs\TranscribeAudioJob;
use App\Models\Document;
use App\Models\Entry;
use Livewire\Component;
use Livewire\WithFileUploads;

class VoiceRecorder extends Component
{
    use WithFileUploads;

    public $audioFile;
    public $isUploading = false;
    public $uploadComplete = false;
    public $entryId = null;

    public function uploadAudio()
    {
        $this->validate([
            'audioFile' => 'required|file|max:51200', // 50MB max
        ]);

        $this->isUploading = true;

        $workspace = auth()->user()->currentWorkspace();

        // Create entry
        $entry = Entry::create([
            'workspace_id' => $workspace->id,
            'type' => 'voice',
            'status' => 'processing',
            'entry_date' => now()->toDateString(),
        ]);

        // Store audio file
        $path = $this->audioFile->store(
            "voice/{$workspace->id}",
            'local'
        );

        // Create document record
        Document::create([
            'documentable_type' => Entry::class,
            'documentable_id' => $entry->id,
            'type' => 'voice',
            'disk' => 'local',
            'path' => $path,
            'original_name' => $this->audioFile->getClientOriginalName(),
            'mime_type' => $this->audioFile->getMimeType(),
            'size' => $this->audioFile->getSize(),
        ]);

        // Dispatch transcription job
        TranscribeAudioJob::dispatch($entry, $path);

        $this->entryId = $entry->id;
        $this->isUploading = false;
        $this->uploadComplete = true;
    }

    public function render()
    {
        return view('livewire.voice-recorder');
    }
}
