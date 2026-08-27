<?php

namespace App\Http\Controllers;

use App\Models\TeamJoinRequest;
use Illuminate\Http\RedirectResponse;

class TeamJoinRequestController extends Controller
{
    public function approve(TeamJoinRequest $teamJoinRequest): RedirectResponse
    {
        $this->ensureCoachOwnsRequest($teamJoinRequest);

        $teamJoinRequest->approve();

        return redirect()->route('squad.index', ['team' => $teamJoinRequest->team_id]);
    }

    public function reject(TeamJoinRequest $teamJoinRequest): RedirectResponse
    {
        $this->ensureCoachOwnsRequest($teamJoinRequest);

        $teamJoinRequest->reject();

        return redirect()->route('squad.index', ['team' => $teamJoinRequest->team_id]);
    }

    protected function ensureCoachOwnsRequest(TeamJoinRequest $teamJoinRequest): void
    {
        $teamJoinRequest->loadMissing('team');

        if (! auth()->user()->isCoachOf($teamJoinRequest->team)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
