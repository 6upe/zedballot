<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $poll->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <style>
        .poll-preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .poll-cover {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .poll-banner {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .category-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: #f9f9f9;
        }
        .nominee-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-draft { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="poll-preview-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">{{ $poll->name }}</h1>
                    <p class="mb-0">{{ $poll->description }}</p>
                </div>
                <div class="col-md-4 text-right">
                    <span class="status-badge status-draft">DRAFT PREVIEW</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        @if($poll->cover_image)
            <img src="{{ asset('storage/'.$poll->cover_image) }}" alt="Cover" class="poll-cover">
        @endif

        @if($poll->banner_image)
            <img src="{{ asset('storage/'.$poll->banner_image) }}" alt="Banner" class="poll-banner">
        @endif

        @if($poll->video)
            <div class="mb-4">
                <h5>Intro Video</h5>
                <video controls class="w-100" style="max-height: 400px; border-radius: 8px;">
                    <source src="{{ asset('storage/'.$poll->video) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <h5><i class="fas fa-calendar-alt mr-2"></i>Poll Dates</h5>
                <p class="mb-1"><strong>Start:</strong> {{ $poll->start_at ? $poll->start_at->format('M d, Y h:i A') : 'Not set' }}</p>
                <p class="mb-0"><strong>End:</strong> {{ $poll->end_at ? $poll->end_at->format('M d, Y h:i A') : 'Not set' }}</p>
            </div>
            <div class="col-md-6">
                <h5><i class="fas fa-vote-yea mr-2"></i>Voting Methods</h5>
                <p class="mb-0">
                    @if($poll->voting_methods)
                        @foreach(explode(',', $poll->voting_methods) as $method)
                            <span class="badge badge-primary mr-1">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">Not configured</span>
                    @endif
                </p>
            </div>
        </div>

        <hr>

        <h4 class="mb-4">Categories & Nominees</h4>

        @if($poll->categories && $poll->categories->count() > 0)
            @foreach($poll->categories as $category)
                <div class="category-card">
                    <h5>{{ $category->name }}</h5>
                    @if($category->description)
                        <p class="text-muted">{{ $category->description }}</p>
                    @endif

                    @php
                        $categoryNominees = $poll->nominees->where('category_id', $category->id);
                    @endphp

                    @if($categoryNominees->count() > 0)
                        <div class="row mt-3">
                            @foreach($categoryNominees as $nominee)
                                <div class="col-md-6">
                                    <div class="nominee-card">
                                        @if($nominee->photo)
                                            <img src="{{ asset('storage/'.$nominee->photo) }}" alt="{{ $nominee->name }}" 
                                                 class="img-fluid rounded mb-2" style="max-height: 100px;">
                                        @endif
                                        <h6 class="mb-1">{{ $nominee->name }}</h6>
                                        @if($nominee->bio)
                                            <p class="text-muted small mb-1">{{ Str::limit($nominee->bio, 100) }}</p>
                                        @endif
                                        <span class="badge badge-{{ $nominee->status === 'approved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($nominee->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No nominees in this category yet.</p>
                    @endif
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>No categories have been added to this poll yet.
            </div>
        @endif

        <div class="alert alert-warning mt-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>This is a preview of a draft poll.</strong> Voting is not available until the poll is published.
        </div>

        <div class="text-center mt-4 mb-4">
            <a href="{{ route('polls.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Polls
            </a>
        </div>
    </div>

    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
</body>
</html>

