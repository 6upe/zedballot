@extends('layouts.app')

@section('title', 'Profile')

@section('content')
  <div class="section-header">
    <h1>Profile</h1>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <h5>{{ auth()->user()->name }}</h5>
        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        <p><strong>Role:</strong> {{ auth()->user()->role->name ?? 'N/A' }}</p>
        <p><strong>Member since:</strong> {{ auth()->user()->created_at->format('M d, Y H:i') }}</p>
        <p><strong>Last login:</strong>
          @if(session('login_at'))
            {{ \Carbon\Carbon::parse(session('login_at'))->toDayDateTimeString() }} ({{ \Carbon\Carbon::parse(session('login_at'))->diffForHumans() }})
          @else
            Unknown
          @endif
        </p>
      </div>
    </div>
  </div>
@endsection
