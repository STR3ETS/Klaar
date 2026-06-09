<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->user()->currentWorkspace();
        $projects = $workspace->projects()
            ->with('client')
            ->withCount('entries')
            ->withSum('entries', 'total_amount')
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'completed' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(20);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function show(Request $request, Project $project)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($project->workspace_id === $workspace->id, 403);

        $project->load('client');
        $project->loadCount('entries');
        $entries = $project->entries()->with('client')->latest()->paginate(20);

        // Invoices linked through entries of this project
        $invoices = \App\Models\Invoice::whereIn('entry_id', $project->entries()->select('id'))
            ->with('client')
            ->latest()
            ->get();

        return view('projects.show', compact('project', 'entries', 'invoices'));
    }

    public function edit(Request $request, Project $project)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($project->workspace_id === $workspace->id, 403);

        return view('projects.edit', compact('project'));
    }

    public function destroy(Request $request, Project $project)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($project->workspace_id === $workspace->id, 403);

        $project->entries()->update(['project_id' => null]);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project verwijderd.');
    }
}
