@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i class="fas fa-poll"></i></div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Polls</h4>
                </div>
                <div class="card-body">{{ $totalPolls ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i class="fas fa-vote-yea"></i></div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Total Votes</h4>
                </div>
                <div class="card-body">{{ $totalVotes ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-info"><i class="fas fa-list"></i></div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Categories</h4>
                </div>
                <div class="card-body">{{ $totalCategories ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i class="fas fa-users"></i></div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>Nominees</h4>
                </div>
                <div class="card-body">{{ $totalNominees ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="d-flex justify-content-between align-items-center mb-3 col-12">
        <p class="mb-0 text-muted">Manage all your polls from one place.</p>
        <a href="{{ route('polls.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> New Poll
        </a>
    </div>
    
    <div class="col-12 row">

        <div class="col-lg-8 mb-4">

            <div class="section-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif


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
                    ? asset('public/storage/'.$poll->cover_image)
                    : asset('assets/img/news/img01.jpg');
                    @endphp
                    <div class="mb-4 mx-2">
                        <div class="card shadow-sm poll-card h-100 border-0">
                            <div class="card-img-top poll-card-cover" style="background-image: url('{{ $coverUrl }}'); max-height: 100vh;">
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
                                        <a href="{{ route('polls.edit', $poll) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>edit
                                        </a>
                                       
                                        <a href="{{ route('polls.results', $poll) }}"
                                            class="btn btn-sm btn-outline-success poll-share-link" title="Results" target="_blank" data-share-url="{{ route('polls.results', $poll) }}">
                                            <i class="fas fa-chart-bar"></i> Results
                                        </a>
                                        @if($poll->status === 'draft')
                                        <a href="{{ route('polls.preview', $poll) }}"
                                            class="btn btn-sm btn-outline-warning" title="Preview" target="_blank">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        @endif
                                        <form action="{{ route('polls.destroy', $poll) }}" method="POST"
                                            class="d-inline"
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
                                        <button type="button" class="btn btn-sm btn-outline-info mb-1 poll-share-btn"
                                            data-share-url="{{ route('polls.preview', $poll) }}"
                                            title="Copy preview link">
                                            Preview link
                                        </button>
                                        @endif
                                        <a href="{{ route('polls.vote', $poll) }}" class="btn btn-sm btn-outline-secondary mb-2 poll-share-link" target="_blank"
                                            data-share-url="{{ route('polls.vote', $poll) }}" @if($poll->computed_status !== 'active') disabled @endif
                                            title="Vote link (opens in new tab, click to copy)">
                                            <i class="fas fa-share-alt mr-1"></i> Vote link
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
        <div class="col-lg-4 mb-4">
            <div class="card row">
                <div class="card-header">
                    <h4 class="mb-0">Recent Activity</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @forelse($recentActivity ?? [] as $activity)
                        <li class="mb-3">
                            <div class="small text-muted">{{ $activity->created_at->diffForHumans() }}</div>
                            <div>{{ $activity->description }}</div>
                        </li>
                        @empty
                        <li class="text-muted">No recent activity.</li>
                        @endforelse
                    </ul>
                </div>
            </div>


        </div>
    </div>

    <div class="col-12">
       <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Recent Polls</h4>
                        <a href="{{ route('polls.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPolls ?? [] as $poll)
                                    <tr>
                                        <td>{{ $poll->name }}</td>
                                        <td><span
                                                class="badge badge-{{ $poll->computed_status === 'active' ? 'success' : ($poll->computed_status === 'closed' ? 'danger' : 'secondary') }}">{{ ucfirst($poll->computed_status) }}</span>
                                        </td>
                                        <td>{{ $poll->start_at->setTimezone('Africa/Lusaka')->format('M d, Y H:i') }}
                                        </td>
                                        <td>{{ $poll->end_at->setTimezone('Africa/Lusaka')->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('polls.show', $poll) }}"
                                                class="btn btn-sm btn-outline-info">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No recent polls.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
    </div>




</div>
@endsection

@push('styles')
<style>
.poll-card-cover {
    position: relative;
    background-size: cover;
    background-position: center;
    padding-top: 56.25%;
    /* 16:9 ratio */
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
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .12);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text);
        } else {
            var temp = document.createElement('input');
            document.body.appendChild(temp);
            temp.value = text;
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
        }
    }
    document.querySelectorAll('.poll-share-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var url = this.getAttribute('data-share-url');
            if (url) {
                copyToClipboard(url);
                this.setAttribute('title', 'Link copied!');
                setTimeout(() => this.setAttribute('title', 'Vote link (opens in new tab, click to copy)'), 2000);
            }
        });
    });
});
</script>
<script>
(function () {
  function updateCountdown(el) {
    var status = el.getAttribute('data-status');
    var end = el.getAttribute('data-end');
    var serverNow = el.getAttribute('data-server-now');
    if (!end || !serverNow) return;
    var serverTime = new Date(serverNow);
    var endTime = new Date(end);
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
  });
})();
</script>
@endpush