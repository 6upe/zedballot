<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Confirmed</title>
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <style>
        .poll-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
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
                        <h2 class="mb-3">Vote Confirmed!</h2>
                        <p>Your vote for the poll <strong>{{ $poll->title ?? $poll->name }}</strong> has been successfully confirmed and counted.</p>
                        <a href="/" class="btn btn-vote mt-4">
                            <i class="fas fa-arrow-left mr-2"></i>Return Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
