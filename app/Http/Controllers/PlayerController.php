<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Notifications\WelcomeToTeamNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlayerController extends Controller
{
    public function store(Request $request, Team $team)
    {
        if ($team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

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
            'position' => ['nullable', Rule::in(Player::POSITIONS)],
        ]);

        [$guardianUser, $player] = DB::transaction(function () use ($validated, $team) {
            $guardianUser = User::where('email', $validated['guardian_email'])->first();

            if ($guardianUser) {
                $validated['guardian_id'] = $guardianUser->id;
            } else {
                $guardianUser = User::create([
                    'name' => $validated['guardian_name'],
                    'email' => $validated['guardian_email'],
                    'password' => Hash::make(Str::random(16)), // Generate random temp password
                    'role' => 'guardian',
                ]);

                $validated['guardian_id'] = $guardianUser->id;
            }

            $player = $team->players()->create($validated);

            // Add new player to already scheduled future events
            $futureEventIds = $team->events()->where('starts_at', '>', now())->pluck('id');
            if ($futureEventIds->isNotEmpty()) {
                $player->events()->attach($futureEventIds);
            }

            return [$guardianUser, $player];
        });

        $guardianUser->notify(new WelcomeToTeamNotification($team, $player));

        return redirect()->route('teams.show', $team);
    }

    public function update(Request $request, Player $player)
    {
        $isTeamCoach = $player->team->user_id === auth()->id();
        $isPlayerGuardian = auth()->user()->isGuardianOf($player);

        if (! $isTeamCoach && ! $isPlayerGuardian) {
            abort(403, 'Unauthorized action.');
        }

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
            'position' => ['nullable', Rule::in(Player::POSITIONS)],
        ]);

        $player->update($validated);

        return redirect()->route('teams.show', $player->team);
    }

    public function destroy(Player $player)
    {
        $isTeamCoach = $player->team->user_id === auth()->id();
        $isPlayerGuardian = auth()->user()->isGuardianOf($player);

        if (! $isTeamCoach && ! $isPlayerGuardian) {
            abort(403, 'Unauthorized action.');
        }

        $player->delete();

        return redirect()->route('teams.show', $player->team);
    }
}
