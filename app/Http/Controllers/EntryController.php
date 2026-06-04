<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->user()->currentWorkspace();
        $entries = $workspace->entries()
            ->with('project')
            ->latest()
            ->paginate(20);

        return view('entries.index', compact('entries'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'voice');

        return view('entries.create', compact('type'));
    }

    public function show(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);

        $entry->load(['lineItems', 'project', 'documents', 'aiJobs']);

        return view('entries.show', compact('entry'));
    }

    public function finalize(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);
        abort_unless($entry->isDraft(), 422);

        $entry->update(['status' => 'final']);

        return redirect()->route('entries.show', $entry)
            ->with('success', 'Invoer gemarkeerd als definitief.');
    }

    public function destroy(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);

        $entry->lineItems()->delete();
        $entry->aiJobs()->delete();
        $entry->documents()->delete();
        $entry->delete();

        return redirect()->route('entries.index')
            ->with('success', 'Invoer verwijderd.');
    }
}
