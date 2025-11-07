<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Program Agreement Ready</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #730623;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background: #730623;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background: #8a0a2a;
            color: white;
        }
        .program-info {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #730623;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Program Agreement Ready</h1>
        <p>Lana Amawi Coaching Services</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $userProgram->user->name }},</h2>
        
        <p>Great news! Your application for the <strong>{{ $userProgram->program->name }}</strong> has been reviewed and approved. We're excited to have you join this program!</p>
        
        <div class="program-info">
            <h3>Program Details</h3>
            <p><strong>Program:</strong> {{ $userProgram->program->name }}</p>
            <p><strong>Monthly Subscription:</strong> ${{ number_format($userProgram->program->monthly_price ?? 0, 2) }}/month</p>
            <p><strong>Sessions Per Month:</strong> {{ $userProgram->program->monthly_sessions ?? 0 }}</p>
        </div>
        
        <h3>Next Steps:</h3>
        <ol>
            <li><strong>Download the Agreement:</strong> Click the button below to download your personalized program agreement</li>
            <li><strong>Review the Terms:</strong> Carefully read through all the terms and conditions</li>
            <li><strong>Sign the Agreement:</strong> Print, sign, and scan the agreement</li>
            <li><strong>Upload the Signed Agreement:</strong> Return to your client portal to upload the signed document</li>
        </ol>
        
        <div style="text-align: center;">
            <a href="{{ route('client.programs.agreement.download', $userProgram) }}" class="button">
                Download Program Agreement
            </a>
        </div>
        
        <p>Once you've uploaded the signed agreement, we'll review it and proceed with the next steps. You'll receive another email once everything is approved and ready to go!</p>
        
        <p>If you have any questions about the program or the agreement, please don't hesitate to contact us through your client portal.</p>
        
        <p>We're looking forward to working with you!</p>
        
        <p>Best regards,<br>
        <strong>Lana Amawi</strong><br>
        Professional Coach</p>
    </div>
    
    <div class="footer">
        <p>This email was sent from Lana Amawi Coaching Services</p>
        <p>If you have any questions, please contact us through your client portal</p>
    </div>
</body>
</html>
