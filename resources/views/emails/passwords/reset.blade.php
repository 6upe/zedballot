<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
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
        <h2>Password Reset Request</h2>
        <p class="muted">Hello {{ $notifiable->name ?? '' }},</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>

        <p style="text-align:center; margin:24px 0;">
          <a href="{{ $url }}" class="btn">Reset Password</a>
        </p>

        <p class="muted">This password reset link will expire in {{ $expire }} minutes.</p>
        <p class="muted">If you did not request a password reset, no further action is required.</p>
      </div>
      <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} — If you have trouble clicking the button, copy and paste the URL below into your web browser:<br>
        <a href="{{ $url }}">{{ $url }}</a>
      </div>
    </div>
  </body>
  </html>
