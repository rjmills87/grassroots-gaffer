<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Message;
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

    public function update(Request $request, Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $message->update($validated);
        $team = $message->team;

        return redirect()->route('teams.show', $team);
    }

    public function destroy(Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $team = $message->team;
        $message->delete();

        return redirect()->route('teams.show', $team);
    }
}