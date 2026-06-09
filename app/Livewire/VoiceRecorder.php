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
    public $entryId = null;

    /**
     * Fallback path: no Web Speech API available.
     * Uploads audio and sends to Whisper first, then Claude.
     */
    public function uploadAudio()
    {
        $this->validate([
            'audioFile' => 'required|file|max:51200',
        ]);

        $this->isUploading = true;

        $workspace = auth()->user()->currentWorkspace();

        $entry = Entry::create([
            'workspace_id' => $workspace->id,
            'type' => 'voice',
            'status' => 'processing',
            'entry_date' => now()->toDateString(),
        ]);

        $path = $this->audioFile->store(
            "voice/{$workspace->id}",
            'local'
        );

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

        TranscribeAudioJob::dispatch($entry, $path);

        $this->entryId = $entry->id;
        $this->isUploading = false;

        // Redirect to entry page (processing happens in background via queue)
        return $this->redirect(route('werkbonnen.show', $entry->id));
    }

    public function render()
    {
        return view('livewire.voice-recorder');
    }
}
