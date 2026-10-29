<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Offert {{ $quote->quote_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
        }

        .container {
            padding: 40px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .logo {
            max-height: 60px;
            max-width: 200px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 10px;
            color: #6b7280;
        }

        .quote-title {
            font-size: 28px;
            font-weight: bold;
            color: #f59e0b;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .quote-number {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Quote Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .info-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .info-box:first-child {
            margin-right: 10px;
        }

        .info-box-title {
            font-size: 10px;
            font-weight: bold;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .info-box-content {
            font-size: 12px;
            color: #1f2937;
        }

        .info-box-content strong {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Quote Details */
        .quote-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .detail-item {
            display: table-cell;
            padding: 10px 20px;
            background: #fef3c7;
            border-right: 1px solid #fcd34d;
        }

        .detail-item:last-child {
            border-right: none;
        }

        .detail-label {
            font-size: 9px;
            font-weight: bold;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 3px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #f59e0b;
            color: #fff;
            padding: 12px 15px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table th:first-child {
            border-radius: 8px 0 0 0;
        }

        .items-table th:last-child {
            border-radius: 0 8px 0 0;
            text-align: right;
        }

        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .items-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .item-name {
            font-weight: bold;
            color: #1f2937;
        }

        .item-description {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }

        .item-sku {
            font-size: 9px;
            color: #9ca3af;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totals */
        .totals-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .totals-spacer {
            display: table-cell;
            width: 60%;
        }

        .totals-box {
            display: table-cell;
            width: 40%;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 15px;
            font-size: 12px;
        }

        .totals-table .label {
            text-align: right;
            color: #6b7280;
        }

        .totals-table .value {
            text-align: right;
            font-weight: bold;
            width: 120px;
        }

        .totals-table .total-row {
            background: #f59e0b;
            color: #fff;
        }

        .totals-table .total-row td {
            padding: 12px 15px;
            font-size: 16px;
        }

        .totals-table .total-row .label {
            color: #fff;
        }

        .discount-row {
            color: #dc2626;
        }

        /* Description Section */
        .description-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .description-content {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }

        /* Terms Section */
        .terms-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 8px;
        }

        .terms-title {
            font-size: 12px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 10px;
        }

        .terms-content {
            font-size: 10px;
            color: #78350f;
            line-height: 1.6;
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .terms-list li {
            padding: 5px 0;
            padding-left: 15px;
            position: relative;
        }

        .terms-list li:before {
            content: "-";
            position: absolute;
            left: 0;
            color: #f59e0b;
            font-weight: bold;
        }

        /* Signature Section */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }

        .signature-box {
            display: table-cell;
            width: 45%;
            padding: 20px;
        }

        .signature-spacer {
            display: table-cell;
            width: 10%;
        }

        .signature-label {
            font-size: 10px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 40px;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            padding-top: 10px;
        }

        .signature-name {
            font-size: 11px;
            color: #1f2937;
        }

        .signature-title {
            font-size: 10px;
            color: #6b7280;
        }

        .signature-date {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }

        .signature-image {
            max-height: 60px;
            margin-bottom: 10px;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 40px;
            background: #1f2937;
            color: #fff;
            font-size: 9px;
            text-align: center;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left, .footer-center, .footer-right {
            display: table-cell;
        }

        .footer-left {
            text-align: left;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-draft {
            background: #e5e7eb;
            color: #4b5563;
        }

        .status-sent {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-viewed {
            background: #e0e7ff;
            color: #4338ca;
        }

        .status-accepted {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-expired {
            background: #fef3c7;
            color: #d97706;
        }

        /* Page break utility */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @php
                    $companyLogo = \App\Models\SystemSetting::get('app_logo');
                    $companyName = \App\Models\SystemSetting::get('app_name', config('app.name', 'Stafe CRM'));
                    $companyAddress = \App\Models\SystemSetting::get('company_address', '');
                    $companyPhone = \App\Models\SystemSetting::get('company_phone', '');
                    $companyEmail = \App\Models\SystemSetting::get('company_email', '');
                    $companyOrg = \App\Models\SystemSetting::get('company_org_number', '');
                @endphp

                @if($companyLogo)
                    <img src="{{ public_path(str_replace('/storage/', 'storage/', $companyLogo)) }}" alt="{{ $companyName }}" class="logo">
                @endif
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-details">
                    @if($companyAddress){{ $companyAddress }}<br>@endif
                    @if($companyPhone)Tel: {{ $companyPhone }}<br>@endif
                    @if($companyEmail){{ $companyEmail }}<br>@endif
                    @if($companyOrg)Org.nr: {{ $companyOrg }}@endif
                </div>
            </div>
            <div class="header-right">
                <div class="quote-title">Offert</div>
                <div class="quote-number">{{ $quote->quote_number }}</div>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ $quote->status }}">{{ ucfirst($quote->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Quote Info -->
        <div class="info-section">
            <div class="info-box" style="margin-right: 20px;">
                <div class="info-box-title">Till</div>
                <div class="info-box-content">
                    <strong>{{ $quote->company->name }}</strong>
                    @if($quote->contact)
                        {{ $quote->contact->first_name }} {{ $quote->contact->last_name }}<br>
                        @if($quote->contact->email){{ $quote->contact->email }}<br>@endif
                        @if($quote->contact->phone){{ $quote->contact->phone }}@endif
                    @endif
                    @if($quote->company->address)
                        <br>{{ $quote->company->address }}
                    @endif
                    @if($quote->company->city || $quote->company->postal_code)
                        <br>{{ $quote->company->postal_code }} {{ $quote->company->city }}
                    @endif
                </div>
            </div>
            <div class="info-box">
                <div class="info-box-title">Offertdetaljer</div>
                <div class="info-box-content">
                    <strong>{{ $quote->title }}</strong>
                    Skapad av: {{ $quote->createdBy->name ?? 'N/A' }}<br>
                    @if($quote->deal)
                        Relaterad affar: {{ $quote->deal->name }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Quote Details Bar -->
        <div class="quote-details">
            <div class="detail-item">
                <div class="detail-label">Offertdatum</div>
                <div class="detail-value">{{ $quote->created_at->format('Y-m-d') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Giltig till</div>
                <div class="detail-value">{{ $quote->valid_until ? $quote->valid_until->format('Y-m-d') : 'Ej specificerat' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Valuta</div>
                <div class="detail-value">{{ $quote->currency ?? 'SEK' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Referens</div>
                <div class="detail-value">{{ $quote->quote_number }}</div>
            </div>
        </div>

        <!-- Description -->
        @if($quote->description)
        <div class="description-section">
            <div class="section-title">Beskrivning</div>
            <div class="description-content">
                {!! nl2br(e($quote->description)) !!}
            </div>
        </div>
        @endif

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Produkt/Tjanst</th>
                    <th class="text-center" style="width: 10%;">Antal</th>
                    <th class="text-right" style="width: 15%;">A-pris</th>
                    <th class="text-right" style="width: 15%;">Rabatt</th>
                    <th class="text-right" style="width: 20%;">Summa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quote->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->name }}</div>
                        @if($item->description)
                            <div class="item-description">{{ $item->description }}</div>
                        @endif
                        @if($item->sku)
                            <div class="item-sku">Art.nr: {{ $item->sku }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', ' ') }}</td>
                    <td class="text-right">
                        @if($item->discount_percent > 0)
                            {{ number_format($item->discount_percent, 0) }}%
                        @elseif($item->discount_amount > 0)
                            {{ number_format($item->discount_amount, 2, ',', ' ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->line_total, 2, ',', ' ') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 30px; color: #6b7280;">
                        Inga produkter/tjanster har lagts till i denna offert.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-spacer"></div>
            <div class="totals-box">
                <table class="totals-table">
                    <tr>
                        <td class="label">Delsumma</td>
                        <td class="value">{{ number_format($quote->subtotal ?? 0, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</td>
                    </tr>
                    @if($quote->discount_amount > 0)
                    <tr class="discount-row">
                        <td class="label">Rabatt</td>
                        <td class="value">-{{ number_format($quote->discount_amount, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</td>
                    </tr>
                    @endif
                    @if($quote->tax_amount > 0)
                    <tr>
                        <td class="label">Moms (25%)</td>
                        <td class="value">{{ number_format($quote->tax_amount, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label">Totalt</td>
                        <td class="value">{{ number_format($quote->total_amount ?? 0, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Terms and Conditions -->
        @if($quote->terms_and_conditions && count($quote->terms_and_conditions) > 0)
        <div class="terms-section">
            <div class="terms-title">Villkor</div>
            <div class="terms-content">
                <ul class="terms-list">
                    @foreach($quote->terms_and_conditions as $term)
                        <li>{{ $term }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @else
        <div class="terms-section">
            <div class="terms-title">Standardvillkor</div>
            <div class="terms-content">
                <ul class="terms-list">
                    <li>Offerten ar giltig i 30 dagar fran offertdatum om inget annat anges.</li>
                    <li>Betalningsvillkor: 30 dagar netto.</li>
                    <li>Priserna ar angivna exklusive moms om inget annat anges.</li>
                    <li>Vid forsenad betalning debiteras drojsmalsranta enligt rantelagen.</li>
                    <li>Leveranstid bekraftas vid orderlaggning.</li>
                </ul>
            </div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">For leverantor</div>
                <div class="signature-line">
                    <div class="signature-name">{{ $quote->createdBy->name ?? '' }}</div>
                    <div class="signature-title">{{ $companyName }}</div>
                    <div class="signature-date">Datum: {{ $quote->created_at->format('Y-m-d') }}</div>
                </div>
            </div>
            <div class="signature-spacer"></div>
            <div class="signature-box">
                <div class="signature-label">For kund (Godkannande)</div>
                @if($quote->signatures->isNotEmpty())
                    @php $signature = $quote->signatures->first(); @endphp
                    @if($signature->signature_data)
                        <img src="data:image/png;base64,{{ $signature->signature_data }}" alt="Signatur" class="signature-image">
                    @endif
                    <div class="signature-line">
                        <div class="signature-name">{{ $signature->signer_name }}</div>
                        <div class="signature-title">{{ $signature->signer_title ?? '' }}</div>
                        <div class="signature-date">Signerad: {{ $signature->signed_at->format('Y-m-d H:i') }}</div>
                    </div>
                @else
                    <div style="height: 50px;"></div>
                    <div class="signature-line">
                        <div class="signature-name">&nbsp;</div>
                        <div class="signature-title">Namnfortydligande</div>
                        <div class="signature-date">Datum: _____________</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">{{ $companyName }}</div>
            <div class="footer-center">Offert {{ $quote->quote_number }} | Skapad {{ $quote->created_at->format('Y-m-d') }}</div>
            <div class="footer-right">Sida 1 av 1</div>
        </div>
    </div>
</body>
</html>
