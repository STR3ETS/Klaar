<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Entry;
use Livewire\Component;
use Livewire\WithFileUploads;

class PhotoUpload extends Component
{
    use WithFileUploads;

    public $photos = [];
    public $isUploading = false;
    public $uploadComplete = false;
    public $entryId = null;

    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:20480', // 20MB per photo
        ]);
    }

    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function uploadPhotos()
    {
        $this->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:20480',
        ]);

        $this->isUploading = true;

        $workspace = auth()->user()->currentWorkspace();

        $entry = Entry::create([
            'workspace_id' => $workspace->id,
            'type' => 'photo',
            'status' => 'processing',
            'entry_date' => now()->toDateString(),
        ]);

        foreach ($this->photos as $photo) {
            $path = $photo->store(
                "photos/{$workspace->id}",
                'local'
            );

            Document::create([
                'documentable_type' => Entry::class,
                'documentable_id' => $entry->id,
                'type' => 'photo',
                'disk' => 'local',
                'path' => $path,
                'original_name' => $photo->getClientOriginalName(),
                'mime_type' => $photo->getMimeType(),
                'size' => $photo->getSize(),
            ]);
        }

        // TODO: Dispatch OCR job when Phase 2 is built
        // ProcessPhotoJob::dispatch($entry);

        $this->entryId = $entry->id;
        $this->isUploading = false;
        $this->uploadComplete = true;
    }

    public function render()
    {
        return view('livewire.photo-upload');
    }
}
