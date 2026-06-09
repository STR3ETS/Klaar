<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; padding: 40px; }
        .header { margin-bottom: 40px; }
        .header::after { content: ''; display: table; clear: both; }
        .company-info { float: right; text-align: right; font-size: 10px; color: #555; line-height: 1.6; }
        .company-name { font-size: 14px; font-weight: bold; color: #1a1a1a; margin-bottom: 4px; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #1a1a1a; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px; }
        .invoice-meta { font-size: 10px; color: #555; line-height: 1.8; }
        .invoice-meta strong { color: #1a1a1a; }
        .client-block { background: #f8f8f8; border: 1px solid #e5e5e5; padding: 16px 20px; margin: 30px 0; }
        .client-block .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 6px; }
        .client-block .name { font-size: 13px; font-weight: bold; color: #1a1a1a; }
        .client-block .details { font-size: 10px; color: #555; line-height: 1.6; margin-top: 4px; }
        table.items { width: 100%; border-collapse: collapse; margin: 24px 0; }
        table.items thead th { background: #f0f0f0; border-bottom: 2px solid #ddd; padding: 10px 12px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #666; text-align: left; }
        table.items thead th.right { text-align: right; }
        table.items tbody td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 11px; }
        table.items tbody td.right { text-align: right; }
        table.items tbody td.mono { font-family: DejaVu Sans Mono, monospace; font-size: 10px; }
        .totals { width: 280px; margin-left: auto; margin-top: 16px; }
        .totals .row { display: table; width: 100%; padding: 6px 0; }
        .totals .row::after { content: ''; display: table; clear: both; }
        .totals .label { float: left; font-size: 10px; color: #666; }
        .totals .value { float: right; font-size: 10px; font-family: DejaVu Sans Mono, monospace; color: #333; }
        .totals .total-row { border-top: 2px solid #1a1a1a; padding-top: 10px; margin-top: 4px; }
        .totals .total-row .label { font-size: 12px; font-weight: bold; color: #1a1a1a; }
        .totals .total-row .value { font-size: 14px; font-weight: bold; color: #1a1a1a; }
        .footer { margin-top: 50px; padding-top: 16px; border-top: 1px solid #e5e5e5; font-size: 9px; color: #999; line-height: 1.6; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $user->company_name ?? $user->name }}</div>
            @if($user->address_street)
                {{ $user->address_street }} {{ $user->address_housenumber }}<br>
            @endif
            @if($user->address_postcode || $user->address_city)
                {{ $user->address_postcode }} {{ $user->address_city }}<br>
            @endif
            @if($user->phone)
                Tel: {{ $user->phone }}<br>
            @endif
            {{ $user->email }}<br>
            @if($user->kvk_number)
                KVK: {{ $user->kvk_number }}<br>
            @endif
            @if($user->btw_number)
                BTW: {{ $user->btw_number }}
            @endif
        </div>

        <div class="invoice-title">Factuur</div>
        <div class="invoice-meta">
            <strong>Factuurnummer:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Factuurdatum:</strong> {{ $invoice->issue_date->format('d-m-Y') }}<br>
            <strong>Vervaldatum:</strong> {{ $invoice->due_date->format('d-m-Y') }}
        </div>
    </div>

    {{-- Client --}}
    @if($invoice->client)
        <div class="client-block">
            <div class="label">Factuuradres</div>
            <div class="name">{{ $invoice->client->name }}</div>
            <div class="details">
                @if($invoice->client->company)
                    {{ $invoice->client->company }}<br>
                @endif
                @if($invoice->client->address_street)
                    {{ $invoice->client->address_street }} {{ $invoice->client->address_housenumber }}<br>
                @endif
                @if($invoice->client->address_postcode || $invoice->client->address_city)
                    {{ $invoice->client->address_postcode }} {{ $invoice->client->address_city }}<br>
                @endif
                @if($invoice->client->btw_number)
                    BTW: {{ $invoice->client->btw_number }}
                @endif
            </div>
        </div>
    @endif

    {{-- Line items --}}
    <table class="items">
        <thead>
            <tr>
                <th>Omschrijving</th>
                <th class="right">Aantal</th>
                <th>Eenheid</th>
                <th class="right">Prijs</th>
                <th class="right">BTW</th>
                <th class="right">Totaal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lineItems as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right mono">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="right mono">&euro;{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="right">{{ (int)$item->btw_rate }}%</td>
                    <td class="right mono">&euro;{{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals">
        <div class="row">
            <span class="label">Subtotaal excl. BTW</span>
            <span class="value">&euro;{{ number_format($invoice->subtotal, 2, ',', '.') }}</span>
        </div>
        <div class="row">
            <span class="label">BTW</span>
            <span class="value">&euro;{{ number_format($invoice->btw_amount, 2, ',', '.') }}</span>
        </div>
        <div class="row total-row">
            <span class="label">Totaal incl. BTW</span>
            <span class="value">&euro;{{ number_format($invoice->total, 2, ',', '.') }}</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Gelieve het totaalbedrag van &euro;{{ number_format($invoice->total, 2, ',', '.') }} binnen 14 dagen over te maken
        onder vermelding van factuurnummer {{ $invoice->invoice_number }}.
        @if($user->kvk_number || $user->btw_number)
            <br>
            @if($user->kvk_number) KVK: {{ $user->kvk_number }} @endif
            @if($user->kvk_number && $user->btw_number) &middot; @endif
            @if($user->btw_number) BTW: {{ $user->btw_number }} @endif
        @endif
    </div>

</body>
</html>
