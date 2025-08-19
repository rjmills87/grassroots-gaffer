<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeamController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    $users_teams = auth()->user()->teams;
    return Inertia::render('Dashboard',['teams' => $users_teams]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/teams/create', function () {
    return Inertia::render('Teams/Create');
})->middleware(['auth', 'verified'])->name('teams.create');

Route::post('/teams', [TeamController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('teams.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';