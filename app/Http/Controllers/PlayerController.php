<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Notifications\WelcomeToTeamNotification;

class PlayerController extends Controller
{
    public function store(Request $request, Team $team, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_email' => 'required|email',
            'guardian_phone' => 'required|string|max:255',
        ]);

        $guardianUser = User::where('email', $validated['guardian_email'])->first();

        if ($guardianUser) {
            $validated['user_id'] = $guardianUser->id;
        } else {
            $guardianUser =  User::create([
                'name' => $validated['guardian_name'],
                'email' => $validated['guardian_email'],
                'password' => Hash::make(Str::random(16)), // Generate random temp password
                'role' => 'guardian',
            ]);
            
            $validated['user_id'] = $guardianUser->id;
        }

        $player = $team->players()->create($validated);
        $guardianUser->notify(new WelcomeToTeamNotification($team, $player));

        // Add new Player to already scheduled future events
        $futureEvents = $team->events()->where('occurs_at', '>' ,now())->get();
        foreach($futureEvents as $event) {
            $event->players()->attach($player->id);
        };


        return redirect()->route('teams.show',$team);
    }
}