<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->user()->currentWorkspace();

        $query = $workspace->entries()->with(['project', 'client']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }
        if ($request->filled('project')) {
            $query->where('project_id', $request->project);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $entries = $query->latest()->paginate(20)->withQueryString();

        $clients = $workspace->clients()->orderBy('name')->get();
        $projects = $workspace->projects()->where('status', 'active')->orderBy('name')->get();

        return view('werkbonnen.index', compact('entries', 'clients', 'projects'));
    }

    public function show(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);

        $entry->load(['lineItems', 'project', 'client', 'documents', 'aiJobs', 'invoices']);

        return view('werkbonnen.show', compact('entry'));
    }

    public function edit(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);
        abort_unless($entry->isDraft(), 403);

        return view('werkbonnen.edit', compact('entry'));
    }

    public function finalize(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);
        abort_unless($entry->isDraft(), 422);

        $entry->update(['status' => 'final']);

        return redirect()->route('werkbonnen.show', $entry)
            ->with('success', 'Werkbon gemarkeerd als definitief.');
    }

    public function destroy(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);

        $entry->lineItems()->delete();
        $entry->aiJobs()->delete();
        $entry->documents()->delete();
        $entry->delete();

        return redirect()->route('werkbonnen.index')
            ->with('success', 'Werkbon verwijderd.');
    }
}
