<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Client Portal Login Credentials</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .credentials-box {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .login-button {
            display: inline-block;
            background-color: #730623;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Lana Amawi Coaching" style="max-width: 200px; height: auto; margin-bottom: 20px;">
        <h1>Welcome to Lana Amawi Coaching Portal</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $user->name }},</p>
        
        <p>Thank you for booking your coaching session with Lana Amawi! Your client portal account has been created successfully.</p>
        
        <p>Here are your login credentials:</p>
        
        <div class="credentials-box">
            <strong>Email:</strong> {{ $user->email }}<br>
            <strong>Password:</strong> {{ $password }}<br>
            <strong>Portal URL:</strong> <a href="{{ url('/client/login') }}">{{ url('/client/login') }}</a>
        </div>
        
        <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>
        
        <div style="text-align: center;">
            <a href="{{ url('/client/login') }}" class="login-button">Login to Your Portal</a>
        </div>
        
        <p>In your client portal, you can:</p>
        <ul>
            <li>View and manage your appointments</li>
            <li>Send messages to Lana</li>
            <li>Access your coaching materials</li>
            <li>Track your progress</li>
        </ul>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        The Lana Amawi Coaching Team</p>
    </div>
    
    <div class="footer">
        <p>This email was sent to {{ $user->email }}. If you didn't request this, please ignore this email.</p>
    </div>
</body>
</html> 