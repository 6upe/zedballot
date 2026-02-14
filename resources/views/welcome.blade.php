<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Dynamic Page Title --}}
    <title>@yield('title', 'ZedBallot — Secure Online Voting & Polling Platform')</title>

    {{-- Primary Meta --}}
    <meta name="description" content="@yield('meta_description', 'ZedBallot is a secure, transparent, and easy-to-use online voting and polling platform designed for organizations, institutions, and public elections.')">
    <meta name="keywords" content="online voting, polling system, secure elections, digital ballots, voting platform, Zambia voting system">

    {{-- Author & App Info --}}
    <meta name="author" content="ZedBallot">
    <meta name="application-name" content="ZedBallot">

    <!-- Social Media Meta Tags -->
    <meta property="og:title" content="@yield('title', 'ZedBallot — Secure Online Voting & Polling Platform')" />
    <meta property="og:description" content="@yield('meta_description', 'ZedBallot is a secure, transparent, and easy-to-use online voting and polling platform designed for organizations, institutions, and public elections.')" />
    <meta property="og:image" content="{{ asset('website-asset/assets/img/logo-icon.png') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('title', 'ZedBallot — Secure Online Voting & Polling Platform')" />
    <meta name="twitter:description" content="@yield('meta_description', 'ZedBallot is a secure, transparent, and easy-to-use online voting and polling platform designed for organizations, institutions, and public elections.')" />
    <meta name="twitter:image" content="{{ asset('website-asset/assets/img/logo-icon.png') }}" />

    {{-- Favicons --}}
    <link rel="icon" href="{{ asset('website-asset/assets/img/logo-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-icon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?
        family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,300;1,400;1,500;1,700;1,900&
        family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700&
        family=Raleway:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700
        &display=swap"
        rel="stylesheet">

    {{-- Vendor CSS --}}
    <link href="{{ asset('website-asset/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website-asset/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('website-asset/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('website-asset/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('website-asset/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    {{-- Main Styles --}}
    <link href="{{ asset('website-asset/assets/css/main.css') }}" rel="stylesheet">

    {{-- Extra Page Styles --}}
    @stack('styles')
</head>


<body class="index-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
            {{-- Image logo (optional) --}}
            <img src="{{ asset('website-asset/assets/img/logo-word.png') }}" alt="ZedBallot Logo">
            <!-- <h1 class="sitename">Zed<span>Ballot</span></h1> -->
        </a>

        {{-- Navigation --}}
        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="#hero" class="active">Home</a></li>

                <li><a href="#about">About</a></li>

                <li><a href="#how-it-works">How It Works</a></li>

                <li><a href="#security">Security</a></li>

                <li><a href="#contact">Contact</a></li>

               
            </ul>

            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

         {{-- Auth-ready links (optional later) --}}
              
                @auth
                    <a class="btn-getstarted" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="btn-getstarted" href="{{ route('login') }}">Create a Poll</a>
                @endauth
                
     
        

    </div>
</header>


  <main class="main">

<!-- Hero Section -->
<section id="hero" class="hero section">

    {{-- Background Image --}}
    <img src="{{ asset('website-asset/assets/img/hero-bg-abstract.jpg') }}" alt="ZedBallot Secure Online Voting" data-aos="fade-in">

    <div class="container">
        {{-- Main Heading --}}
        <div class="row justify-content-center" data-aos="zoom-out">
            <div class="col-xl-8 col-lg-9 text-center">
                <h1>Secure, Transparent & Smart Online Voting</h1>
                <p>
                    ZedBallot is a modern online polling and election platform designed for
                    organizations, institutions, communities, and awards — enabling fair,
                    time-based, and fraud-resistant voting from anywhere.
                </p>
            </div>
        </div>

        {{-- Primary CTA --}}
        <div class="text-center" data-aos="zoom-out" data-aos-delay="100">
            <a href="/login" class="btn-get-started">Create Your First Poll</a>
        </div>

        {{-- Feature Highlights --}}
        <div class="row gy-4 mt-5">

            <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="100">
                <div class="icon-box">
                    <div class="icon"><i class="bi bi-shield-lock"></i></div>
                    <h4 class="title">Secure Voting</h4>
                    <p class="description">
                        Built with strong validation, eligibility control, and one-vote-per-voter enforcement.
                    </p>
                </div>
            </div><!-- End Icon Box -->

            <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="200">
                <div class="icon-box">
                    <div class="icon"><i class="bi bi-clock-history"></i></div>
                    <h4 class="title">Time-Based Polls</h4>
                    <p class="description">
                        Schedule polls with precise start and end times that automatically open and close.
                    </p>
                </div>
            </div><!-- End Icon Box -->

            <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="300">
                <div class="icon-box">
                    <div class="icon"><i class="bi bi-people"></i></div>
                    <h4 class="title">Flexible Eligibility</h4>
                    <p class="description">
                        Public or private polls with email, ID, CSV imports, or self-registration links.
                    </p>
                </div>
            </div><!-- End Icon Box -->

            <div class="col-md-6 col-lg-3" data-aos="zoom-out" data-aos-delay="400">
                <div class="icon-box">
                    <div class="icon"><i class="bi bi-bar-chart-line"></i></div>
                    <h4 class="title">Live Results</h4>
                    <p class="description">
                        View real-time results with transparent vote counts and category breakdowns.
                    </p>
                </div>
            </div><!-- End Icon Box -->

        </div>
    </div>

