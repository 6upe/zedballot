@extends('layouts.app')

@section('title', 'Polls')

@section('content')
  <div class="section-header">
    <h1>Active Polls</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <h5>Welcome, {{ auth()->user()->name ?? 'User' }}!</h5>
        <p class="text-muted">List of active polls will be displayed here.</p>
      </div>
    </div>
  </div>
@endsection



