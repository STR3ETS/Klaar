<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
    Route::view('settings', 'settings')->name('settings');

    // AI transcript cleanup (direct fetch from voice recorder — bypasses Livewire)
    Route::post('api/clean-transcript', function (\Illuminate\Http\Request $request) {
        $request->validate(['text' => 'required|string|min:5']);
        $cleaned = app(\App\Services\ClaudeService::class)->cleanTranscript($request->text);
        return response()->json(['cleaned' => $cleaned]);
    })->name('api.clean-transcript');

    // AI correction: apply spoken correction to existing transcript
    Route::post('api/apply-correction', function (\Illuminate\Http\Request $request) {
        $request->validate(['original' => 'required|string', 'correction' => 'required|string|min:2']);
        $result = app(\App\Services\ClaudeService::class)->applyCorrection($request->original, $request->correction);
        return response()->json(['result' => $result]);
    })->name('api.apply-correction');

    // Voice-extract: extract structured data from transcript for forms
    Route::post('api/voice-extract', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'transcript' => 'required|string|min:3',
            'context' => 'required|in:client,project,auto',
        ]);

        $claude = app(\App\Services\ClaudeService::class);
        $transcript = $request->transcript;
        $context = $request->context;

        // Auto-classify if context is 'auto'
        if ($context === 'auto') {
            $classification = $claude->classifyVoiceIntent($transcript);
            $context = match ($classification['intent']) {
                'create_client' => 'client',
                'create_project' => 'project',
                default => 'entry',
            };
        }

        try {
            if ($context === 'client') {
                $data = $claude->extractClientData($transcript);
                return response()->json(['context' => 'client', 'data' => $data]);
            }

            if ($context === 'project') {
                $workspace = auth()->user()->currentWorkspace();
                $clientNames = $workspace->clients()->pluck('name')->toArray();
                $data = $claude->extractProjectData($transcript, $clientNames);

                // Match client_name to actual client ID
                if (!empty($data['client_name'])) {
                    $client = $workspace->clients()
                        ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($data['client_name']) . '%'])
                        ->first();
                    $data['client_id'] = $client?->id;
                }

                return response()->json(['context' => 'project', 'data' => $data]);
            }

            // Default: entry extraction (handled by process-entry route)
            return response()->json(['context' => 'entry', 'redirect' => true]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Voice extract failed', [
                'error' => $e->getMessage(),
                'context' => $context,
            ]);

            return response()->json(['error' => 'Extractie mislukt. Probeer opnieuw.'], 422);
        }
    })->name('api.voice-extract');

    // Universal smart voice: classify intent(s) and execute — supports compound requests
    Route::post('api/smart-voice', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'audio' => 'nullable|file|max:51200',
            'transcript' => 'required|string|min:5',
        ]);

        $workspace = auth()->user()->currentWorkspace();
        $claude = app(\App\Services\ClaudeService::class);
        $transcript = $request->transcript;

        // 1. Classify intents (now returns array)
        $classification = $claude->classifyVoiceIntent($transcript);
        $intents = $classification['intents'] ?? ['create_entry'];
        $isCompound = count($intents) > 1;

        try {
            // ── Single intent shortcuts (backward-compatible response formats) ──
            if (!$isCompound) {
                $intent = $intents[0];

                if ($intent === 'create_client') {
                    $data = $claude->extractClientData($transcript);
                    $client = $workspace->clients()->create([
                        'type' => $data['type'] ?? 'particulier',
                        'name' => $data['name'] ?? 'Naamloos',
                        'email' => $data['email'] ?? null,
                        'phone' => $data['phone'] ?? null,
                        'company' => $data['company'] ?? null,
                        'address_street' => $data['address_street'] ?? null,
                        'address_housenumber' => $data['address_housenumber'] ?? null,
                        'address_postcode' => $data['address_postcode'] ?? null,
                        'address_city' => $data['address_city'] ?? null,
                        'kvk_number' => $data['kvk_number'] ?? null,
                        'btw_number' => $data['btw_number'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);
                    return response()->json([
                        'type' => 'client',
                        'message' => "Klant \"{$client->name}\" aangemaakt.",
                        'redirect' => route('clients.show', $client),
                        'data' => ['id' => $client->id, 'name' => $client->name],
                    ]);
                }

                if ($intent === 'create_project') {
                    $clientNames = $workspace->clients()->pluck('name')->toArray();
                    $data = $claude->extractProjectData($transcript, $clientNames);
                    $clientId = null;
                    if (!empty($data['client_name'])) {
                        $client = $workspace->clients()
                            ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($data['client_name']) . '%'])
                            ->first();
                        $clientId = $client?->id;
                    }
                    $project = $workspace->projects()->create([
                        'name' => $data['name'] ?? 'Naamloos project',
                        'description' => $data['description'] ?? null,
                        'address' => $data['address'] ?? null,
                        'client_id' => $clientId,
                        'status' => 'active',
                    ]);
                    return response()->json([
                        'type' => 'project',
                        'message' => "Project \"{$project->name}\" aangemaakt.",
                        'redirect' => route('projects.show', $project),
                        'data' => ['id' => $project->id, 'name' => $project->name],
                    ]);
                }

                if ($intent === 'command') {
                    $entries = $workspace->entries()
                        ->with('client')
                        ->whereIn('status', ['draft', 'final'])
                        ->latest()->take(100)->get()
                        ->map(fn ($e) => [
                            'id' => $e->id,
                            'title' => $e->title ?? 'Zonder titel',
                            'status' => $e->status,
                            'client_name' => $e->client?->name,
                            'client_id' => $e->client_id,
                            'entry_date' => $e->entry_date?->toDateString() ?? $e->created_at->toDateString(),
                            'total_amount' => (float) $e->total_amount,
                        ])->toArray();

                    if (empty($entries)) {
                        return response()->json([
                            'type' => 'command',
                            'message' => 'Er zijn geen werkbonnen om acties op uit te voeren.',
                            'actions_taken' => [],
                        ]);
                    }

                    $result = $claude->interpretCommand($transcript, $entries);
                    $actionsTaken = [];
                    $workspaceEntryIds = collect($entries)->pluck('id')->toArray();

                    foreach (($result['actions'] ?? []) as $action) {
                        $type = $action['type'] ?? null;
                        $ids = array_intersect($action['entry_ids'] ?? [], $workspaceEntryIds);
                        if (empty($ids)) continue;

                        if ($type === 'finalize') {
                            $count = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->where('status', 'draft')->update(['status' => 'final']);
                            $actionsTaken[] = ['type' => 'finalize', 'count' => $count];
                        } elseif ($type === 'delete') {
                            $toDelete = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->get();
                            foreach ($toDelete as $e) { $e->lineItems()->delete(); $e->aiJobs()->delete(); $e->documents()->delete(); $e->delete(); }
                            $actionsTaken[] = ['type' => 'delete', 'count' => $toDelete->count()];
                        } elseif ($type === 'reopen') {
                            $count = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->where('status', 'final')->update(['status' => 'draft']);
                            $actionsTaken[] = ['type' => 'reopen', 'count' => $count];
                        } elseif ($type === 'convert_to_invoice') {
                            $toInvoice = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->where('status', 'final')->whereNotNull('client_id')->with(['lineItems', 'client'])->get();
                            $invoiceIds = [];
                            foreach ($toInvoice as $e) {
                                $sub = $e->lineItems->sum(fn ($li) => $li->quantity * $li->unit_price);
                                $tax = $e->lineItems->sum(fn ($li) => ($li->quantity * $li->unit_price) * ($li->btw_rate / 100));
                                $inv = \App\Models\Invoice::create(['workspace_id' => $workspace->id, 'entry_id' => $e->id, 'client_id' => $e->client_id, 'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4)), 'status' => 'draft', 'issue_date' => now(), 'due_date' => now()->addDays(30), 'subtotal' => $sub, 'tax_total' => $tax, 'total' => $sub + $tax, 'notes' => null]);
                                foreach ($e->lineItems as $li) { $inv->lineItems()->create(['description' => $li->description, 'quantity' => $li->quantity, 'unit' => $li->unit, 'unit_price' => $li->unit_price, 'btw_rate' => $li->btw_rate, 'total' => $li->quantity * $li->unit_price]); }
                                $invoiceIds[] = $inv->id;
                            }
                            if (!empty($invoiceIds)) {
                                $actionsTaken[] = ['type' => 'convert_to_invoice', 'count' => count($invoiceIds), 'redirect' => count($invoiceIds) === 1 ? route('invoices.show', $invoiceIds[0]) : route('invoices.index')];
                            }
                        }
                    }

                    return response()->json([
                        'type' => 'command',
                        'message' => $result['message'] ?? 'Commando verwerkt.',
                        'actions_taken' => $actionsTaken,
                    ]);
                }

                // Single create_entry → passthrough (frontend calls /api/process-entry)
                return response()->json(['type' => 'entry', 'passthrough' => true]);
            }

            // ── Compound: multiple intents processed sequentially ──
            $steps = [];
            $createdEntryIds = [];
            $audioPath = null;

            if ($request->hasFile('audio')) {
                $audioPath = $request->file('audio')->store("voice/{$workspace->id}", 'local');
            }

            foreach ($intents as $intent) {
                // ── Create entry ──
                if ($intent === 'create_entry') {
                    $extractResult = $claude->extractEntryData($transcript);
                    $entriesData = $extractResult['extracted']['entries'] ?? [];

                    foreach ($entriesData as $idx => $entryData) {
                        // Auto-match/create client
                        $clientId = null;
                        $clientHint = $entryData['client_hint'] ?? null;
                        if ($clientHint && trim($clientHint) !== '') {
                            $clientHint = trim($clientHint);
                            $client = $workspace->clients()->whereRaw('LOWER(name) = ?', [strtolower($clientHint)])->first();
                            if ($client) {
                                $clientId = $client->id;
                            } else {
                                $client = $workspace->clients()->create(['name' => $clientHint]);
                                $clientId = $client->id;
                            }
                        }

                        // Auto-match project
                        $projectId = null;
                        $projectHint = $entryData['project_hint'] ?? null;
                        if ($projectHint && trim($projectHint) !== '') {
                            $project = $workspace->projects()->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(trim($projectHint)) . '%'])->first();
                            if ($project) {
                                $projectId = $project->id;
                                if (!$clientId && $project->client_id) $clientId = $project->client_id;
                            }
                        }

                        $entry = \App\Models\Entry::create([
                            'workspace_id' => $workspace->id,
                            'client_id' => $clientId,
                            'project_id' => $projectId,
                            'type' => 'voice',
                            'status' => 'draft',
                            'entry_date' => $entryData['entry_date'] ?? now()->toDateString(),
                            'raw_transcript' => $transcript,
                            'title' => $entryData['title'] ?? 'Zonder titel',
                            'ai_extracted_data' => $entryData,
                        ]);

                        // Audio document for first entry
                        if ($idx === 0 && $audioPath && $request->hasFile('audio')) {
                            \App\Models\Document::create([
                                'documentable_type' => \App\Models\Entry::class,
                                'documentable_id' => $entry->id,
                                'type' => 'voice',
                                'disk' => 'local',
                                'path' => $audioPath,
                                'original_name' => $request->file('audio')->getClientOriginalName(),
                                'mime_type' => $request->file('audio')->getMimeType(),
                                'size' => $request->file('audio')->getSize(),
                            ]);
                        }

                        // Line items
                        $totalAmount = 0;
                        foreach (($entryData['line_items'] ?? []) as $i => $item) {
                            $lineTotal = (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
                            $totalAmount += $lineTotal;
                            \App\Models\LineItem::create([
                                'entry_id' => $entry->id,
                                'description' => $item['description'] ?? '',
                                'quantity' => $item['quantity'] ?? 1,
                                'unit' => $item['unit'] ?? 'stuk',
                                'unit_price' => $item['unit_price'] ?? 0,
                                'btw_rate' => $item['btw_rate'] ?? 21.00,
                                'total' => $lineTotal,
                                'sort_order' => $i + 1,
                            ]);
                        }
                        $entry->update(['total_amount' => $totalAmount]);
                        $createdEntryIds[] = $entry->id;

                        $formattedTotal = '€' . number_format($totalAmount, 0, ',', '.');
                        $steps[] = [
                            'type' => 'entry_created',
                            'text' => "Werkbon '{$entry->title}' aangemaakt ({$formattedTotal})",
                            'link' => route('werkbonnen.show', $entry),
                        ];
                    }

                    // Track AI usage
                    if (!empty($createdEntryIds)) {
                        \App\Models\AiJob::create([
                            'entry_id' => $createdEntryIds[0],
                            'type' => 'extraction',
                            'status' => 'completed',
                            'provider' => 'anthropic-claude',
                            'started_at' => now(),
                            'completed_at' => now(),
                            'output' => $extractResult['extracted'],
                            'tokens_used' => $extractResult['usage']['total_tokens'] ?? 0,
                        ]);
                    }
                }

                // ── Create client ──
                if ($intent === 'create_client') {
                    $data = $claude->extractClientData($transcript);
                    $client = $workspace->clients()->create([
                        'type' => $data['type'] ?? 'particulier',
                        'name' => $data['name'] ?? 'Naamloos',
                        'email' => $data['email'] ?? null,
                        'phone' => $data['phone'] ?? null,
                        'company' => $data['company'] ?? null,
                        'address_street' => $data['address_street'] ?? null,
                        'address_housenumber' => $data['address_housenumber'] ?? null,
                        'address_postcode' => $data['address_postcode'] ?? null,
                        'address_city' => $data['address_city'] ?? null,
                        'kvk_number' => $data['kvk_number'] ?? null,
                        'btw_number' => $data['btw_number'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);

                    // Link to just-created entries without a client
                    if (!empty($createdEntryIds)) {
                        \App\Models\Entry::whereIn('id', $createdEntryIds)->whereNull('client_id')->update(['client_id' => $client->id]);
                    }

                    $steps[] = [
                        'type' => 'client_created',
                        'text' => "Klant '{$client->name}' aangemaakt",
                        'link' => route('clients.show', $client),
                    ];
                }

                // ── Command (finalize, delete, reopen, convert_to_invoice) ──
                if ($intent === 'command') {
                    // Build entries list INCLUDING just-created ones
                    $entriesForCommand = $workspace->entries()
                        ->with('client')
                        ->whereIn('status', ['draft', 'final'])
                        ->latest()->take(100)->get()
                        ->map(function ($e) use ($createdEntryIds) {
                            $mapped = [
                                'id' => $e->id,
                                'title' => $e->title ?? 'Zonder titel',
                                'status' => $e->status,
                                'client_name' => $e->client?->name,
                                'client_id' => $e->client_id,
                                'entry_date' => $e->entry_date?->toDateString() ?? $e->created_at->toDateString(),
                                'total_amount' => (float) $e->total_amount,
                            ];
                            if (in_array($e->id, $createdEntryIds)) {
                                $mapped['just_created'] = true;
                            }
                            return $mapped;
                        })->toArray();

                    if (!empty($entriesForCommand)) {
                        $commandResult = $claude->interpretCommand($transcript, $entriesForCommand);
                        $validIds = collect($entriesForCommand)->pluck('id')->toArray();

                        foreach (($commandResult['actions'] ?? []) as $action) {
                            $type = $action['type'] ?? null;
                            $ids = array_intersect($action['entry_ids'] ?? [], $validIds);
                            if (empty($ids)) continue;

                            if ($type === 'finalize') {
                                $count = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->where('status', 'draft')->update(['status' => 'final']);
                                if ($count > 0) $steps[] = ['type' => 'finalize', 'text' => "{$count} werkbon(nen) definitief gemaakt", 'link' => null];
                            } elseif ($type === 'delete') {
                                $toDelete = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->get();
                                foreach ($toDelete as $e) { $e->lineItems()->delete(); $e->aiJobs()->delete(); $e->documents()->delete(); $e->delete(); }
                                if ($toDelete->count() > 0) $steps[] = ['type' => 'delete', 'text' => "{$toDelete->count()} werkbon(nen) verwijderd", 'link' => null];
                            } elseif ($type === 'reopen') {
                                $count = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->where('status', 'final')->update(['status' => 'draft']);
                                if ($count > 0) $steps[] = ['type' => 'reopen', 'text' => "{$count} werkbon(nen) heropend", 'link' => null];
                            } elseif ($type === 'convert_to_invoice') {
                                $toInvoice = \App\Models\Entry::whereIn('id', $ids)->where('workspace_id', $workspace->id)->where('status', 'final')->whereNotNull('client_id')->with(['lineItems', 'client'])->get();
                                $invoiceIds = [];
                                foreach ($toInvoice as $e) {
                                    $sub = $e->lineItems->sum(fn ($li) => $li->quantity * $li->unit_price);
                                    $tax = $e->lineItems->sum(fn ($li) => ($li->quantity * $li->unit_price) * ($li->btw_rate / 100));
                                    $inv = \App\Models\Invoice::create(['workspace_id' => $workspace->id, 'entry_id' => $e->id, 'client_id' => $e->client_id, 'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4)), 'status' => 'draft', 'issue_date' => now(), 'due_date' => now()->addDays(30), 'subtotal' => $sub, 'tax_total' => $tax, 'total' => $sub + $tax, 'notes' => null]);
                                    foreach ($e->lineItems as $li) { $inv->lineItems()->create(['description' => $li->description, 'quantity' => $li->quantity, 'unit' => $li->unit, 'unit_price' => $li->unit_price, 'btw_rate' => $li->btw_rate, 'total' => $li->quantity * $li->unit_price]); }
                                    $invoiceIds[] = $inv->id;
                                }
                                if (!empty($invoiceIds)) {
                                    $steps[] = ['type' => 'convert_to_invoice', 'text' => count($invoiceIds) . ' factuur/facturen aangemaakt', 'link' => count($invoiceIds) === 1 ? route('invoices.show', $invoiceIds[0]) : route('invoices.index')];
                                }
                            }
                        }
                    }
                }

                // ── Create project ──
                if ($intent === 'create_project') {
                    $clientNames = $workspace->clients()->pluck('name')->toArray();
                    $data = $claude->extractProjectData($transcript, $clientNames);

                    $clientId = null;
                    if (!empty($data['client_name'])) {
                        $client = $workspace->clients()->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($data['client_name']) . '%'])->first();
                        $clientId = $client?->id;
                    }

                    $project = $workspace->projects()->create([
                        'name' => $data['name'] ?? 'Naamloos project',
                        'description' => $data['description'] ?? null,
                        'address' => $data['address'] ?? null,
                        'client_id' => $clientId,
                        'status' => 'active',
                    ]);

                    // Link to just-created entries without a project
                    if (!empty($createdEntryIds)) {
                        \App\Models\Entry::whereIn('id', $createdEntryIds)->whereNull('project_id')->update(['project_id' => $project->id]);
                    }

                    $steps[] = [
                        'type' => 'project_created',
                        'text' => "Project '{$project->name}' aangemaakt",
                        'link' => route('projects.show', $project),
                    ];
                }
            }

            return response()->json([
                'type' => 'compound',
                'message' => count($steps) . ' ' . (count($steps) === 1 ? 'actie' : 'acties') . ' uitgevoerd.',
                'steps' => $steps,
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Smart voice failed', [
                'error' => $e->getMessage(),
                'intents' => $intents,
            ]);

            // Fall back to entry creation
            return response()->json(['type' => 'entry', 'passthrough' => true]);
        }
    })->name('api.smart-voice');

    // Process voice entry: upload audio + extract data synchronously (bypasses queue)
    Route::post('api/process-entry', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'audio' => 'required|file|max:51200',
            'transcript' => 'required|string|min:5',
        ]);

        $workspace = auth()->user()->currentWorkspace();
        $claude = app(\App\Services\ClaudeService::class);

        // 1. Store audio file
        $path = $request->file('audio')->store("voice/{$workspace->id}", 'local');

        // 2. Extract entry data synchronously (may return multiple entries)
        try {
            $result = $claude->extractEntryData($request->transcript);
            $entriesData = $result['extracted']['entries'] ?? [];

            if (empty($entriesData)) {
                throw new \RuntimeException('No entries extracted from transcript.');
            }

            $createdEntries = [];

            foreach ($entriesData as $idx => $entryData) {
                // Auto-match or create client from client_hint
                $clientId = null;
                $clientInfo = null;
                $clientHint = $entryData['client_hint'] ?? null;

                if ($clientHint && trim($clientHint) !== '') {
                    $clientHint = trim($clientHint);
                    $client = $workspace->clients()
                        ->whereRaw('LOWER(name) = ?', [strtolower($clientHint)])
                        ->first();

                    if ($client) {
                        $clientId = $client->id;
                        $clientInfo = ['id' => $client->id, 'name' => $client->name, 'is_new' => false];
                    } else {
                        $client = $workspace->clients()->create(['name' => $clientHint]);
                        $clientId = $client->id;
                        $clientInfo = ['id' => $client->id, 'name' => $client->name, 'is_new' => true];
                    }
                }

                // Auto-match project from project_hint
                $projectId = null;
                $projectHint = $entryData['project_hint'] ?? null;

                if ($projectHint && trim($projectHint) !== '') {
                    $projectHint = trim($projectHint);
                    $project = $workspace->projects()
                        ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($projectHint) . '%'])
                        ->first();

                    if ($project) {
                        $projectId = $project->id;
                        // If project has a client and entry doesn't, inherit it
                        if (!$clientId && $project->client_id) {
                            $clientId = $project->client_id;
                        }
                    }
                }

                $entry = \App\Models\Entry::create([
                    'workspace_id' => $workspace->id,
                    'client_id' => $clientId,
                    'project_id' => $projectId,
                    'type' => 'voice',
                    'status' => 'draft',
                    'entry_date' => $entryData['entry_date'] ?? now()->toDateString(),
                    'raw_transcript' => $request->transcript,
                    'title' => $entryData['title'] ?? 'Zonder titel',
                    'ai_extracted_data' => $entryData,
                ]);

                // Link audio file to the first entry
                if ($idx === 0) {
                    \App\Models\Document::create([
                        'documentable_type' => \App\Models\Entry::class,
                        'documentable_id' => $entry->id,
                        'type' => 'voice',
                        'disk' => 'local',
                        'path' => $path,
                        'original_name' => $request->file('audio')->getClientOriginalName(),
                        'mime_type' => $request->file('audio')->getMimeType(),
                        'size' => $request->file('audio')->getSize(),
                    ]);
                }

                // Create line items
                $totalAmount = 0;
                foreach (($entryData['line_items'] ?? []) as $i => $item) {
                    $lineTotal = (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
                    $totalAmount += $lineTotal;

                    \App\Models\LineItem::create([
                        'entry_id' => $entry->id,
                        'description' => $item['description'] ?? '',
                        'quantity' => $item['quantity'] ?? 1,
                        'unit' => $item['unit'] ?? 'stuk',
                        'unit_price' => $item['unit_price'] ?? 0,
                        'btw_rate' => $item['btw_rate'] ?? 21.00,
                        'total' => $lineTotal,
                        'sort_order' => $i + 1,
                    ]);
                }

                $entry->update(['total_amount' => $totalAmount]);

                $createdEntries[] = [
                    'id' => $entry->id,
                    'title' => $entry->title,
                    'total' => $totalAmount,
                    'client' => $clientInfo,
                ];
            }

            // Track AI usage on first entry
            \App\Models\AiJob::create([
                'entry_id' => $createdEntries[0]['id'],
                'type' => 'extraction',
                'status' => 'completed',
                'provider' => 'anthropic-claude',
                'started_at' => now(),
                'completed_at' => now(),
                'output' => $result['extracted'],
                'tokens_used' => $result['usage']['total_tokens'] ?? 0,
            ]);

            return response()->json([
                'entries' => $createdEntries,
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Entry extraction failed', [
                'error' => $e->getMessage(),
            ]);

            // Create a bare entry so the user can still edit manually
            $entry = \App\Models\Entry::create([
                'workspace_id' => $workspace->id,
                'type' => 'voice',
                'status' => 'draft',
                'entry_date' => now()->toDateString(),
                'raw_transcript' => $request->transcript,
            ]);

            \App\Models\Document::create([
                'documentable_type' => \App\Models\Entry::class,
                'documentable_id' => $entry->id,
                'type' => 'voice',
                'disk' => 'local',
                'path' => $path,
                'original_name' => $request->file('audio')->getClientOriginalName(),
                'mime_type' => $request->file('audio')->getMimeType(),
                'size' => $request->file('audio')->getSize(),
            ]);

            return response()->json([
                'entries' => [['id' => $entry->id, 'title' => 'Zonder titel', 'total' => 0]],
                'error' => 'Extractie mislukt, je kunt de werkbon handmatig aanvullen.',
            ]);
        }
    })->name('api.process-entry');

    // Voice command: interpret and execute spoken commands on entries
    Route::post('api/voice-command', function (\Illuminate\Http\Request $request) {
        $request->validate(['transcript' => 'required|string|min:3']);

        $workspace = auth()->user()->currentWorkspace();
        $claude = app(\App\Services\ClaudeService::class);

        // Load recent entries for context (max 100)
        $entries = $workspace->entries()
            ->with('client')
            ->whereIn('status', ['draft', 'final'])
            ->latest()
            ->take(100)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title ?? 'Zonder titel',
                'status' => $e->status,
                'client_name' => $e->client?->name,
                'entry_date' => $e->entry_date?->toDateString() ?? $e->created_at->toDateString(),
                'total_amount' => (float) $e->total_amount,
            ])
            ->toArray();

        if (empty($entries)) {
            return response()->json([
                'message' => 'Er zijn geen werkbonnen om acties op uit te voeren.',
                'actions_taken' => [],
            ]);
        }

        $result = $claude->interpretCommand($request->transcript, $entries);
        $actionsTaken = [];
        $workspaceEntryIds = collect($entries)->pluck('id')->toArray();

        foreach (($result['actions'] ?? []) as $action) {
            $type = $action['type'] ?? null;
            $ids = $action['entry_ids'] ?? [];

            // Security: only allow IDs from this workspace
            $ids = array_intersect($ids, $workspaceEntryIds);

            if (empty($ids)) continue;

            if ($type === 'finalize') {
                $count = \App\Models\Entry::whereIn('id', $ids)
                    ->where('workspace_id', $workspace->id)
                    ->where('status', 'draft')
                    ->update(['status' => 'final']);
                $actionsTaken[] = ['type' => 'finalize', 'count' => $count];

            } elseif ($type === 'delete') {
                $entriesToDelete = \App\Models\Entry::whereIn('id', $ids)
                    ->where('workspace_id', $workspace->id)
                    ->get();
                foreach ($entriesToDelete as $entry) {
                    $entry->lineItems()->delete();
                    $entry->aiJobs()->delete();
                    $entry->documents()->delete();
                    $entry->delete();
                }
                $actionsTaken[] = ['type' => 'delete', 'count' => $entriesToDelete->count()];

            } elseif ($type === 'reopen') {
                $count = \App\Models\Entry::whereIn('id', $ids)
                    ->where('workspace_id', $workspace->id)
                    ->where('status', 'final')
                    ->update(['status' => 'draft']);
                $actionsTaken[] = ['type' => 'reopen', 'count' => $count];
            }
        }

        return response()->json([
            'message' => $result['message'] ?? 'Commando verwerkt.',
            'actions_taken' => $actionsTaken,
        ]);
    })->name('api.voice-command');

    // Clients
    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Projects
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Invoeren (AI command center)
    Route::get('invoeren', function () {
        $type = request('type', 'voice');
        $workspace = auth()->user()->currentWorkspace();
        $recentEntries = $workspace->entries()
            ->with(['client', 'project'])
            ->latest()
            ->take(15)
            ->get();
        return view('invoeren.index', compact('type', 'recentEntries'));
    })->name('invoeren.index');

    // Werkbonnen (CRUD)
    Route::get('werkbonnen', [EntryController::class, 'index'])->name('werkbonnen.index');
    Route::get('werkbonnen/{entry}', [EntryController::class, 'show'])->name('werkbonnen.show');
    Route::get('werkbonnen/{entry}/edit', [EntryController::class, 'edit'])->name('werkbonnen.edit');
    Route::patch('werkbonnen/{entry}/finalize', [EntryController::class, 'finalize'])->name('werkbonnen.finalize');
    Route::delete('werkbonnen/{entry}', [EntryController::class, 'destroy'])->name('werkbonnen.destroy');

    // Backward compatibility redirects
    Route::get('entries', fn () => redirect()->route('werkbonnen.index'));
    Route::get('entries/create', fn () => redirect()->route('invoeren.index'));
    Route::get('entries/{entry}', fn (\App\Models\Entry $entry) => redirect()->route('werkbonnen.show', $entry));
    Route::get('entries/{entry}/edit', fn (\App\Models\Entry $entry) => redirect()->route('werkbonnen.edit', $entry));

    // Invoices
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('werkbonnen/{entry}/invoice', [InvoiceController::class, 'createFromEntry'])->name('invoices.create-from-entry');
    Route::patch('invoices/{invoice}/send', [InvoiceController::class, 'markSent'])->name('invoices.mark-sent');
    Route::patch('invoices/{invoice}/pay', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::post('invoices/{invoice}/email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
});

require __DIR__.'/auth.php';
