<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    $users_teams = auth()->user()->teams;
    $user = auth()->user();
    return Inertia::render('Dashboard',['user' => $user,'teams' => $users_teams]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/teams/create', function () {
    return Inertia::render('Teams/Create');
})->middleware(['auth', 'verified'])->name('teams.create');

Route::get('/teams/{team}',[TeamController::class,'show'])->middleware(['auth','verified'])->name('teams.show');

Route::get('/events/{event}',[EventController::class,'show'])->middleware(['auth','verified'])->name('event.show');

Route::post('/teams', [TeamController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('teams.store');

Route::post('/teams/{team}/players', [PlayerController::class,'store'])->middleware(['auth','verified'])->name('players.store');

Route::post('/teams/{team}/events',
[EventController::class,'store'])->middleware(['auth','verified'])->name('events.store');

Route::post('/events/{event}/players/{player}',[EventController::class,'update'])->middleware(['auth','verified'])->name('events.update');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';