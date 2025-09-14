<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Team $team)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        $message = $team->messages()->create($validated);

        return redirect()->route('teams.show',$team);
    }
}