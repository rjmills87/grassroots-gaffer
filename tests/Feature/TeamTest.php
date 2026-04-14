<?php

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('coach can create a team', function () {
    $coach = User::factory()->create([
        'role' => 'coach',
    ]);

    $this->actingAs($coach);

    $response = $this->post('/teams', [
        'team-name' => 'U12 Eagles',
        'age-group' => 'under-12s',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('teams', [
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
        'user_id' => $coach->id,
    ]);
});

test('guest cannot create a team', function () {
    $response = $this->post('/teams', [
        'team-name' => 'U12 Eagles',
        'age-group' => 'under-12s',
    ]);

    $response->assertRedirect('/login');
});

test('coach cannot create a team with invalid data', function () {
    $coach = User::factory()->create([
        'role' => 'coach',
    ]);

    $this->actingAs($coach);

    $response = $this->post('/teams', [
        'team-name' => '',
        'age-group' => '',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['team-name', 'age-group']);
});

test('coach can view own team show page', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U11 Lions',
        'age_group' => 'under-11s',
    ]);

    $response = $this->actingAs($coach)->get(route('teams.show', $team));

    $response->assertOk();
});

test('coach cannot view another coach team', function () {
    $owner = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $owner->teams()->create([
        'name' => 'U13 Tigers',
        'age_group' => 'under-13s',
    ]);

    $response = $this->actingAs($otherCoach)->get(route('teams.show', $team));

    $response->assertForbidden();
});

test('guardian can view team when linked to a player in that team', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);
    $team = $coach->teams()->create([
        'name' => 'U10 Eagles',
        'age_group' => 'under-10s',
    ]);

    Player::create([
        'team_id' => $team->id,
        'name' => 'Player One',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0123456789',
        'guardian_id' => $guardian->id,
    ]);

    $response = $this->actingAs($guardian)->get(route('teams.show', $team));

    $response->assertOk();
});
