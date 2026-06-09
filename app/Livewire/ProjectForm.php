<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectForm extends Component
{
    public ?Project $project = null;

    public string $name = '';
    public string $description = '';
    public string $status = 'active';
    public string $address = '';
    public ?int $client_id = null;
    public bool $isSaving = false;

    public function mount(?Project $project = null)
    {
        if ($project && $project->exists) {
            $workspace = auth()->user()->currentWorkspace();
            abort_unless($project->workspace_id === $workspace->id, 403);

            $this->project = $project;
            $this->name = $project->name ?? '';
            $this->description = $project->description ?? '';
            $this->status = $project->status ?? 'active';
            $this->address = $project->address ?? '';
            $this->client_id = $project->client_id;
        }
    }

    public function fillFromVoice($data)
    {
        if (!empty($data['name'])) $this->name = $data['name'];
        if (!empty($data['description'])) $this->description = $data['description'];
        if (!empty($data['address'])) $this->address = $data['address'];
        if (!empty($data['client_id'])) $this->client_id = (int) $data['client_id'];
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,archived',
            'address' => 'nullable|string|max:500',
            'client_id' => 'nullable|exists:clients,id',
        ]);

        $this->isSaving = true;

        $data = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'address' => $this->address ?: null,
            'client_id' => $this->client_id,
        ];

        if ($this->project && $this->project->exists) {
            $this->project->update($data);
            $project = $this->project;
        } else {
            $workspace = auth()->user()->currentWorkspace();
            $data['workspace_id'] = $workspace->id;
            $project = Project::create($data);
        }

        $this->isSaving = false;

        return redirect()->route('projects.show', $project);
    }

    public function render()
    {
        $clients = auth()->user()->currentWorkspace()->clients()->orderBy('name')->get();
        return view('livewire.project-form', compact('clients'));
    }
}
