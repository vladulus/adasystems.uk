<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .field-value {
            color: #6b7280;
        }
        .message-box {
            background: white;
            padding: 20px;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">Name:</div>
                <div class="field-value">{{ $name }}</div>
            </div>

            <div class="field">
                <div class="field-label">Email:</div>
                <div class="field-value">{{ $email }}</div>
            </div>

            @if($phone)
            <div class="field">
                <div class="field-label">Phone:</div>
                <div class="field-value">{{ $phone }}</div>
            </div>
            @endif

            @if($company)
            <div class="field">
                <div class="field-label">Company:</div>
                <div class="field-value">{{ $company }}</div>
            </div>
            @endif

            <div class="field">
                <div class="field-label">Subject:</div>
                <div class="field-value">{{ $subject }}</div>
            </div>

            <div class="field">
                <div class="field-label">Message:</div>
                <div class="message-box">
                    {{ $message }}
                </div>
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
            
            <p style="color: #6b7280; font-size: 14px;">
                This message was sent from the Ada Pi Systems contact form.
            </p>
        </div>
    </div>
</body>
</html>
