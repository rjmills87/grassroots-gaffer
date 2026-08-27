<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;

class TeamInviteController extends Controller
{
    public function rotate(Team $team): RedirectResponse
    {
        if (! auth()->user()->isCoachOf($team)) {
            abort(403, 'Unauthorized action.');
        }

        $team->regenerateInviteCode();

        return redirect()->route('squad.index', ['team' => $team->id]);
    }
}
