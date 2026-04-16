<?php

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

test('dashboard allows selecting an owned team via team query parameter', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $selectedTeam = $coach->teams()->create([
        'name' => 'U15 Rockets',
        'age_group' => 'under-15s',
    ]);
    $coach->teams()->create([
        'name' => 'U13 Jets',
        'age_group' => 'under-13s',
    ]);

    $response = $this->actingAs($coach)
        ->get('/dashboard?team='.$selectedTeam->id);

    $response->assertOk();
    $response->assertSee($selectedTeam->name);
});

test('dashboard ignores inaccessible team query and falls back to accessible team', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);

    $ownedTeam = $coach->teams()->create([
        'name' => 'U11 Lions',
        'age_group' => 'under-11s',
    ]);

    $unownedTeam = $otherCoach->teams()->create([
        'name' => 'U10 Wolves',
        'age_group' => 'under-10s',
    ]);

    $response = $this->actingAs($coach)
        ->get('/dashboard?team='.$unownedTeam->id);

    $response->assertOk();
    $response->assertSee($ownedTeam->name);
    $response->assertDontSee($unownedTeam->name);
});

test('guardian can view dashboard team selection for linked team', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);

    $team = $coach->teams()->create([
        'name' => 'U12 Rangers',
        'age_group' => 'under-12s',
    ]);

    Player::create([
        'team_id' => $team->id,
        'name' => 'Player One',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0123456789',
        'guardian_id' => $guardian->id,
    ]);

    $response = $this->actingAs($guardian)
        ->get('/dashboard?team='.$team->id);

    $response->assertOk();
    $response->assertSee($team->name);
});
