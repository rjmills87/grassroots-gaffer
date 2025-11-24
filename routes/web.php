<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MessageController;

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

Route::get('/faq', function (){
    return Inertia::render('FAQ');
})->name('faq');


// Dashboard Route
Route::get('dashboard', function () {
    $user = auth()->user();
    $teams = [];

    if ($user->role === 'coach') {
        $teams = $user->teams()->with([
            'events' => function($query) {
                $query->where('occurs_at', '>=', now())
                      ->orderBy('occurs_at', 'asc')
                      ->limit(2);
            },
            'messages' => function($query) {
                $query->with('user')
                      ->latest()
                      ->limit(2);
            },
            'players' // Preload players count
        ])->get();
    } elseif ($user->role === 'guardian') {
        $players = $user->players()->with([
            'team.events' => function($query) {
                $query->where('occurs_at', '>=', now())
                      ->orderBy('occurs_at', 'asc')
                      ->limit(2);
            },
            'team.messages' => function($query) {
                $query->with('user')
                      ->latest()
                      ->limit(2);
            },
            'team.players' // Preload players count
        ])->get();
        
        $teams = $players->map(function ($player) {
            return $player->team;
        })->unique();
    }

    return Inertia::render('Dashboard', [
        'user' => $user,
        'teams' => $teams->map(function($team) {
            return array_merge($team->toArray(), [
                'events' => $team->events ?? [],
                'messages' => $team->messages ?? [],
                'players' => $team->players ?? []
            ]);
        })
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Team Routes
Route::get('/teams/{team}',[TeamController::class,'show'])->middleware(['auth','verified'])->name('teams.show');
Route::post('/teams', [TeamController::class, 'store'])
->middleware(['auth', 'verified'])
->name('teams.store');
Route::delete('/teams/{team}', [TeamController::class, 'destroy'])
->middleware(['auth', 'verified'])
->name('teams.destroy');

// Player Routes
Route::post('/teams/{team}/players', [PlayerController::class,'store'])->middleware(['auth','verified'])->name('players.store');
Route::patch('/players/{player}', [PlayerController::class, 'update'])->name('players.update');
Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

// Event Routes
Route::get('/events/{event}',[EventController::class,'show'])->middleware(['auth','verified'])->name('event.show');
Route::post('/teams/{team}/events',[EventController::class,'store'])->middleware(['auth','verified'])->name('events.store');
Route::post('/events/{event}/players/{player}',[EventController::class,'update'])->middleware(['auth','verified'])->name('events.update');
Route::post('/events/{event}/send-reminders', [EventController::class,'sendReminders'])->middleware(['auth','verified'])->name('events.sendReminders');

// Message Routes
Route::post('/teams/{team}/messages',[MessageController::class,'store'])->middleware(['auth','verified'])->name('teams.messages.store');
Route::put('/messages/{message}', [MessageController::class,'update'])->middleware(['auth','verified'])->name('messages.update');
Route::delete('/messages/{message}', [MessageController::class,'destroy'])->middleware(['auth','verified'])->name('messages.destroy');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';