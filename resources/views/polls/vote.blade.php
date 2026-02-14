    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote: {{ $poll->name }}</title>

     {{-- Author & App Info --}}
    <meta name="author" content="ZedBallot">
    <meta name="application-name" content="ZedBallot">

    {{-- Favicons --}}
    <link rel="icon" href="{{ asset('website-asset/assets/img/logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-icon.png') }}">


    <!-- Open Graph Meta Tags for WhatsApp and Facebook -->
    <meta property="og:title" content="{{ $poll->name }}">
    <meta property="og:description" content="{{ $poll->description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($poll->cover_image)
    <meta property="og:image" content="{{ asset('public/storage/'.$poll->cover_image) }}">
    @elseif($poll->banner_image)
    <meta property="og:image" content="{{ asset('public/storage/'.$poll->banner_image) }}">
    @else
    <meta property="og:image" content="{{ asset('assets/images/bg.jpg') }}">
    @endif
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="ZedBallot">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: white;
            background-size: 400% 400%;
            /* animation: gradientBG 5s ease infinite; */
        }

        .poll-header {
            background: linear-gradient(135deg, #00A82A 0%, #ff9800 100%);
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
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background: white;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            backdrop-filter: blur(8px);
        }
        .profile-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            padding: 2.5rem 1.5rem 2rem 1.5rem;
            text-align: center;
            min-height: 260px;
            position: relative;
            backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255,255,255,0.18);
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        
        .profile-card.selected {
            border-color: #ff9800;
            background: #fff;
            box-shadow: 0 0 0 4px #ff980088;
        }

        .profile-card.selected h5 {
            margin-bottom: 0.5rem;
            font-weight: 700;
            color: #ff9800;
            text-shadow: 0 2px 8px rgba(0,0,0,0.18);
        }

        .profile-card h5 {
            margin-bottom: 0.5rem;
            font-weight: 700;
            color: #000;
            text-shadow: 0 2px 8px rgba(0,0,0,0.18);
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
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-active { background: #28a745; color: white; }
        .btn-vote {
            background: linear-gradient(135deg, #00A82A 0%, #ff9800 100%);
            border: none;
            color: white;
            padding: 0.7rem 2rem;
            font-weight: 600;
            border-radius: 6px;
            transition: transform 0.01s ease;
        }
        .btn-vote:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 12px rgba(47, 202, 0, 0.4);
        }
        .poll-dates {
            background: #f8f9fa;
            border-left: 4px solid #00A82A;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="poll-header">
        <div class="container">
                    @if ($errors->any())
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: `{!! implode('<br>', $errors->all()) !!}`,
                                });
                            });
                        </script>
                    @endif

                    @if (session('email_confirmation_sent'))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Email Sent',
                                    text: 'A confirmation email has been sent. Please check your inbox to confirm your vote.',
                                });
                            });
                        </script>
                    @endif
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">{{ $poll->name }}</h1>
                    <p class="mb-0">{{ $poll->description }}</p>
                </div>
                <div class="col-md-4 text-right">
                    <span class="status-badge status-active">{{ strtoupper($poll->computed_status) }}</span>
                </div>

                @if ($poll->computed_status === 'closed')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'info',
                                title: 'Voting Closed',
                                text: 'This poll is now closed. Voting is no longer possible.',
                            });
                        });
                    </script>
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        @if($poll->cover_image)
            <img src="{{ asset('public/storage/'.$poll->cover_image) }}" alt="Cover" class="poll-cover">
        @endif

        @if($poll->banner_image)
            <img src="{{ asset('public/storage/'.$poll->banner_image) }}" alt="Banner" class="poll-banner">
        @endif

        @if($poll->video)
            <div class="mb-4">
                <h5><i class="fas fa-video mr-2"></i>Intro Video</h5>
                <video controls class="w-100" style="max-height: 400px; border-radius: 8px;">
                    <source src="{{ asset('public/storage/'.$poll->video) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @endif

        <div class="poll-dates">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-1"><i class="fas fa-clock mr-2"></i>Voting Period</h6>
                    <p class="mb-0"><strong>Start:</strong> {{ $poll->start_at ? $poll->start_at->format('M d, Y h:i A') : 'N/A' }}</p>
                    <p class="mb-0"><strong>End:</strong> {{ $poll->end_at ? $poll->end_at->format('M d, Y h:i A') : 'N/A' }}</p>
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

        <hr>

        <h3 class="mb-4"><i class="fas fa-list mr-2"></i>Categories & Nominees</h3>


        @if($poll->categories && $poll->categories->count() > 0)
            <form id="votingForm" method="POST" action="{{ route('polls.vote.submit', $poll) }}">
                @csrf
                <hr>

                @foreach($poll->categories as $category)
                    <div class="category-card">
                        <h5 class="mb-3">
                            <i class="fas fa-folder mr-2"></i>{{ $category->name }}
                        </h5>
                        @if($category->description)
                            <p class="text-muted mb-3">{{ $category->description }}</p>
                        @endif

                        @php
                            $categoryNominees = $poll->nominees->where('category_id', $category->id);
                            $maxVotes = $categoryNominees->max('vote_count');
                            $repeatCount = 5;
                            $cards = collect();
                            for ($i = 0; $i < $repeatCount; $i++) {
                                foreach ($categoryNominees as $nominee) {
                                    $cards->push($nominee);
                                }
                            }
                        @endphp

                        @if($categoryNominees->count() > 0)
                            <div class="nominee-carousel-container">
                                <div class="custom-nominee-carousel" id="custom-carousel-{{ $category->id }}" style="overflow: hidden; position: relative; width: 100%;">
                                    <div class="custom-carousel-track d-flex align-items-center justify-content-center" style="transition: transform 0.7s cubic-bezier(.4,2,.6,1); will-change: transform;">
                                        @foreach($cards as $i => $nominee)
                                            <div class="profile-card custom-carousel-card my-5" data-index="{{ $i }}" style="min-width: 320px; max-width: 340px; margin: 0 1rem; position: relative;">
                                                <input type="radio" name="votes[{{ $category->id }}]" value="{{ $nominee->id }}" id="vote-{{ $category->id }}-{{ $nominee->id }}-{{ $i }}" class="visually-hidden" hidden>
                                                @if($nominee->photo)
                                                    <img src="{{ asset('public/storage/'.$nominee->photo) }}" alt="{{ $nominee->name }}" class="profile-photo mb-2">
                                                @else
                                                    <div class="profile-photo mb-2">
                                                        <i class="fas fa-user" style="font-size: 48px; color: #999;"></i>
                                                    </div>
                                                @endif
                                                <h5>{{ $nominee->name }}</h5>
                                                @if($nominee->bio)
                                                    <p class="text-muted small mb-1">{{ Str::limit($nominee->bio, 180) }}</p>
                                                @endif
                                                <!-- @if($nominee->email)
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
                                                @endif -->
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-link custom-carousel-prev" style="position:absolute;left:0;top:50%;transform:translateY(-50%);z-index:10;font-size:2rem;color:#fff;"><i class="fas fa-chevron-left text-warning"></i></button>
                                    <button type="button" class="btn btn-link custom-carousel-next" style="position:absolute;right:0;top:50%;transform:translateY(-50%);z-index:10;font-size:2rem;color:#fff;"><i class="fas fa-chevron-right text-warning"></i></button>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">No nominees in this category.</p>
                        @endif
                    </div>
                @endforeach

                <h4 class="mb-3">Voter Information</h4>
                <input type="hidden" name="poll_id" value="{{ $poll->id }}">
                @php $methods = array_map('trim', explode(',', $poll->voting_methods)); @endphp
                <div class="form-row">
                    @if(in_array('name', $methods) || $poll->is_public)
                    <div class="form-group col-md-6">
                        <label for="voter_name">Name</label>
                        <input type="text" class="form-control" id="voter_name" name="name" required>
                    </div>
                    @endif
                    @if(in_array('email', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_email">Email</label>
                        <input type="email" class="form-control" id="voter_email" name="email" required>
                    </div>
                    @endif
                    @if(in_array('phone', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_phone">Phone</label>
                        <input type="text" class="form-control" id="voter_phone" name="phone" required>
                    </div>
                    @endif
                    @if(in_array('nrc', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_nrc">NRC</label>
                        <input type="text" class="form-control" id="voter_nrc" name="nrc" required>
                    </div>
                    @endif
                    @if(in_array('passport', $methods))
                    <div class="form-group col-md-6">
                        <label for="voter_passport">Passport</label>
                        <input type="text" class="form-control" id="voter_passport" name="passport" required>
                    </div>
                    @endif
                </div>
                @if(count($methods) === 1)
                    <input type="hidden" name="identifier_type" value="{{ $methods[0] }}">
                    <input type="hidden" name="identifier_value" value="{{ $methods[0] === 'email' ? old('email') : ( ($methods[0] === 'phone') ? old('phone') : ( ($methods[0] === 'nrc') ? old('nrc') : old('passport') ) ) }}">
                @else
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="identifier_type">Identifier Type</label>
                            <select class="form-control" id="identifier_type" name="identifier_type" required>
                                @foreach($methods as $method)
                                    <option value="{{ $method }}">{{ ucfirst($method) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="identifier_value">Identifier Value</label>
                            <input type="text" class="form-control" id="identifier_value" name="identifier_value" required>
                        </div>
                    </div>
                @endif

                <div class="text-center mb-4">
                    <button type="submit" class="btn btn-vote btn-lg">
                        <i class="fas fa-check-circle mr-2"></i>Submit Your Vote
                    </button>
                </div>
            </form>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>No categories have been added to this poll yet.
            </div>
        @endif

        <div class="text-center mt-4 mb-4">
            <a href="{{ route('polls.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Polls
            </a>
        </div>
    </div>

    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script>
    // Prevent form submit on Enter except on submit button
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('votingForm');
        if (form) {
            form.addEventListener('keydown', function(e) {
                var tag = e.target.tagName.toLowerCase();
                if (e.key === 'Enter' && (tag === 'input' || tag === 'select')) {
                    // Prevent Enter from submitting the form unless on a textarea or submit button
                    if (e.target.type !== 'submit' && e.target.type !== 'textarea') {
                        e.preventDefault();
                        return false;
                    }
                }
            });
            // Defensive: prevent accidental submit by Enter key on form
            form.addEventListener('submit', function(e) {
                if (document.activeElement && document.activeElement.tagName.toLowerCase() !== 'button') {
                    // Only allow submit if triggered by button
                    // (optional: comment out if you want Enter on button to work)
                    // e.preventDefault();
                }
            });
        }
    });
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

    // Highlight selected profile card
    document.addEventListener('change', function(e) {
        if (e.target.matches('.profile-card input[type="radio"]')) {
            const group = e.target.name;
            const value = e.target.value;
            document.querySelectorAll('input[name="' + group + '"]').forEach(input => {
                const card = input.closest('.profile-card');
                if (input.value === value) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
        }
    });
    // Also allow clicking the card itself to select
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.profile-card').forEach(card => {
            card.onclick = function(e) {
                const radio = card.querySelector('input[type="radio"]');
                if (radio && !radio.checked) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            };
        });
    });
    </script>
</body>
</html>
