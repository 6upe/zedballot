<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Submitted</title>
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <style>
        .poll-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-success { background: #28a745; color: white; }
        .btn-vote {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.7rem 2rem;
            font-weight: 600;
            border-radius: 6px;
            transition: transform 0.2s ease;
        }
        .btn-vote:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="poll-header text-center">
        <h1 class="mb-2">Thank You for Voting!</h1>
    </div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm text-center">
                    <div class="card-body py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                        <h2 class="mb-3">Your vote has been successfully submitted for <strong>{{ $poll->name }}</strong>.</h2>
                        @if(isset($email_confirmation) && $email_confirmation)
                            <div class="alert alert-info mt-4">
                                Please check your email inbox (<strong>{{ $confirmation_email }}</strong>) and click the confirmation button to complete your vote. Your vote will only be counted after confirmation.
                            </div>
                        @endif

                        @if(isset($email_confirmation) && $email_confirmation)
                            <div class="mt-4">
                                <span id="resend-info" class="text-muted">
                                    You can reload this page to resend the confirmation email in <span id="countdown">60</span> seconds.
                                </span>
                                <button id="reload-btn" class="btn btn-link p-0 mt-2" style="display:none;" onclick="location.reload();">Reload Page to Resend Email</button>
                            </div>
                            <script>
                                let seconds = 60;
                                const countdownEl = document.getElementById('countdown');
                                const reloadBtn = document.getElementById('reload-btn');
                                const resendInfo = document.getElementById('resend-info');
                                const timer = setInterval(() => {
                                    seconds--;
                                    countdownEl.textContent = seconds;
                                    if (seconds <= 0) {
                                        clearInterval(timer);
                                        resendInfo.style.display = 'none';
                                        reloadBtn.style.display = 'inline-block';
                                    }
                                }, 1000);
                            </script>
                        @endif


                        <a href="{{ route('polls.index') }}" class="btn btn-vote mt-4">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Polls
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
