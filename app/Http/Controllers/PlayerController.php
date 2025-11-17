<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Notifications\WelcomeToTeamNotification;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    public function store(Request $request, Team $team, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_email' => 'required|email',
            'guardian_phone' => 'required|string|max:255',
            'squad_number' => [
                'integer',
                'min:1',
                'max:99',
                Rule::unique('players')->where(function ($query) use ($team) {
                    return $query->where('team_id', $team->id);
                }),
            ],
            'position' => 'string|max:255',
        ]);

        $guardianUser = User::where('email', $validated['guardian_email'])->first();

        if ($guardianUser) {
            $validated['guardian_id'] = $guardianUser->id;
        } else {
            $guardianUser =  User::create([
                'name' => $validated['guardian_name'],
                'email' => $validated['guardian_email'],
                'password' => Hash::make(Str::random(16)), // Generate random temp password
                'role' => 'guardian',
            ]);
            
            $validated['guardian_id'] = $guardianUser->id;
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

    public function update(Request $request, Player $player)
    {
             $validated = $request->validate([
                'name' => 'required|string|max:255',
                'guardian_name' => 'required|string|max:255',
                'guardian_email' => 'required|email',
                'guardian_phone' => 'required|string|max:255',
                'squad_number' => [
                    'integer',
                    'min:1',
                    'max:99',
                    Rule::unique('players')->where(function ($query) use ($player) {
                    return $query->where('team_id', $player->team->id);
                })->ignore($player->id),
            ],
            'position' => 'string|max:255',
        ]);

        $player->update($validated);

        return redirect()->route('team.show', $player->team);
    }
}