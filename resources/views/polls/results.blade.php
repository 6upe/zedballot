
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results: {{ $poll->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(-45deg, #0f3d0f, #ff9800, #d32f2f, #000 90%);
            background-size: 400% 400%;
            animation: gradientBG 18s ease infinite;
        }
        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
        .poll-header {
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-bottom: 2px solid #ff9800;
        }
        .poll-cover {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.25);
        }
        .poll-banner {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        }
        .category-card {
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background: rgba(30,30,30,0.55);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            backdrop-filter: blur(8px);
        }
        .nominee-carousel-container {
            position: relative;
            width: 100%;
            margin: 0 auto 2rem auto;
            min-height: 320px;
        }
        .carousel-inner {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-item {
            flex: 0 0 60%;
            max-width: 60%;
            opacity: 0.4;
            transform: scale(0.85);
            transition: transform 0.7s cubic-bezier(.4,2,.6,1), opacity 0.4s;
            z-index: 1;
            pointer-events: none;
            display: none;
        }
        .carousel-item.active {
            opacity: 1;
            transform: scale(1.08);
            z-index: 3;
            pointer-events: auto;
            display: block;
        }
        .carousel-item-next, .carousel-item-prev {
            opacity: 0.7;
            transform: scale(0.95);
            z-index: 2;
            display: block;
        }
        .carousel-item-next {
            margin-left: 60%;
        }
        .carousel-item-prev {
            margin-right: 60%;
        }
        .profile-card {
            background: rgba(0, 0, 0, 0.18);
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            padding: 2.5rem 1.5rem 2rem 1.5rem;
            text-align: center;
            min-height: 260px;
            position: relative;
            backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255,255,255,0.18);
        }
        .profile-photo {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto 1rem auto;
            border: 4px solid #ff9800;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 12px rgba(255,152,0,0.12);
        }
        .profile-card h5 {
            margin-bottom: 0.5rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.18);
        }
        .profile-card .badge {
            font-size: 1em;
            margin-bottom: 0.5rem;
            background: rgba(255,152,0,0.85);
            color: #fff;
            box-shadow: 0 2px 8px rgba(255,152,0,0.12);
        }
        .profile-card .progress {
            height: 14px;
            background: rgba(255,255,255,0.18);
        }
        .profile-card .progress-bar {
            background: linear-gradient(90deg, #43ea7f, #ff9800, #d32f2f);
        }
        .carousel-control-prev, .carousel-control-next {
            width: 7%;
        }
        @media (max-width: 768px) {
            .carousel-item, .carousel-item.active, .carousel-item-next, .carousel-item-prev {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
            <script>
            // Restore Bootstrap carousel sliding and show prev/next cards with scale effect
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.nominees-carousel').forEach(function(carousel) {
                    carousel.addEventListener('slide.bs.carousel', function (e) {
                        // Remove all custom margin classes
                        let items = carousel.querySelectorAll('.carousel-item');
                        items.forEach(function(item) {
                            item.style.marginLeft = '';
                            item.style.marginRight = '';
                        });
                    });
                    carousel.addEventListener('slid.bs.carousel', function (e) {
                        let items = carousel.querySelectorAll('.carousel-item');
                        items.forEach(function(item) {
                            item.style.marginLeft = '';
                            item.style.marginRight = '';
                        });
                        let active = carousel.querySelector('.carousel-item.active');
                        let prev = active ? active.previousElementSibling : null;
                        let next = active ? active.nextElementSibling : null;
                        if (prev) prev.style.marginRight = '60%';
                        if (next) next.style.marginLeft = '60%';
                    });
                    // Initial state
                    let active = carousel.querySelector('.carousel-item.active');
                    let prev = active ? active.previousElementSibling : null;
                    let next = active ? active.nextElementSibling : null;
                    if (prev) prev.style.marginRight = '60%';
                    if (next) next.style.marginLeft = '60%';
                });
            });
            </script>
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-active { background: #28a745; color: white; }
        .poll-dates {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }
        .winner-summary {
            background: #f0f2ff;
            border-left: 4px solid #764ba2;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .winner-badge {
            background: #764ba2;
            color: #fff;
            border-radius: 20px;
            padding: 0.4rem 1.2rem;
            font-size: 1em;
            margin-left: 1rem;
        }

        .custom-carousel-track {
            display: flex;
            align-items: center;
            transition: transform 0.7s cubic-bezier(.4,2,.6,1);
            will-change: transform;
        }

        .custom-carousel-card {
            flex: 0 0 auto;
        }


    </style>
</head>
<body>
    <div class="poll-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">{{ $poll->name }}</h1>
                    <p class="mb-0">{{ $poll->description }}</p>
                </div>
                <div class="col-md-4 text-right">
                    <span class="status-badge status-active">{{ strtoupper($poll->computed_status) }}</span>
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

        <div class="poll-dates">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-1"><i class="fas fa-clock mr-2"></i>Voting Period</h6>
                    <p class="mb-0"><strong>Start:</strong> {{ $poll->start_at ? \Carbon\Carbon::parse($poll->start_at)->format('M d, Y h:i A') : 'N/A' }}</p>
                    <p class="mb-0"><strong>End:</strong> {{ $poll->end_at ? \Carbon\Carbon::parse($poll->end_at)->format('M d, Y h:i A') : 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-1"><i class="fas fa-check-circle mr-2"></i>Voting Methods</h6>
                    <p class="mb-0">
                        @if($poll->voting_methods)
                            @foreach(explode(',', $poll->voting_methods) as $method)
                                <span class="badge badge-info mr-1">{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Not configured</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="winner-summary">
            <h4 class="mb-3"><i class="fas fa-trophy mr-2"></i>Who is winning in each category?</h4>
            <div class="row">
                @foreach($poll->categories as $category)
                    @php
                        $nominees = $category->nominees;
                        $winner = $nominees->sortByDesc('vote_count')->first();
                    @endphp
                    <div class="col-md-6 mb-3">
                        <strong>{{ $category->name }}:</strong>
                        @if($winner)
                            <span class="winner-badge">
                                {{ $winner->name }} ({{ $winner->vote_count }} votes)
                            </span>
                        @else
                            <span class="text-muted">No votes yet</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <h3 class="mb-4"><i class="fas fa-list mr-2"></i>Categories & Nominees</h3>

        @foreach($poll->categories as $category)
            <div class="category-card">
                <h5 class="mb-3">
                    <i class="fas fa-folder mr-2"></i>{{ $category->name }}
                </h5>
                @if($category->description)
                    <p class="text-muted mb-3">{{ $category->description }}</p>
                @endif

                @php
                    $categoryNominees = $category->nominees;
                    $maxVotes = $categoryNominees->max('vote_count');
                @endphp

                @if($categoryNominees->count() > 0)
                    <div class="nominee-carousel-container">
                        <div class="custom-nominee-carousel" id="custom-carousel-{{ $category->id }}" style="overflow: hidden; position: relative; width: 100%;">
                            <div class="custom-carousel-track d-flex align-items-center justify-content-center" style="transition: transform 0.7s cubic-bezier(.4,2,.6,1); will-change: transform;">
                               @php
                                    $original = $categoryNominees;
                                    $repeatCount = 5; // enough to simulate infinity
                                    $cards = collect();

                                    for ($i = 0; $i < $repeatCount; $i++) {
                                        foreach ($original as $nominee) {
                                            $cards->push($nominee);
                                        }
                                    }
                                @endphp

                                @foreach($cards as $i => $nominee)
                                    <div class="profile-card custom-carousel-card" data-index="{{ $i }}" style="min-width: 320px; max-width: 340px; margin: 0 1rem;">
                                        @if($nominee->photo)
                                            <img src="{{ asset('storage/'.$nominee->photo) }}" alt="{{ $nominee->name }}" class="profile-photo mb-2">
                                        @else
                                            <div class="profile-photo mb-2" style="display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-user" style="font-size: 48px; color: #999;"></i>
                                            </div>
                                        @endif
                                        <h5>{{ $nominee->name }}</h5>
                                        @if($nominee->bio)
                                            <p class="text-muted small mb-1">{{ Str::limit($nominee->bio, 180) }}</p>
                                        @endif
                                        @if($nominee->email)
                                            <p class="text-muted small mb-1"><i class="fas fa-envelope mr-1"></i>{{ $nominee->email }}</p>
                                        @endif
                                        @if($nominee->phone)
                                            <p class="text-muted small mb-1"><i class="fas fa-phone mr-1"></i>{{ $nominee->phone }}</p>
                                        @endif
                                        @if($nominee->social_link)
                                            <p class="small mb-1">
                                                <a href="{{ $nominee->social_link }}" target="_blank" rel="noopener">
                                                    <i class="fas fa-external-link-alt mr-1"></i>Profile
                                                </a>
                                            </p>
                                        @endif
                                        <span class="badge badge-pill badge-success" style="font-size:1.1em;">{{ $nominee->vote_count }} votes</span>
                                        <div class="progress mt-2">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $maxVotes > 0 ? round(($nominee->vote_count/$maxVotes)*100) : 0 }}%" aria-valuenow="{{ $nominee->vote_count }}" aria-valuemin="0" aria-valuemax="{{ $maxVotes }}">
                                                {{ $maxVotes > 0 ? round(($nominee->vote_count/$maxVotes)*100) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-link custom-carousel-prev" style="position:absolute;left:0;top:50%;transform:translateY(-50%);z-index:10;font-size:2rem;color:#fff;"><i class="fas fa-chevron-left"></i></button>
                            <button class="btn btn-link custom-carousel-next" style="position:absolute;right:0;top:50%;transform:translateY(-50%);z-index:10;font-size:2rem;color:#fff;"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                @else
                    <p class="text-muted">No nominees in this category.</p>
                @endif
            </div>
        @endforeach

        <div class="text-center mt-4 mb-4">
            <a href="{{ route('polls.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Polls
            </a>
        </div>
    </div>

    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script>
    // Custom nominee carousel for smooth sliding, always centered, and infinite loop for 1/2 nominees
   document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.custom-nominee-carousel').forEach(carousel => {

        const track = carousel.querySelector('.custom-carousel-track');
        const cards = carousel.querySelectorAll('.custom-carousel-card');
        const prevBtn = carousel.querySelector('.custom-carousel-prev');
        const nextBtn = carousel.querySelector('.custom-carousel-next');

        const total = cards.length;
        const realCount = total / 5; // original nominees count

        // Start in the middle block
        let current = Math.floor(total / 2);

        function update(animate = true) {
            track.style.transition = animate
                ? 'transform 0.7s cubic-bezier(.4,2,.6,1)'
                : 'none';

            const carouselCenter = carousel.clientWidth / 2;
            const card = cards[current];
            const cardCenter = card.offsetLeft + card.offsetWidth / 2;
            const offset = carouselCenter - cardCenter;

            track.style.transform = `translateX(${offset}px)`;

            cards.forEach((c, i) => {
                const d = Math.abs(i - current);
                c.style.opacity = d === 0 ? '1' : d === 1 ? '0.6' : '0.3';
                c.style.transform = d === 0 ? 'scale(1.08)' : d === 1 ? 'scale(0.95)' : 'scale(0.85)';
                c.style.zIndex = d === 0 ? '3' : d === 1 ? '2' : '1';
            });
        }

        function jumpIfNeeded() {
            // Jump silently when leaving middle block
            if (current < realCount) {
                current += realCount * 2;
                update(false);
            }
            if (current >= total - realCount) {
                current -= realCount * 2;
                update(false);
            }
        }

        nextBtn.addEventListener('click', () => {
            current++;
            update();
            setTimeout(jumpIfNeeded, 750);
        });

        prevBtn.addEventListener('click', () => {
            current--;
            update();
            setTimeout(jumpIfNeeded, 750);
        });

        // Auto slide
        let auto = setInterval(() => nextBtn.click(), 3500);
        carousel.addEventListener('mouseenter', () => clearInterval(auto));
        carousel.addEventListener('mouseleave', () => {
            auto = setInterval(() => nextBtn.click(), 3500);
        });

        // Initial
        update(false);
        window.addEventListener('resize', () => update(false));
    });
});

    </script>
</body>
</html>
