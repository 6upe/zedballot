@extends('layouts.website')

@section('title', 'Welcome to ZedBallot')

@section('content')
<main>
    <nav class="navbar navbar-expand-lg navbar-light bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center me-lg-5 me-0" href="{{ url('/') }}">
                <img src="{{ asset('website-assets/images/pod-talk-logo.png') }}" alt="ZedBallot" class="me-2" style="height:40px;">
                <span>ZedBallot</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-lg-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('polls*') ? 'active' : '' }}" href="/">Polls</a></li>
                    <li class="nav-item"><a class="nav-link" href="/about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Contact</a></li>
                </ul>
                <div class="ms-lg-4 mt-3 mt-lg-0 d-flex gap-2">
                    @auth
                    <a href="/dashboard" class="btn btn-outline-primary">Dashboard</a>
                    @else
                    <a href="/login" class="btn btn-outline-primary">Login</a>
                    <a href="/polls/create" class="btn btn-primary">Create Poll</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section py-5" style="background: linear-gradient(120deg, #007bff 0%, #00c6ff 100%); color: #fff;">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Modern, Secure Online Voting</h1>
            <p class="lead mb-4">ZedBallot empowers organizations, communities, and teams to run transparent, trusted, and easy-to-manage online elections and polls.</p>
            <a href="/register" class="btn btn-lg btn-light fw-bold shadow">Get Started Free</a>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Secure & Private</h5>
                    <p class="text-muted">End-to-end encryption, robust authentication, and audit trails ensure every vote is safe and verifiable.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Accessible Anywhere</h5>
                    <p class="text-muted">Fully responsive and mobile-friendly, so everyone can participate from any device, anywhere.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Instant Results</h5>
                    <p class="text-muted">Get real-time analytics and results as votes come in. Visualize outcomes with clear, interactive reports and charts.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">How ZedBallot Works</h2>
                <p class="text-muted">Simple, secure, and designed for trust.</p>
            </div>
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="p-4 border rounded h-100">
                        <span class="badge bg-primary mb-3">Step 1</span>
                        <h5 class="fw-bold">Create a Poll</h5>
                        <p class="text-muted">Admins define categories, nominees, voting rules, and timelines in minutes.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 border rounded h-100">
                        <span class="badge bg-primary mb-3">Step 2</span>
                        <h5 class="fw-bold">Verify & Vote</h5>
                        <p class="text-muted">Voters verify their identity and cast one secure vote per category.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 border rounded h-100">
                        <span class="badge bg-primary mb-3">Step 3</span>
                        <h5 class="fw-bold">View Results</h5>
                        <p class="text-muted">Results are calculated instantly and displayed transparently.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Active Polls</h3>
                <a href="{{ route('polls.index') }}" class="text-decoration-none">View All →</a>
            </div>
            <div class="row">
                @forelse($activePolls ?? [] as $poll)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        @if($poll->cover_image)
                        <img src="{{ asset('storage/'.$poll->cover_image) }}" class="card-img-top" style="height:200px;object-fit:cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $poll->name }}</h5>
                            <p class="text-muted small">Ends {{ $poll->end_at?->diffForHumans() }}</p>
                            <a href="{{ route('polls.vote', $poll) }}" class="btn btn-primary w-100">Vote Now</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center text-muted">No active polls at the moment.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-5 text-white" style="background:linear-gradient(135deg,#0f172a,#1e293b);">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Ready to Run a Trusted Vote?</h2>
            <p class="mb-4">Create transparent, secure polls with confidence using ZedBallot.</p>
            <a href="{{ route('polls.create') }}" class="btn btn-light btn-lg fw-semibold">Get Started</a>
        </div>
    </section>
</main>

<footer class="site-footer bg-white border-top mt-5 pt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-12 mb-5 mb-lg-0">
                <div class="subscribe-form-wrap">
                    <h6>Subscribe for Updates</h6>
                    <form class="custom-form subscribe-form" action="#" method="get" role="form">
                        <input type="email" name="subscribe-email" id="subscribe-email" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="Email Address" required="">
                        <div class="col-lg-12 col-12">
                            <button type="submit" class="form-control" id="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12 mb-4 mb-md-0 mb-lg-0">
                <h6 class="site-footer-title mb-3">Contact</h6>
                <p class="mb-2"><strong class="d-inline me-2">Phone:</strong> 010-020-0340</p>
                <p><strong class="d-inline me-2">Email:</strong> <a href="#">info@zedballot.com</a></p>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <h6 class="site-footer-title mb-3">Social</h6>
                <ul class="social-icon">
                    <li class="social-icon-item"><a href="#" class="social-icon-link bi-instagram"></a></li>
                    <li class="social-icon-item"><a href="#" class="social-icon-link bi-twitter"></a></li>
                    <li class="social-icon-item"><a href="#" class="social-icon-link bi-whatsapp"></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container pt-5">
        <div class="row align-items-center">
            <div class="col-lg-2 col-md-3 col-12">
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('website-assets/images/pod-talk-logo.png') }}" class="logo-image img-fluid" alt="ZedBallot logo">
                </a>
            </div>
            <div class="col-lg-7 col-md-9 col-12">
                <ul class="site-footer-links">
                    <li class="site-footer-link-item"><a href="#" class="site-footer-link">Homepage</a></li>
                    <li class="site-footer-link-item"><a href="#" class="site-footer-link">Help Center</a></li>
                    <li class="site-footer-link-item"><a href="#" class="site-footer-link">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-12">
                <p class="copyright-text mb-0">Copyright © {{ date('Y') }} ZedBallot</p>
            </div>
        </div>
    </div>
</footer>
@endsection