<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Entry;
use App\Models\Invoice;
use App\Models\LineItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->user()->currentWorkspace();

        $query = $workspace->invoices()->with('client');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();
        $clients = $workspace->clients()->orderBy('name')->get();

        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function show(Request $request, Invoice $invoice)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($invoice->workspace_id === $workspace->id, 403);

        $invoice->load(['lineItems', 'client', 'entry']);

        return view('invoices.show', compact('invoice'));
    }

    public function createFromEntry(Request $request, Entry $entry)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($entry->workspace_id === $workspace->id, 403);
        abort_unless($entry->isFinal(), 422, 'Alleen definitieve werkbonnen kunnen gefactureerd worden.');
        abort_unless($entry->client_id !== null, 422, 'Koppel eerst een klant aan deze werkbon.');

        $invoice = Invoice::create([
            'workspace_id' => $workspace->id,
            'client_id' => $entry->client_id,
            'entry_id' => $entry->id,
            'invoice_number' => Invoice::generateNumber($workspace->id),
            'status' => 'draft',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

        $subtotal = 0;
        $btwAmount = 0;

        foreach ($entry->lineItems as $i => $item) {
            $lineTotal = round($item->quantity * $item->unit_price, 2);
            $lineBtw = round($lineTotal * ($item->btw_rate / 100), 2);
            $subtotal += $lineTotal;
            $btwAmount += $lineBtw;

            LineItem::create([
                'invoice_id' => $invoice->id,
                'entry_id' => null,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'btw_rate' => $item->btw_rate,
                'total' => $lineTotal,
                'sort_order' => $i + 1,
            ]);
        }

        $invoice->update([
            'subtotal' => $subtotal,
            'btw_amount' => $btwAmount,
            'total' => $subtotal + $btwAmount,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Factuur aangemaakt.');
    }

    public function markSent(Request $request, Invoice $invoice)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($invoice->workspace_id === $workspace->id, 403);
        abort_unless($invoice->isDraft(), 422);

        $invoice->update(['status' => 'sent', 'sent_at' => now()]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Factuur gemarkeerd als verstuurd.');
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($invoice->workspace_id === $workspace->id, 403);

        $invoice->update(['status' => 'paid', 'paid_at' => now()]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Factuur gemarkeerd als betaald.');
    }

    public function sendEmail(Request $request, Invoice $invoice)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($invoice->workspace_id === $workspace->id, 403);

        $invoice->load('client');
        abort_unless($invoice->client && $invoice->client->email, 422, 'Deze klant heeft geen e-mailadres.');

        Mail::to($invoice->client->email)
            ->send(new InvoiceMail($invoice, $request->user()));

        if ($invoice->isDraft()) {
            $invoice->update(['status' => 'sent', 'sent_at' => now()]);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Factuur verstuurd naar ' . $invoice->client->email);
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($invoice->workspace_id === $workspace->id, 403);
        abort_unless($invoice->isDraft(), 422, 'Alleen concept-facturen kunnen verwijderd worden.');

        $invoice->lineItems()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Factuur verwijderd.');
    }

    public function downloadPdf(Request $request, Invoice $invoice)
    {
        $workspace = $request->user()->currentWorkspace();
        abort_unless($invoice->workspace_id === $workspace->id, 403);

        $invoice->load(['lineItems', 'client']);
        $user = $request->user();

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'user'));

        return $pdf->download("factuur-{$invoice->invoice_number}.pdf");
    }
}
