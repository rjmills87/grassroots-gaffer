<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;

class PlayerController extends Controller
{
    public function store(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email',
            'guardian_phone' => 'nullable|string|max:255',
        ]);

        $team->players()->create($validated);

        return redirect()->route('teams.show',$team);
    }
}