@extends('layouts.app')

@section('title', 'Elections Dashboard')

@section('content')
	<div class="section-header">
		<h1>Elections Dashboard</h1>
	</div>

	<div class="section-body">
		<div class="card">
			<div class="card-body">
				<h5>Welcome, {{ auth()->user()->name ?? 'User' }}!</h5>
				<p class="text-muted">Manage elections and view related tools from here.</p>
			</div>
		</div>
	</div>
@endsection
