<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->user()->currentWorkspace();
        $clients = $workspace->clients()
            ->withCount('entries')
            ->withSum('entries', 'total_amount')
            ->orderBy('name')
            ->paginate(20);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function show(Request $request, Client $client)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($client->workspace_id === $workspace->id, 403);

        $client->loadCount('entries');
        $entries = $client->entries()->with('project')->latest()->paginate(20);
        $invoices = $client->invoices()->latest()->get();

        return view('clients.show', compact('client', 'entries', 'invoices'));
    }

    public function edit(Request $request, Client $client)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($client->workspace_id === $workspace->id, 403);

        return view('clients.edit', compact('client'));
    }

    public function destroy(Request $request, Client $client)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($client->workspace_id === $workspace->id, 403);

        $client->entries()->update(['client_id' => null]);
        $client->invoices()->update(['client_id' => null]);
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Relatie verwijderd.');
    }
}
