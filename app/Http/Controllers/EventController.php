<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function store(Request $request, Team $team)
    {
        if (! $this->userIsTeamCoach(auth()->user(), $team)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'occurs_at' => 'required|date',
            'location' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        $event = $team->events()->create($validated);

        $playerIds = $team->players()->pluck('id');

        $event->players()->attach($playerIds);

        return redirect()->route('events.index', ['team' => $team->id]);
    }

    public function show(Event $event)
    {
        $user = auth()->user();

        if (! $this->userCanAccessEvent($user, $event)) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Event/Show', [
            'event' => $event->load('players'),
            'user' => $user,
        ]);
    }

    public function update(Request $request, Event $event, Player $player)
    {
        if ($player->team_id !== $event->team_id || ! $event->players()->where('players.id', $player->id)->exists()) {
            abort(404);
        }

        $user = auth()->user();
        $isTeamCoach = $this->userIsTeamCoach($user, $event->team);
        $isPlayerGuardian = $player->guardian_id === $user->id;

        if (! $isTeamCoach && ! $isPlayerGuardian) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'player_response' => 'required|string|in:attending,unavailable',
        ]);

        $event->players()->updateExistingPivot($player->id, $validated);

        return redirect()->route('event.show', $event);
    }

    public function sendReminders(Event $event)
    {
        if (! $this->userIsTeamCoach(auth()->user(), $event->team)) {
            abort(403, 'Unauthorized action.');
        }

        $playersWithoutResponse = $event->players()->wherePivotNull('player_response')->get();

        foreach ($playersWithoutResponse as $player) {
            $guardian = $player->guardian;

            if (! $guardian) {
                continue;
            }

            $guardian->notify(new EventReminderNotification($event));
        }

        return redirect()->route('event.show', $event);
    }

    protected function userIsTeamCoach(User $user, Team $team): bool
    {
        return $user->role === 'coach' && $team->user_id === $user->id;
    }

    protected function userCanAccessEvent(User $user, Event $event): bool
    {
        if ($this->userIsTeamCoach($user, $event->team)) {
            return true;
        }

        if ($user->role === 'guardian') {
            return $user->players()->where('team_id', $event->team_id)->exists();
        }

        return false;
    }
}
