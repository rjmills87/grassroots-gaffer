<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;

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
            $team->players()->create($validated);
        } else {
            $newGuardianUser =  User::create([
                'name' => $validated['guardian_name'],
                'email' => $validated['guardian_email'],
                'password' => Hash::make(Str::random(16)), // Generate random temp password
                'role' => 'guardian',
            ]);
            
            $validated['user_id'] = $newGuardianUser->id;
            $team->players()->create($validated);
            event(new Registered($newGuardianUser));
        }


        return redirect()->route('teams.show',$team);
    }
}