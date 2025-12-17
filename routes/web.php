<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PollController;
use App\Models\Poll;



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

    // Polls (resourceful routes for election admins)
    Route::middleware('role:election_admin')->group(function () {
        Route::resource('polls', PollController::class);
    });

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

Route::prefix('polls')->middleware(['auth'])->group(function () {
    Route::get('/', [PollController::class, 'index'])->name('polls.index');
    Route::get('/create', [PollController::class, 'create'])->name('polls.create');
    Route::post('/', [PollController::class, 'store'])->name('polls.store');

    // API: Fetch current server time (for frontend countdowns)
    Route::get('/api/server-time', function () {
        return response()->json([
            'server_time' => now()->toIso8601String(),
        ]);
    })->withoutMiddleware('auth')->name('api.server-time');

    // Progressive update endpoints for multi-step creation
    Route::put('{poll}/step1', [PollController::class, 'updateStep1'])->name('polls.update.step1');
    Route::put('{poll}/step2', [PollController::class, 'updateStep2'])->name('polls.update.step2');
    Route::put('{poll}/step3', [PollController::class, 'updateStep3'])->name('polls.update.step3');
    Route::post('{poll}/finalize', [PollController::class, 'finalize'])->name('polls.finalize');

    Route::get('{poll}', [PollController::class, 'show'])->name('polls.show');
    Route::get('{poll}/edit', [PollController::class, 'edit'])->name('polls.edit');
    Route::get('{poll}/preview', [PollController::class, 'preview'])->name('polls.preview');
    Route::get('{poll}/step2-data', [PollController::class, 'getStep2Data'])->name('polls.step2-data');
    Route::get('{poll}/step3-data', [PollController::class, 'getStep3Data'])->name('polls.step3-data');
    Route::put('{poll}', [PollController::class, 'update'])->name('polls.update');
    Route::delete('{poll}', [PollController::class, 'destroy'])->name('polls.destroy');

    // Categories
    Route::post('{poll}/categories', [PollController::class, 'storeCategory'])->name('polls.categories.store');
    Route::put('categories/{category}', [PollController::class, 'updateCategory'])->name('polls.categories.update');
    Route::delete('categories/{category}', [PollController::class, 'deleteCategory'])->name('polls.categories.destroy');

    // Nominees
    Route::post('{poll}/nominees', [PollController::class, 'storeNominee'])->name('polls.nominees.store');
    Route::post('{poll}/nominees/import', [PollController::class, 'importNominees'])->name('polls.nominees.import');
    Route::get('nominees/csv-template', [PollController::class, 'downloadNomineesCSVTemplate'])->name('polls.nominees.csv-template');
    Route::get('{poll}/nominees/registration-link', [PollController::class, 'generateNomineeRegistrationLink'])->name('polls.nominees.registration-link');
    Route::put('nominees/{nominee}', [PollController::class, 'updateNominee'])->name('polls.nominees.update');
    Route::post('nominees/{nominee}/approve', [PollController::class, 'approveNominee'])->name('polls.nominees.approve');

    // Eligible Voters (for private polls)
    Route::post('{poll}/eligible-voters', [PollController::class, 'storeEligibleVoter'])->name('polls.eligible-voters.store');
    Route::post('{poll}/eligible-voters/import', [PollController::class, 'importEligibleVoters'])->name('polls.eligible-voters.import');
    Route::get('eligible-voters/csv-template', [PollController::class, 'downloadVotersCSVTemplate'])->name('polls.eligible-voters.csv-template');
    Route::get('{poll}/eligible-voters/registration-link', [PollController::class, 'generateVoterRegistrationLink'])->name('polls.eligible-voters.registration-link');
    Route::delete('eligible-voters/{voter}', [PollController::class, 'deleteEligibleVoter'])->name('polls.eligible-voters.destroy');
    Route::delete('nominees/{nominee}', [PollController::class, 'deleteNominee'])->name('polls.nominees.destroy');

    // Voting
    Route::get('{poll}/vote', [PollController::class, 'showVote'])->name('polls.vote');
    Route::post('{poll}/vote', [PollController::class, 'submitVote'])->name('polls.vote.submit');

    // Results
    Route::get('{poll}/results', [PollController::class, 'results'])->name('polls.results');

    // Debug: Check poll status
    Route::get('{poll}/debug', function (Poll $poll) {
        $now = now();
        return response()->json([
            'poll_id' => $poll->id,
            'poll_status' => $poll->status,
            'start_at' => $poll->start_at?->toIso8601String(),
            'end_at' => $poll->end_at?->toIso8601String(),
            'current_time' => $now->toIso8601String(),
            'is_active' => $poll->isActive(),
            'is_closed' => $poll->isClosed(),
            'computed_status' => $poll->computed_status,
            'timestamps' => [
                'now' => $now->getTimestamp(),
                'start' => $poll->start_at?->getTimestamp(),
                'end' => $poll->end_at?->getTimestamp(),
            ]
        ]);
    })->name('polls.debug');
});

// Public self-registration routes (no auth required)
Route::prefix('nominees')->group(function () {
    Route::get('register/{poll}/{token}', function (Poll $poll, string $token) {
        if ($poll->nominee_registration_token !== $token) {
            abort(403, 'Invalid registration token.');
        }
        $poll->load('categories');
        return view('nominees.register', compact('poll', 'token'));
    })->name('nominees.register');
    
    Route::post('register', [PollController::class, 'submitNomineeRegistration'])->name('nominees.register.submit');
});

Route::prefix('voters')->group(function () {
    Route::get('register/{poll}/{token}', function (Poll $poll, string $token) {
        if ($poll->voter_registration_token !== $token) {
            abort(403, 'Invalid registration token.');
        }
        return view('voters.register', compact('poll', 'token'));
    })->name('voters.register');
    
    Route::post('register', [PollController::class, 'submitVoterRegistration'])->name('voters.register.submit');
});

Route::get('/debug-time', function () {
    return [
        'laravel_now' => now()->toDateTimeString(),
        'php_timezone' => date_default_timezone_get(),
        'laravel_timezone' => config('app.timezone'),
    ];
});


