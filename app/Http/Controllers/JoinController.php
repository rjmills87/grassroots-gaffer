<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamJoinRequest;
use App\Models\Team;
use App\Notifications\JoinRequestReceivedNotification;
use App\Support\InviteCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class JoinController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Join', [
            'valid' => null,
            'reason' => null,
            'code' => null,
            'team' => null,
            'players' => [],
        ]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        $code = InviteCode::normalize($request->validate([
            'code' => ['required', 'string', 'max:12'],
        ])['code']);

        return redirect()->route('join.show', ['code' => $code]);
    }

    public function show(string $code): Response|SymfonyResponse
    {
        return $this->renderJoinPage($code);
    }

    public function store(StoreTeamJoinRequest $request, string $code): RedirectResponse|SymfonyResponse
    {
        $resolved = Team::resolveInvite($code);

        if (! $resolved['valid'] || ! $resolved['team'] instanceof Team) {
            return $this->renderJoinPage($code);
        }

        $team = $resolved['team'];
        $validated = $request->validated();
        $isExistingChild = $validated['child_mode'] === 'existing';

        $joinRequest = $team->joinRequests()->create([
            'player_id' => $isExistingChild ? $validated['player_id'] : null,
            'requested_player_name' => $isExistingChild ? null : $validated['requested_player_name'],
            'guardian_name' => $validated['guardian_name'],
            'guardian_email' => $validated['guardian_email'],
            'guardian_phone' => $validated['guardian_phone'],
        ]);

        $joinRequest->load(['player', 'team.user']);
        $team->loadMissing('user');
        $team->user->notify(new JoinRequestReceivedNotification($joinRequest));

        return redirect()->route('join.submitted')->with('join', [
            'team_name' => $team->name,
            'child_name' => $joinRequest->childName(),
            'coach_first_name' => str($team->user->name)->before(' ')->toString(),
        ]);
    }

    public function submitted(): Response
    {
        return Inertia::render('JoinSubmitted');
    }

    protected function renderJoinPage(string $code): Response|SymfonyResponse
    {
        $resolved = Team::resolveInvite($code);
        $team = $resolved['team'];
        $team?->loadMissing('user');

        $page = Inertia::render('Join', [
            'valid' => $resolved['valid'],
            'reason' => $resolved['reason'],
            'code' => InviteCode::normalize($code),
            'team' => $team && $resolved['valid'] ? [
                'name' => $team->name,
                'age_group' => $team->age_group,
                'coach_first_name' => str($team->user->name)->before(' ')->toString(),
            ] : ($team ? [
                'name' => $team->name,
                'age_group' => $team->age_group,
                'coach_first_name' => null,
            ] : null),
            'players' => $team && $resolved['valid']
                ? $team->players()
                    ->orderBy('name')
                    ->get(['id', 'name', 'squad_number'])
                    ->map(fn ($player) => [
                        'id' => $player->id,
                        'name' => $player->name,
                        'squad_number' => $player->squad_number,
                    ])
                    ->values()
                    ->all()
                : [],
        ]);

        if ($resolved['valid']) {
            return $page;
        }

        return $page->toResponse(request())->setStatusCode(404);
    }
}
