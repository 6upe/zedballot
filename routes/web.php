<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;



Route::get('/', function () {
    return view('welcome');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Password reset routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Polls
    Route::get('/polls', fn () => view('dashboard.sidebar_items.polls.index'))->name('polls.index');
    Route::get('/polls/create', fn () => view('dashboard.sidebar_items.polls.create'))->name('polls.create');

    // Voters
    Route::get('/voters', fn () => view('dashboard.sidebar_items.voters.index'))->name('voters.index');

    // Results, reports, settings
    Route::get('/results', fn () => view('dashboard.sidebar_items.results.index'))->name('results.index');
    Route::get('/reports', fn () => view('dashboard.sidebar_items.reports.index'))->name('reports.index');
    Route::get('/settings', fn () => view('dashboard.sidebar_items.settings.index'))->name('settings.index');
    
    // Profile
    Route::get('/profile', fn () => view('dashboard.sidebar_items.profile.show'))->name('profile.show');
    
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin', fn () => view('dashboard.admin.dashboard'));
});

Route::middleware(['auth', 'role:election_admin'])->group(function () {
    Route::get('/elections/manage', fn () => view('dashboard.elections.dashboard'));
});

Route::middleware(['auth', 'role:voter'])->group(function () {
    Route::get('/vote', fn () => view('dashboard.voter.dashboard'));
});

Route::middleware(['auth', 'role:observer'])->group(function () {
    Route::get('/observer/results', fn () => view('dashboard.results.dashboard'));
});



