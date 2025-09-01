<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Team;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request, Team $team)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'occurs_at' => 'required|date',
            'location' => 'required|string|max:255',
            'details' => 'nullable|string|max:500',            
        ]);

        $team->event()->create($validated);

        return redirect()->route('teams.show', $team);
    }
}