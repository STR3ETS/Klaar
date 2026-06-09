<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; color: #333; line-height: 1.6; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 560px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #fff; border-radius: 6px; padding: 36px 32px; border: 1px solid #e5e5e5; }
        .greeting { font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 16px; }
        .body-text { font-size: 14px; color: #555; margin-bottom: 12px; }
        .invoice-box { background: #fafafa; border: 1px solid #eee; border-radius: 4px; padding: 16px 20px; margin: 20px 0; }
        .invoice-box .row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
        .invoice-box .row .label { color: #888; }
        .invoice-box .row .value { font-weight: 600; color: #1a1a1a; }
        .invoice-box .total { border-top: 1px solid #e5e5e5; margin-top: 8px; padding-top: 8px; }
        .invoice-box .total .value { font-size: 16px; color: #d97706; }
        .footer-text { font-size: 12px; color: #999; margin-top: 24px; padding-top: 16px; border-top: 1px solid #eee; }
        .company-name { font-weight: 600; color: #333; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            <p class="greeting">Beste {{ $invoice->client?->name ?? 'klant' }},</p>

            <p class="body-text">
                Hierbij ontvangt u factuur <strong>{{ $invoice->invoice_number }}</strong>
                van <span class="company-name">{{ $sender->company_name ?? $sender->name }}</span>.
            </p>

            <div class="invoice-box">
                <div class="row">
                    <span class="label">Factuurnummer</span>
                    <span class="value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="row">
                    <span class="label">Factuurdatum</span>
                    <span class="value">{{ $invoice->issue_date->format('d-m-Y') }}</span>
                </div>
                <div class="row">
                    <span class="label">Vervaldatum</span>
                    <span class="value">{{ $invoice->due_date->format('d-m-Y') }}</span>
                </div>
                <div class="row total">
                    <span class="label">Totaal incl. BTW</span>
                    <span class="value">&euro;{{ number_format($invoice->total, 2, ',', '.') }}</span>
                </div>
            </div>

            <p class="body-text">
                De factuur vindt u als PDF-bijlage bij deze e-mail.
                Gelieve het bedrag binnen {{ $invoice->issue_date->diffInDays($invoice->due_date) }} dagen over te maken
                onder vermelding van factuurnummer {{ $invoice->invoice_number }}.
            </p>

            <p class="body-text">
                Met vriendelijke groet,<br>
                <strong>{{ $sender->company_name ?? $sender->name }}</strong>
                @if($sender->phone)
                    <br><span style="font-size: 13px; color: #888;">Tel: {{ $sender->phone }}</span>
                @endif
            </p>

            <div class="footer-text">
                @if($sender->kvk_number)
                    KVK: {{ $sender->kvk_number }}
                @endif
                @if($sender->kvk_number && $sender->btw_number)
                    &middot;
                @endif
                @if($sender->btw_number)
                    BTW: {{ $sender->btw_number }}
                @endif
            </div>

        </div>
    </div>
</body>
</html>
