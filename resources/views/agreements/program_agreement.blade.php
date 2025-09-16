<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Program Agreement - {{ $program->name }}</title>
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
        .program-info {
            background: #e8f4f8;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .program-info h3 {
            color: #007bff;
            margin-top: 0;
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
        .features-list {
            margin: 10px 0;
        }
        .features-list li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROGRAM AGREEMENT</h1>
        <p><strong>Lana Amawi Coaching Services</strong></p>
        <p>Professional Development Programs for Healthcare Professionals</p>
        <p>Agreement Date: {{ $agreement_date }}</p>
    </div>

    <div class="client-info">
        <h3>Client Information</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        @if($user->phone)
            <p><strong>Phone:</strong> {{ $user->phone }}</p>
        @endif
        <p><strong>Institution:</strong> {{ $user->institution_hospital }}</p>
        <p><strong>Position:</strong> {{ $user->position }}</p>
        <p><strong>Specialty:</strong> {{ $user->specialty }}</p>
    </div>

    <div class="program-info">
        <h3>Selected Program: {{ $program->name }}</h3>
        <p><strong>Description:</strong> {{ $program->description }}</p>
        <p><strong>Duration:</strong> {{ $program->duration_text }}</p>
        <p><strong>Sessions Included:</strong> {{ $program->sessions_text }}</p>
        <p><strong>Program Fee:</strong> {{ $program->formatted_price }}</p>
        
        <h4>Program Features:</h4>
        <ul class="features-list">
            @foreach($program->features as $feature)
                <li>{{ $feature }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>1. PROGRAM TERMS</h2>
        <p>This agreement covers the participation of {{ $user->name }} ("Client") in the {{ $program->name }} provided by Lana Amawi Coaching Services ("Coach"). The program includes {{ $program->sessions_included }} coaching sessions over a period of {{ $program->duration_weeks }} weeks.</p>
    </div>

    <div class="section">
        <h2>2. PAYMENT TERMS</h2>
        <p>The total program fee is {{ $program->formatted_price }}. Payment is due upon program approval and before the first session. Payment can be made via bank transfer, credit card, or other agreed-upon methods.</p>
    </div>

    <div class="section">
        <h2>3. PROGRAM STRUCTURE</h2>
        <p>The program includes:</p>
        <ul>
            <li>{{ $program->sessions_included }} one-on-one coaching sessions</li>
            <li>Personalized action plans and goal setting</li>
            <li>Email support between sessions</li>
            <li>Progress tracking and assessment</li>
            <li>Access to program resources and materials</li>
        </ul>
    </div>

    <div class="section">
        <h2>4. CLIENT RESPONSIBILITIES</h2>
        <p>The Client agrees to:</p>
        <ul>
            <li>Attend all scheduled sessions on time and prepared</li>
            <li>Complete any agreed-upon assignments or exercises</li>
            <li>Communicate openly and honestly during sessions</li>
            <li>Provide 24-hour notice for session cancellations or rescheduling</li>
            <li>Take responsibility for implementing coaching insights and strategies</li>
            <li>Maintain confidentiality of program materials and discussions</li>
        </ul>
    </div>

    <div class="section">
        <h2>5. COACH RESPONSIBILITIES</h2>
        <p>Lana Amawi agrees to:</p>
        <ul>
            <li>Provide professional coaching services in a safe and supportive environment</li>
            <li>Maintain confidentiality as outlined in the privacy policy</li>
            <li>Be punctual and prepared for all scheduled sessions</li>
            <li>Provide 24-hour notice for any session cancellations or rescheduling</li>
            <li>Maintain professional boundaries and ethical standards</li>
            <li>Provide program materials and resources as outlined</li>
        </ul>
    </div>

    <div class="section">
        <h2>6. CONFIDENTIALITY</h2>
        <p>All information shared during program sessions is confidential, except where disclosure is required by law or where there is a risk of harm to the client or others. The coach will maintain the highest standards of confidentiality and privacy.</p>
    </div>

    <div class="section">
        <h2>7. CANCELLATION POLICY</h2>
        <p>Both parties agree to provide at least 24 hours' notice for session cancellations or rescheduling. Late cancellations may result in session fees being charged. Emergency situations will be handled on a case-by-case basis.</p>
        <p>Program cancellation after commencement will be handled according to the following terms:</p>
        <ul>
            <li>Before first session: Full refund minus administrative fee</li>
            <li>After first session: Pro-rated refund based on sessions completed</li>
            <li>After 50% completion: No refund available</li>
        </ul>
    </div>

    <div class="section">
        <h2>8. LIMITATIONS</h2>
        <p>Coaching is not a substitute for professional medical, psychological, or legal advice. If the client requires such services, they should seek appropriate professional help. The coach will refer clients to appropriate professionals when necessary.</p>
    </div>

    <div class="section">
        <h2>9. AGREEMENT</h2>
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
        <p>This agreement is valid for the {{ $program->name }} program</p>
        <p>For questions or concerns, please contact: {{ $user->email }}</p>
    </div>
</body>
</html>
