{{-- filepath: resources/views/emails/vote_confirmation.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vote Confirmation</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f7fafc; color:#2d3748; }
        .container { max-width:600px; margin:32px auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
        .header { padding:24px; text-align:center; background:#fff; }
        .content { padding:24px; }
        .btn { display:inline-block; padding:12px 20px; background:#4f46e5; color:#fff; border-radius:6px; text-decoration:none; }
        .muted { color:#718096; font-size:14px; }
        .footer { padding:16px; text-align:center; font-size:12px; color:#a0aec0; }
        img.logo { width:96px; height:auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/img/logo-word.png') }}" alt="{{ config('app.name') }}" class="logo">
        </div>
        <div class="content">
            <h2>Vote Confirmation</h2>
            <p class="muted">Hello {{ $voter->name ?? 'Voter' }},</p>
            <p>Thank you for voting in the poll: <strong>{{ $poll->title }}</strong></p>
            <p>To confirm your vote, please click the button below:</p>
            <p style="text-align:center; margin:24px 0;">
                <a href="{{ $confirmationUrl }}" class="btn">Confirm Vote</a>
            </p>
            <p class="muted">If you did not submit this vote, you can ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>