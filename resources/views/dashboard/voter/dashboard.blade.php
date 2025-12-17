@extends('layouts.app')

@section('title', 'Voter Dashboard')

@section('content')
	<div class="section-header">
		<h1>Voter Dashboard</h1>
	</div>

	<div class="section-body">
		<div class="card">
			<div class="card-body">
				<h5>Welcome, {{ auth()->user()->name ?? 'Voter' }}!</h5>
				<p class="text-muted">Access voting pages and see your information here.</p>
			</div>
		</div>
	</div>
@endsection
