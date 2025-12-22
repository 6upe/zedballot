<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
  <title>Login — ZedBallot</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">

  <!-- Favicon and App Icon -->
  <link rel="icon" type="image/png" href="{{ asset('assets/img/logo-icon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-icon.png') }}">

  <!-- Open Graph / Facebook Meta Tags -->
  <meta property="og:title" content="Login — ZedBallot">
  <meta property="og:description" content="Login to your ZedBallot account.">
  <meta property="og:image" content="{{ asset('assets/img/logo-word.png') }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">

  <!-- WhatsApp Meta Tags (uses Open Graph) -->
  <meta property="og:site_name" content="ZedBallot">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  @stack('styles')
</head>
<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <img src="{{ asset('assets/img/logo-word.png') }}" alt="logo" width="200">
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Login</h4></div>

              <div class="card-body">
                @if(session('success'))
                  <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ url('/login') }}" class="needs-validation" novalidate>
                  @csrf

                  <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" tabindex="1" required autofocus>
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>
                      <div class="float-right">
                        <a href="{{ url('/forgot-password') }}" class="text-small">Forgot Password?</a>
                      </div>
                    </div>
                    <div class="input-group">
                      <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" tabindex="2" required>
                      <div class="input-group-append">
                        <button class="btn btn-light toggle-password" type="button" data-target="#password" aria-label="Toggle password visibility">
                          <i class="fas fa-eye"></i>
                        </button>
                      </div>
                    </div>
                    @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Login</button>
                  </div>
                </form>

              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              Don't have an account? <a href="{{ url('/register') }}">Create One</a>
            </div>
            <div class="simple-footer">
              Copyright &copy; {{ date('Y') }}
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/modules/popper.js') }}"></script>
  <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>

  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>
  <script>
    (function($){
      $(document).on('click', '.toggle-password', function(){
        var target = $($(this).data('target'));
        if (!target || target.length === 0) return;
        var type = target.attr('type') === 'password' ? 'text' : 'password';
        target.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
      });
    })(jQuery);
  </script>
</body>
</html>