</section>
<!-- /Hero Section -->


<!-- About Section -->
<section id="about" class="about section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>    <img src="{{ asset('website-asset/assets/img/logo-word.png') }}" alt="ZedBallot Logo" width="50%"></h2>
        <p>A trusted digital voting platform built for fairness, transparency, and scale</p>
    </div><!-- End Section Title -->

    <div class="container">

        <div class="row gy-4">

            {{-- Left Content --}}
            <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                <p>
                    ZedBallot was created to modernize how organizations conduct polls, elections,
                    and voting-based decisions. From awards and associations to institutions and
                    private organizations, we provide a secure and reliable way to collect votes online.
                </p>

                <ul>
                    <li>
                        <i class="bi bi-check2-circle"></i>
                        <span>Designed to prevent duplicate voting and enforce eligibility rules.</span>
                    </li>
                    <li>
                        <i class="bi bi-check2-circle"></i>
                        <span>Supports public and private polls with flexible voter identification.</span>
                    </li>
                    <li>
                        <i class="bi bi-check2-circle"></i>
                        <span>Automatically manages poll timing, status, and result visibility.</span>
                    </li>
                </ul>
            </div>

            {{-- Right Content --}}
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <p>
                    Unlike traditional voting tools, ZedBallot is built with real-world challenges in mind —
                    time zones, voter verification, data integrity, and scalability. Every poll is backed
                    by structured logic that ensures each voter participates only once, and every vote
                    is counted accurately.
                </p>

                <p>
                    Whether you are running a small internal vote or a large public poll,
                    ZedBallot gives you confidence, clarity, and control — all from a simple,
                    intuitive interface.
                </p>

                <a href="/register" class="read-more">
                    <span>Sign up Now!</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        </div>

    </div>

</section>
<!-- /About Section -->


<!-- Stats Section -->
<section id="security" class="stats section light-background">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

            <!-- Total Polls -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-item text-center w-100 h-100">
                    <span
                        data-purecounter-start="0"
                        data-purecounter-end="120"
                        data-purecounter-duration="1.2"
                        class="purecounter"></span>
                    <p>Polls Created</p>
                </div>
            </div><!-- End Stats Item -->

            <!-- Votes Cast -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-item text-center w-100 h-100">
                    <span
                        data-purecounter-start="0"
                        data-purecounter-end="18500"
                        data-purecounter-duration="1.4"
                        class="purecounter"></span>
                    <p>Votes Successfully Cast</p>
                </div>
            </div><!-- End Stats Item -->

            <!-- Organizations -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-item text-center w-100 h-100">
                    <span
                        data-purecounter-start="0"
                        data-purecounter-end="48"
                        data-purecounter-duration="1"
                        class="purecounter"></span>
                    <p>Organizations Using ZedBallot</p>
                </div>
            </div><!-- End Stats Item -->

            <!-- System Reliability -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-item text-center w-100 h-100">
                    <span
                        data-purecounter-start="0"
                        data-purecounter-end="99"
                        data-purecounter-duration="1"
                        class="purecounter"></span>
                    <p>% Voting Uptime</p>
                </div>
            </div><!-- End Stats Item -->

        </div>

    </div>

</section>
<!-- /Stats Section -->



<!-- Call To Action Section -->
<section id="contact" class="call-to-action section accent-background">

    <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
            <div class="col-xl-10">
                <div class="text-center">

                    <h3>Launch Your Poll in Minutes</h3>

                    <p>
                        Create secure, transparent, and time-controlled polls for organizations,
                        communities, awards, or elections. ZedBallot gives you full control over
                        voter eligibility, voting windows, and real-time results — without complexity.
                    </p>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a class="cta-btn" href="{{ route('polls.create') }}">
                            Create a Poll
                        </a>

                        <a class="cta-btn cta-btn-outline" href="#about">
                            Learn How It Works
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>
<!-- /Call To Action Section -->


