<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Email from Stafe CRM' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .email-header img {
            max-height: 40px;
        }
        .email-body {
            color: #374151;
            font-size: 16px;
        }
        .email-body p {
            margin-bottom: 16px;
        }
        .email-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .signature {
            margin-top: 20px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <strong style="color: #10b981; font-size: 24px;">Stafe CRM</strong>
        </div>

        <div class="email-body">
            {!! nl2br(e($emailBody)) !!}
        </div>

        <div class="signature">
            <p>Best regards,<br>{{ $senderName }}</p>
        </div>

        <div class="email-footer">
            <p>This email was sent via Stafe CRM.</p>
            <p>&copy; {{ date('Y') }} Stafe CRM. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
