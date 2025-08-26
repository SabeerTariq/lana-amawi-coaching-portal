<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Coaching Agreement - Lana Amawi</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #730623;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #730623;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #730623;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .client-info {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #730623;
            margin-bottom: 20px;
        }
        .client-info p {
            margin: 5px 0;
        }
        .signature-section {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 300px;
            height: 20px;
            margin: 10px 0;
        }
        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>COACHING AGREEMENT</h1>
        <p><strong>Lana Amawi Coaching Services</strong></p>
        <p>Professional Life & Wellness Coaching</p>
        <p>Agreement Date: {{ $agreement_date }}</p>
    </div>

    <div class="client-info">
        <h3>Client Information</h3>
        <p><strong>Name:</strong> {{ $client_name }}</p>
        <p><strong>Email:</strong> {{ $client_email }}</p>
        @if($client_phone)
            <p><strong>Phone:</strong> {{ $client_phone }}</p>
        @endif
        <p><strong>Preferred Date:</strong> {{ \Carbon\Carbon::parse($preferred_date)->format('F j, Y') }}</p>
        <p><strong>Preferred Time:</strong> {{ \Carbon\Carbon::parse($preferred_time)->format('g:i A') }}</p>
        @if($message)
            <p><strong>Session Goals/Notes:</strong> {{ $message }}</p>
        @endif
    </div>

    <div class="section">
        <h2>1. SERVICES PROVIDED</h2>
        <p>Lana Amawi agrees to provide professional coaching services to {{ $client_name }} ("Client") in accordance with the terms outlined in this agreement. Coaching services may include but are not limited to:</p>
        <ul>
            <li>Life coaching and personal development</li>
            <li>Career guidance and professional development</li>
            <li>Relationship and wellness coaching</li>
            <li>Goal setting and achievement strategies</li>
            <li>Personal accountability and support</li>
        </ul>
    </div>

    <div class="section">
        <h2>2. SESSION DETAILS</h2>
        <p><strong>Session Date:</strong> {{ \Carbon\Carbon::parse($preferred_date)->format('F j, Y') }}</p>
        <p><strong>Session Time:</strong> {{ \Carbon\Carbon::parse($preferred_time)->format('g:i A') }}</p>
        <p><strong>Session Duration:</strong> 60 minutes (unless otherwise specified)</p>
        <p><strong>Session Format:</strong> Video call, phone call, or in-person (as agreed upon)</p>
    </div>

    <div class="section">
        <h2>3. CLIENT RESPONSIBILITIES</h2>
        <p>The Client agrees to:</p>
        <ul>
            <li>Attend scheduled sessions on time and prepared</li>
            <li>Complete any agreed-upon assignments or exercises</li>
            <li>Communicate openly and honestly during sessions</li>
            <li>Provide 24-hour notice for session cancellations or rescheduling</li>
            <li>Take responsibility for implementing coaching insights and strategies</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. COACH RESPONSIBILITIES</h2>
        <p>Lana Amawi agrees to:</p>
        <ul>
            <li>Provide professional coaching services in a safe and supportive environment</li>
            <li>Maintain confidentiality as outlined in the privacy policy</li>
            <li>Be punctual and prepared for all scheduled sessions</li>
            <li>Provide 24-hour notice for any session cancellations or rescheduling</li>
            <li>Maintain professional boundaries and ethical standards</li>
        </ul>
    </div>

    <div class="section">
        <h2>5. CONFIDENTIALITY</h2>
        <p>All information shared during coaching sessions is confidential, except where disclosure is required by law or where there is a risk of harm to the client or others. The coach will maintain the highest standards of confidentiality and privacy.</p>
    </div>

    <div class="section">
        <h2>6. CANCELLATION POLICY</h2>
        <p>Both parties agree to provide at least 24 hours' notice for session cancellations or rescheduling. Late cancellations may result in session fees being charged. Emergency situations will be handled on a case-by-case basis.</p>
    </div>

    <div class="section">
        <h2>7. LIMITATIONS</h2>
        <p>Coaching is not a substitute for professional medical, psychological, or legal advice. If the client requires such services, they should seek appropriate professional help. The coach will refer clients to appropriate professionals when necessary.</p>
    </div>

    <div class="section">
        <h2>8. AGREEMENT</h2>
        <p>By signing this agreement, both parties acknowledge that they have read, understood, and agree to the terms and conditions outlined above.</p>
    </div>

    <div class="signature-section">
        <div style="float: left; width: 45%;">
            <p><strong>Client Signature:</strong></p>
            <div class="signature-line"></div>
            <p><strong>Date:</strong></p>
            <div class="signature-line"></div>
        </div>
        
        <div style="float: right; width: 45%;">
            <p><strong>Coach Signature:</strong></p>
            <div class="signature-line"></div>
            <p><strong>Date:</strong></p>
            <div class="signature-line"></div>
        </div>
        
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        <p><strong>Lana Amawi Coaching Services</strong></p>
        <p>This agreement is valid for the session scheduled on {{ \Carbon\Carbon::parse($preferred_date)->format('F j, Y') }}</p>
        <p>For questions or concerns, please contact: {{ $client_email }}</p>
    </div>
</body>
</html>
