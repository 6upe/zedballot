@extends('layouts.app')

@section('title', 'Poll Results')

@section('content')
<div class="section-header">
  <h1>Poll Results</h1>
</div>
<div class="section-body">
  <div class="card">
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Poll</th>
            <th>Total Votes</th>
            <th>Winner(s)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($polls as $poll)
          <tr>
            <td>{{ $poll->title }}</td>
            <td>{{ $poll->votes_count ?? 0 }}</td>
            <td>
              @if($poll->winners && count($poll->winners))
                @foreach($poll->winners as $winner)
                  <span class="badge badge-success">{{ $winner->name }}</span>
                @endforeach
              @else
                <span class="text-muted">No winner yet</span>
              @endif
            </td>
            <td>
              <a href="{{ route('results.show', $poll->id) }}" class="btn btn-info btn-sm" target="_blank">View Details</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center">No polls found.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
