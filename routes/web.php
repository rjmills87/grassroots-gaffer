<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Page Routes
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/features', function () {
    return Inertia::render('Features');
})->name('features');

Route::get('/pricing', function () {
    return Inertia::render('Pricing');
})->name('pricing');

Route::get('/faq', function () {
    return Inertia::render('FAQ');
})->name('faq');

// Dashboard Route
Route::get('dashboard', function () {
    $user = auth()->user();
    $teams = accessibleTeams($user, [
        'events' => function ($query) {
            $query->where('starts_at', '>=', now())
                ->orderBy('starts_at', 'asc')
                ->limit(4);
        },
        'messages' => function ($query) {
            $query->with(['user', 'attachments'])
                ->latest()
                ->limit(2);
        },
        'players',
    ]);
    $selectedTeam = selectedTeamFromRequest(request('team'), $teams);
    $selectedTeamId = $selectedTeam['id'] ?? null;

    return Inertia::render('Dashboard', [
        'user' => $user,
        'teams' => $teams->map(function ($team) {
            return array_merge($team->toArray(), [
                'events' => $team->events ?? [],
                'messages' => $team->messages ?? [],
                'players' => $team->players ?? [],
            ]);
        })->values(),
        'selectedTeam' => $selectedTeam,
        'selectedTeamId' => $selectedTeamId,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/squad', function () {
    $user = auth()->user();
    $teams = accessibleTeams($user, ['players']);
    $selectedTeam = selectedTeamFromRequest(request('team'), $teams);

    return Inertia::render('Squad', [
        'user' => $user,
        'teams' => $teams->values(),
        'selectedTeam' => $selectedTeam,
    ]);
})->middleware(['auth', 'verified'])->name('squad.index');

Route::get('/events', function () {
    $user = auth()->user();
    $teams = accessibleTeams($user, [
        'events' => function ($query) {
            $query->withCount([
                'players as attending_count' => function ($query) {
                    $query->where('player_response', 'attending');
                },
                'players as unavailable_count' => function ($query) {
                    $query->where('player_response', 'unavailable');
                },
            ])->orderBy('starts_at', 'asc');
        },
    ]);
    $selectedTeam = selectedTeamFromRequest(request('team'), $teams);

    return Inertia::render('Events', [
        'user' => $user,
        'teams' => $teams->values(),
        'selectedTeam' => $selectedTeam,
    ]);
})->middleware(['auth', 'verified'])->name('events.index');

Route::get('/announcements', function () {
    $user = auth()->user();
    $teams = accessibleTeams($user, [
        'messages' => function ($query) {
            $query->with(['user', 'attachments'])->latest();
        },
    ]);
    $selectedTeam = selectedTeamFromRequest(request('team'), $teams);

    return Inertia::render('Announcements', [
        'user' => $user,
        'teams' => $teams->values(),
        'selectedTeam' => $selectedTeam,
    ]);
})->middleware(['auth', 'verified'])->name('announcements.index');

// Team Routes
Route::get('/teams/{team}', [TeamController::class, 'show'])->middleware(['auth', 'verified'])->name('teams.show');
Route::post('/teams', [TeamController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('teams.store');
Route::delete('/teams/{team}', [TeamController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('teams.destroy');

// Player Routes
Route::post('/teams/{team}/players', [PlayerController::class, 'store'])->middleware(['auth', 'verified'])->name('players.store');
Route::patch('/players/{player}', [PlayerController::class, 'update'])->middleware(['auth', 'verified'])->name('players.update');
Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->middleware(['auth', 'verified'])->name('players.destroy');

// Event Routes
Route::get('/events/{event}', [EventController::class, 'show'])->middleware(['auth', 'verified'])->name('event.show');
Route::post('/teams/{team}/events', [EventController::class, 'store'])->middleware(['auth', 'verified'])->name('events.store');
Route::patch('/events/{event}', [EventController::class, 'update'])->middleware(['auth', 'verified'])->name('events.manage.update');
Route::delete('/events/{event}', [EventController::class, 'destroy'])->middleware(['auth', 'verified'])->name('events.destroy');
Route::post('/events/{event}/players/{player}', [EventController::class, 'updatePlayerResponse'])->middleware(['auth', 'verified'])->name('events.players.update');
Route::post('/events/{event}/send-reminders', [EventController::class, 'sendReminders'])->middleware(['auth', 'verified', 'throttle:5,1'])->name('events.sendReminders');
Route::get('/event-attachments/{eventAttachment}/preview', [EventController::class, 'previewAttachment'])
    ->middleware(['auth', 'verified'])
    ->name('events.attachments.preview');
Route::get('/event-attachments/{eventAttachment}/download', [EventController::class, 'downloadAttachment'])
    ->middleware(['auth', 'verified'])
    ->name('events.attachments.download');

// Message Routes
Route::post('/teams/{team}/messages', [MessageController::class, 'store'])->middleware(['auth', 'verified'])->name('teams.messages.store');
Route::match(['put', 'post'], '/messages/{message}', [MessageController::class, 'update'])->middleware(['auth', 'verified'])->name('messages.update');
Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->middleware(['auth', 'verified'])->name('messages.destroy');
Route::get('/message-attachments/{messageAttachment}/preview', [MessageController::class, 'previewAttachment'])
    ->middleware(['auth', 'verified'])
    ->name('messages.attachments.preview');
Route::get('/message-attachments/{messageAttachment}/download', [MessageController::class, 'downloadAttachment'])
    ->middleware(['auth', 'verified'])
    ->name('messages.attachments.download');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

if (! function_exists('accessibleTeams')) {
    function accessibleTeams(User $user, array $relations = []): Collection
    {
        if ($user->role === 'coach') {
            return $user->teams()->with($relations)->get();
        }

        if ($user->role === 'guardian') {
            $guardianRelations = [];

            foreach ($relations as $key => $relation) {
                if (is_string($key)) {
                    $guardianRelations["team.{$key}"] = $relation;
                } else {
                    $guardianRelations[] = "team.{$relation}";
                }
            }

            return $user->players()->with($guardianRelations)
                ->get()
                ->map(function ($player) {
                    return $player->team;
                })
                ->filter()
                ->unique('id')
                ->values();
        }

        return collect();
    }
}

if (! function_exists('selectedTeamFromRequest')) {
    function selectedTeamFromRequest(?string $teamId, Collection $teams): ?array
    {
        if ($teams->isEmpty()) {
            return null;
        }

        $selectedTeam = $teamId
            ? $teams->firstWhere('id', (int) $teamId)
            : $teams->first();

        if (! $selectedTeam) {
            $selectedTeam = $teams->first();
        }

        return $selectedTeam?->toArray();
    }
}
