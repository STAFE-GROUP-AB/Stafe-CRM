<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offert {{ $quote->quote_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 30px;
            text-align: center;
        }

        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 10px 0 0;
            font-size: 14px;
        }

        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .message {
            color: #4b5563;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .quote-details {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .quote-details h3 {
            color: #f59e0b;
            margin: 0 0 15px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6b7280;
            font-size: 14px;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }

        .total-row {
            background-color: #f59e0b;
            margin: -25px -25px -25px -25px;
            margin-top: 20px;
            padding: 15px 25px;
            border-radius: 0 0 8px 8px;
        }

        .total-row .detail-label,
        .total-row .detail-value {
            color: #ffffff;
            font-size: 18px;
        }

        .cta-buttons {
            text-align: center;
            margin: 30px 0;
        }

        .btn {
            display: inline-block;
            padding: 14px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin: 5px;
        }

        .btn-primary {
            background-color: #f59e0b;
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .personal-message {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }

        .personal-message p {
            margin: 0;
            color: #92400e;
            font-style: italic;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-text {
            color: #6b7280;
            font-size: 13px;
            margin: 0;
        }

        .footer-company {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
            margin: 10px 0 0;
        }

        .validity-notice {
            background-color: #fef3c7;
            color: #92400e;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 13px;
            text-align: center;
            margin-top: 20px;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .cta-buttons .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>Offert</h1>
                <p>{{ $quote->quote_number }}</p>
            </div>

            <div class="email-body">
                <p class="greeting">Hej{{ $quote->contact ? ' ' . $quote->contact->first_name : '' }},</p>

                <p class="message">
                    Vi har nojet att skicka dig var offert for {{ $quote->title }}.
                    Bifogat finner du offerten som PDF-dokument.
                </p>

                @if($personalMessage)
                <div class="personal-message">
                    <p>{{ $personalMessage }}</p>
                </div>
                @endif

                <div class="quote-details">
                    <h3>Offertsammanfattning</h3>
                    <div class="detail-row">
                        <span class="detail-label">Offertnummer</span>
                        <span class="detail-value">{{ $quote->quote_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Titel</span>
                        <span class="detail-value">{{ $quote->title }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Datum</span>
                        <span class="detail-value">{{ $quote->created_at->format('Y-m-d') }}</span>
                    </div>
                    @if($quote->valid_until)
                    <div class="detail-row">
                        <span class="detail-label">Giltig till</span>
                        <span class="detail-value">{{ $quote->valid_until->format('Y-m-d') }}</span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Antal poster</span>
                        <span class="detail-value">{{ $quote->items->count() }} st</span>
                    </div>
                    <div class="total-row">
                        <div class="detail-row" style="border: none; padding: 0;">
                            <span class="detail-label">Totalt belopp</span>
                            <span class="detail-value">{{ number_format($quote->total_amount ?? 0, 2, ',', ' ') }} {{ $quote->currency ?? 'SEK' }}</span>
                        </div>
                    </div>
                </div>

                <div class="cta-buttons">
                    <a href="{{ $previewUrl }}" class="btn btn-secondary">Visa offert online</a>
                    <a href="{{ $signUrl }}" class="btn btn-primary">Signera offert</a>
                </div>

                @if($quote->valid_until)
                <div class="validity-notice">
                    Denna offert ar giltig till och med {{ $quote->valid_until->format('Y-m-d') }}.
                </div>
                @endif
            </div>

            <div class="email-footer">
                <p class="footer-text">
                    Detta e-postmeddelande skickades fran {{ $companyName }}.
                    <br>
                    Vid fragor, kontakta {{ $senderName }}.
                </p>
                <p class="footer-company">{{ $companyName }}</p>
            </div>
        </div>
    </div>
</body>
</html>
