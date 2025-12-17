@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
	<div class="section-header">
		<h1>Admin Dashboard</h1>
	</div>

	<div class="section-body">
		<div class="card">
			<div class="card-body">
				<h5>Welcome, {{ auth()->user()->name ?? 'User' }}!</h5>
				<p class="text-muted">This is your admin dashboard. Use the sidebar to navigate admin features.</p>
			</div>
		</div>
	</div>
@endsection
