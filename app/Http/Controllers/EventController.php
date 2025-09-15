<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Team;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function store(Request $request, Team $team)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'occurs_at' => 'required|date',
            'location' => 'required|string|max:255',
            'details' => 'required|string|max:255',            
        ]);

        $event = $team->events()->create($validated);

        $playerIds = $team->players()->pluck('id');

        $event->players()->attach($playerIds);

        return redirect()->route('teams.show',$team);
    }

    public function show(Event $event)
    {
        return Inertia::render('Event/Show',['event' => $event->load('players'), 'user' => auth()->user()]);
    }

    public function update(Request $request, Event $event, Player $player)
    {
        $validated = $request->validate([
            'player_response' => 'required|string|in:attending,unavailable',
        ]);

        $event->players()->updateExistingPivot($player->id, $validated);

        return redirect()->route('event.show',$event);
    }
}