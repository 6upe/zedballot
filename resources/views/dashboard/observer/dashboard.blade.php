@extends('layouts.app')

@section('title', 'Observer Dashboard')

@section('content')
	<div class="section-header">
		<h1>Observer Dashboard</h1>
	</div>

	<div class="section-body">
		<div class="card">
			<div class="card-body">
				<h5>Welcome, {{ auth()->user()->name ?? 'User' }}!</h5>
				<p class="text-muted">View results and monitoring tools available to observers.</p>
			</div>
		</div>
	</div>
@endsection
