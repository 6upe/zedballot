<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'ZedBallot')</title>

    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400&family=Sono:wght@200;300;400;500;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('website-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website-assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('website-assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website-assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('website-assets/css/templatemo-pod-talk.css') }}">

    <!-- Favicon and App Icon -->
  <link rel="icon" type="image/png" href="{{ asset('assets/img/logo-icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-icon.png') }}">

  <!-- Open Graph / Facebook Meta Tags -->
  <meta property="og:title" content="ZedBallot - Official Website">
  <meta property="og:description" content="ZedBallot - Your trusted platform for secure and transparent voting.">
  <meta property="og:image" content="{{ asset('assets/img/logo-word.png') }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">

  <!-- WhatsApp Meta Tags (uses Open Graph) -->
  <meta property="og:site_name" content="ZedBallot">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

    <!--

TemplateMo 584 Pod Talk

https://templatemo.com/tm-584-pod-talk

-->
</head>

<body>

    @yield('content')

    <!-- JAVASCRIPT FILES -->
    <script src="{{ asset('website-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('website-assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('website-assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('website-assets/js/custom.js') }}"></script>

</body>

</html>