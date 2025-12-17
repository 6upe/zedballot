@extends('layouts.app')

@section('title', 'Polls')

@section('content')
  <div class="section-header">
    <h1>Polls</h1>
  </div>
  <div class="section-body">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
      <p class="mb-0 text-muted">Manage all your polls from one place.</p>
      <a href="{{ route('polls.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-1"></i> New Poll
      </a>
    </div>

    @if($polls->isEmpty())
      <div class="card">
        <div class="card-body text-center text-muted">
          <i class="fas fa-poll fa-2x mb-3"></i>
          <p class="mb-1">You have not created any polls yet.</p>
          <a href="{{ route('polls.create') }}" class="btn btn-sm btn-outline-primary mt-2">
            Create your first poll
          </a>
        </div>
      </div>
    @else
      <div class="row">
        @foreach($polls as $poll)
            @php
            $statusClass = match($poll->computed_status) {
              'active' => 'badge-success',
              'closed' => 'badge-danger',
              'scheduled' => 'badge-info',
              default => 'badge-secondary',
            };
            $coverUrl = $poll->cover_image
              ? asset('storage/'.$poll->cover_image)
              : asset('assets/img/news/img01.jpg');
          @endphp
          <div class="col-12 col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm poll-card h-100 border-0">
              <div class="card-img-top poll-card-cover"
                   style="background-image: url('{{ $coverUrl }}');">
                <span class="badge {{ $statusClass }} poll-status-badge text-uppercase">
                  {{ $poll->computed_status }}
                </span>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title mb-1">{{ $poll->name }}</h5>
                <p class="card-text text-muted small mb-2">
                  {{ Str::limit($poll->description, 120) ?: 'No description provided.' }}
                </p>

                <div class="mb-2 small text-muted">
                  <div>
                    <i class="far fa-calendar mr-1"></i>
                    {{ $poll->start_at->setTimezone('Africa/Lusaka')->format('M d, Y H:i') }}
                    &mdash;
                    {{ $poll->end_at->setTimezone('Africa/Lusaka')->format('M d, Y H:i') }}
                  </div>
                </div>

                <div class="mb-3">
                  @if($poll->computed_status === 'draft')
                    <span class="text-warning small font-weight-semibold">Not yet published</span>
                  @elseif($poll->computed_status === 'scheduled')
                    <span class="text-info small font-weight-semibold">Scheduled to start</span>
                  @elseif($poll->computed_status === 'closed')
                    <span class="text-danger small font-weight-semibold">Poll Closed</span>
                  @else
                    <span class="small text-muted d-block">Time remaining</span>
                    <div class="font-weight-bold" data-countdown
                         data-status="{{ $poll->computed_status }}"
                         data-end="{{ $poll->end_at->toIso8601String() }}"
                         data-server-now="{{ now()->toIso8601String() }}">
                      --:--:--:--
                    </div>
                  @endif
                </div>

                <div class="mt-auto d-flex flex-wrap justify-content-between align-items-center">
                  <div class="btn-group mb-2" role="group">
                    <a href="{{ route('polls.edit', $poll) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('polls.show', $poll) }}" class="btn btn-sm btn-outline-info" title="View">
                      <i class="fas fa-eye"></i>
                    </a>
                    @if($poll->status === 'draft')
                    <a href="{{ route('polls.preview', $poll) }}" class="btn btn-sm btn-outline-warning" title="Preview" target="_blank">
                      <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                    <form action="{{ route('polls.destroy', $poll) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this poll?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                  <div class="d-flex flex-column">
                    @if($poll->status === 'draft')
                    <button type="button"
                            class="btn btn-sm btn-outline-info mb-1 poll-share-btn"
                            data-share-url="{{ route('polls.preview', $poll) }}"
                            title="Copy preview link">
                      <i class="fas fa-eye mr-1"></i> Preview link
                    </button>
                    @endif
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary mb-2 poll-share-btn"
                            data-share-url="{{ route('polls.vote', $poll) }}"
                            @if($poll->computed_status !== 'active') disabled @endif
                            title="Copy voting link">
                      <i class="fas fa-share-alt mr-1"></i> Vote link
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
@endsection

@push('styles')
<style>
  .poll-card-cover {
    position: relative;
    background-size: cover;
    background-position: center;
    padding-top: 56.25%; /* 16:9 ratio */
    border-top-left-radius: .25rem;
    border-top-right-radius: .25rem;
  }
  .poll-status-badge {
    position: absolute;
    top: .75rem;
    left: .75rem;
    padding: .3rem .6rem;
    font-size: .7rem;
    letter-spacing: .05em;
  }
  .poll-card {
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .poll-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.12);
  }
</style>
@endpush

@push('scripts')
<script>
  (function () {
    function updateCountdown(el) {
      var status = el.getAttribute('data-status');
      var end = el.getAttribute('data-end');
      var serverNow = el.getAttribute('data-server-now');
      
      if (!end || !serverNow) return;

      // Parse server time and end time
      var serverTime = new Date(serverNow);
      var endTime = new Date(end);
      
      // Calculate difference from SERVER time to end time (not browser time)
      // On first load, use server time. Then increment by elapsed wall-clock time.
      if (!el._serverStartMs) {
        el._serverStartMs = serverTime.getTime();
        el._wallClockStartMs = Date.now();
      }
      
      var elapsedMs = Date.now() - el._wallClockStartMs;
      var currentServerTimeMs = el._serverStartMs + elapsedMs;
      var diff = endTime.getTime() - currentServerTimeMs;

      if (status === 'draft') {
        el.textContent = 'Not yet published';
        return;
      }

      if (diff <= 0) {
        el.textContent = '00d : 00h : 00m : 00s';
        var card = el.closest('.poll-card');
        if (card) {
          var badge = card.querySelector('.poll-status-badge');
          if (badge) {
            badge.textContent = 'closed';
            badge.classList.remove('badge-success', 'badge-secondary');
            badge.classList.add('badge-danger');
          }
          var shareBtn = card.querySelector('.poll-share-btn');
          if (shareBtn) {
            shareBtn.setAttribute('disabled', 'disabled');
          }
        }
        return;
      }

      var seconds = Math.floor(diff / 1000);
      var days = Math.floor(seconds / (3600 * 24));
      seconds -= days * 3600 * 24;
      var hours = Math.floor(seconds / 3600);
      seconds -= hours * 3600;
      var minutes = Math.floor(seconds / 60);
      seconds -= minutes * 60;

      function pad(n) { return n < 10 ? '0' + n : n; }

      el.textContent = days + 'd : ' + pad(hours) + 'h : ' + pad(minutes) + 'm : ' + pad(seconds) + 's';
    }

    document.addEventListener('DOMContentLoaded', function () {
      var countdownEls = document.querySelectorAll('[data-countdown]');
      countdownEls.forEach(function (el) { updateCountdown(el); });

      if (countdownEls.length) {
        setInterval(function () {
          countdownEls.forEach(function (el) { updateCountdown(el); });
        }, 1000);
      }

      // Share link button
      document.querySelectorAll('.poll-share-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var url = btn.getAttribute('data-share-url');
          if (!url) return;

          if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function () {
              alert('Share link copied to clipboard.');
            }).catch(function () {
              alert('Unable to copy link. URL: ' + url);
            });
          } else {
            // Fallback
            var tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert('Share link copied to clipboard.');
          }
        });
      });
    });
  })();
</script>
@endpush
