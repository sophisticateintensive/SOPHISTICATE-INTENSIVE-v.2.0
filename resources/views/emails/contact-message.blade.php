<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .box { max-width: 600px; margin: 20px auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .field { margin-bottom: 12px; }
        .label { font-weight: bold; color: #475569; font-size: 13px; text-transform: uppercase; }
        .val { font-size: 15px; margin-top: 2px; }
        .message-content { background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #2563eb; margin-top: 6px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="box">
        <div class="header">
            <h2 style="margin: 0; color: #1e3a8a;">New Contact Form Message</h2>
            <p style="margin: 4px 0 0; color: #64748b; font-size: 13px;">Sophisticate Intensive Classes Portal</p>
        </div>

        <div class="field">
            <div class="label">Sender Name</div>
            <div class="val">{{ $contactData['fname'] ?? '' }} {{ $contactData['lname'] ?? '' }}</div>
        </div>

        <div class="field">
            <div class="label">Email Address</div>
            <div class="val"><a href="mailto:{{ $contactData['email'] }}">{{ $contactData['email'] }}</a></div>
        </div>

        <div class="field">
            <div class="label">Subject</div>
            <div class="val">{{ $contactData['subject'] ?? 'General Inquiry' }}</div>
        </div>

        <div class="field">
            <div class="label">Message Body</div>
            <div class="message-content">{{ $contactData['message'] }}</div>
        </div>
    </div>
</body>
</html>
