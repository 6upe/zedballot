@extends('layouts.app')

@section('title', 'Create Poll')

@section('content')
  <div class="section-header">
    <h1>Create Poll</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <h5>Create a new poll</h5>
        <p class="text-muted">Poll creation form will go here.</p>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/page/index.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
@endpush