<!-- Faq Section -->
<section id="how-it-works" class="faq section light-background">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Frequently Asked Questions</h2>
        <p>Everything you need to know before creating or participating in a poll.</p>
    </div>
    <!-- End Section Title -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

                <div class="faq-container">

                    <!-- FAQ Item -->
                    <div class="faq-item faq-active">
                        <h3>How does ZedBallot ensure voting fairness?</h3>
                        <div class="faq-content">
                            <p>
                                Each poll enforces strict voting rules. Voters are uniquely identified
                                using configured identifiers (email, phone, NRC, passport, or system ID),
                                ensuring one person can only vote once per poll.
                            </p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div>

                    <!-- FAQ Item -->
                    <div class="faq-item">
                        <h3>What is the difference between public and private polls?</h3>
                        <div class="faq-content">
                            <p>
                                Public polls allow anyone with the poll link to vote, while private polls
                                restrict participation to pre-approved voters defined by the poll organizer.
                                Eligibility is verified before any vote is accepted.
                            </p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div>

                    <!-- FAQ Item -->
                    <div class="faq-item">
                        <h3>Can voters change their votes?</h3>
                        <div class="faq-content">
                            <p>
                                This depends on the poll configuration. Organizers may allow or disallow
                                vote editing. Once the voting window closes, all votes are permanently locked.
                            </p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div>

                    <!-- FAQ Item -->
                    <div class="faq-item">
                        <h3>How does ZedBallot handle timezones?</h3>
                        <div class="faq-content">
                            <p>
                                All poll timing is controlled by server time to prevent manipulation.
                                While users may be in different timezones, poll start and end times
                                are enforced consistently to guarantee fairness.
                            </p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div>

                    <!-- FAQ Item -->
                    <div class="faq-item">
                        <h3>What happens when a poll closes?</h3>
                        <div class="faq-content">
                            <p>
                                Once a poll reaches its end time, voting automatically stops.
                                Results become available immediately or remain hidden depending
                                on the organizer’s settings.
                            </p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div>

                    <!-- FAQ Item -->
                    <div class="faq-item">
                        <h3>Is my data secure?</h3>
                        <div class="faq-content">
                            <p>
                                Yes. ZedBallot follows best practices for data protection.
                                Sensitive voter identifiers are stored securely and are never
                                shared or exposed publicly.
                            </p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div>

                </div>

            </div>
        </div>
    </div>

</section>
<!-- /Faq Section -->


  </main>

<footer id="footer" class="footer light-background">

    <div class="container footer-top">
        <div class="row gy-4">

            <!-- Brand / About -->
            <div class="col-lg-5 col-md-12 footer-about">
                <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                            <h2>    <img src="{{ asset('website-asset/assets/img/logo-word.png') }}" alt="ZedBallot Logo" width="100%"></h2>
                </a>
                <p>
                    ZedBallot is a secure, transparent, and flexible online voting platform
                    designed for elections, awards, surveys, and decision-making at any scale.
                </p>

            </div>

            <!-- Useful Links -->
            <div class="col-lg-2 col-6 footer-links">
                <h4>Platform</h4>
                <ul>
                    <li><a href="#hero">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Features</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            <!-- Services / Features -->
            <div class="col-lg-2 col-6 footer-links">
                <h4>Solutions</h4>
                <ul>
                    <li><a href="#">Online Voting</a></li>
                    <li><a href="#">Private Elections</a></li>
                    <li><a href="#">Awards & Polls</a></li>
                    <li><a href="#">Voter Verification</a></li>
                    <li><a href="#">Live Results</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                <h4>Contact</h4>
                <p>Zambia</p>
                <p class="mt-4">
                    <strong>Email:</strong>
                    <span>support@zedballot.com</span>
                </p>
                <p>
                    <strong>Support:</strong>
                    <span>24/7 Online Assistance</span>
                </p>
            </div>

        </div>
    </div>

    <!-- Copyright -->
    <div class="container copyright text-center mt-4">
        <p>
            © {{ date('Y') }}
            <strong class="px-1 sitename">ZedBallot</strong>
            <span>All Rights Reserved</span>
        </p>

        <div class="credits">
            Built with security, transparency, and trust in mind.
        </div>
    </div>

</footer>


  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="{{ asset('website-asset/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('website-asset/assets/vendor/aos/aos.js') }}"></script>
<script src="{{ asset('website-asset/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
<script src="{{ asset('website-asset/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('website-asset/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('website-asset/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
<script src="{{ asset('website-asset/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

<!-- Main JS File -->
<script src="{{ asset('website-asset/assets/js/main.js') }}"></script>


</body>

</html>