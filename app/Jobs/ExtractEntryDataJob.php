<?php

namespace App\Jobs;

use App\Models\AiJob;
use App\Models\Entry;
use App\Models\LineItem;
use App\Services\ClaudeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExtractEntryDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(
        public Entry $entry,
    ) {}

    public function handle(ClaudeService $claude): void
    {
        if (empty($this->entry->raw_transcript)) {
            Log::warning('ExtractEntryDataJob: No transcript available', [
                'entry_id' => $this->entry->id,
            ]);
            $this->entry->update(['status' => 'draft']);
            return;
        }

        $aiJob = AiJob::create([
            'entry_id' => $this->entry->id,
            'type' => 'extraction',
            'status' => 'processing',
            'provider' => 'anthropic-claude',
            'started_at' => now(),
        ]);

        try {
            $result = $claude->extractEntryData($this->entry->raw_transcript);
            $extracted = $result['extracted'];
            $usage = $result['usage'];

            // Use first entry from the entries array (fallback path handles one entry)
            $entryData = $extracted['entries'][0] ?? $extracted;

            // Update entry with extracted data
            $this->entry->update([
                'title' => $entryData['title'] ?? 'Zonder titel',
                'ai_extracted_data' => $entryData,
                'status' => 'draft',
            ]);

            // Create line items
            $totalAmount = 0;
            foreach (($entryData['line_items'] ?? []) as $i => $item) {
                $lineTotal = (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0);
                $totalAmount += $lineTotal;

                LineItem::create([
                    'entry_id' => $this->entry->id,
                    'description' => $item['description'] ?? '',
                    'quantity' => $item['quantity'] ?? 1,
                    'unit' => $item['unit'] ?? 'stuk',
                    'unit_price' => $item['unit_price'] ?? 0,
                    'btw_rate' => $item['btw_rate'] ?? 21.00,
                    'total' => $lineTotal,
                    'sort_order' => $i + 1,
                ]);
            }

            $this->entry->update(['total_amount' => $totalAmount]);

            // Update AI job
            $aiJob->update([
                'status' => 'completed',
                'output' => $extracted,
                'tokens_used' => $usage['total_tokens'] ?? 0,
                'completed_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Entry data extraction failed', [
                'entry_id' => $this->entry->id,
                'error' => $e->getMessage(),
            ]);

            $aiJob->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            // Still move to draft so the user can edit manually
            $this->entry->update(['status' => 'draft']);

            throw $e;
        }
    }
}
