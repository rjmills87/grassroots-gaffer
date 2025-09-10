<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team-name' => 'required|string|max:255',
            'age-group' => 'required|string',
        ]);
        
        $team = new Team([
            'name' => $validated['team-name'],
            'age_group' => $validated['age-group'],
        ]);
        
        $request->user()->teams()->save($team);

        return redirect()->route('dashboard');
    }

    public function show(Team $team)
    {
        return Inertia::render('Teams/Show', [
            // Load the team with its players and events.
            // For each event, also count the number of players who are 'attending' or 'unavailable'.
            'team' => $team->load(['players', 'events' => function($query) {
                $query->withCount([
                    'players as attending_count' => function ($query) {
                        $query->where('player_response', 'attending');
                    },
                    'players as unavailable_count' => function($query) {
                        $query->where('player_response', 'unavailable');
                    }
                ]);
            }]),
        ]);
    }
}