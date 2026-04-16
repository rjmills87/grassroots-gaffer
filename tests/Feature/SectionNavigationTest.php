<?php

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests away from section pages', function (string $uri) {
    $this->get($uri)->assertRedirect('/login');
})->with([
    '/squad',
    '/events',
    '/announcements',
]);

it('allows a coach to access section pages', function (string $uri) {
    $coach = User::factory()->create(['role' => 'coach']);
    $coach->teams()->create([
        'name' => 'U14 Falcons',
        'age_group' => 'under-14s',
    ]);

    $this->actingAs($coach)
        ->get($uri)
        ->assertOk();
})->with([
    '/squad',
    '/events',
    '/announcements',
]);

it('allows a guardian to access section pages for linked teams', function (string $uri) {
    $coach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);

    $team = $coach->teams()->create([
        'name' => 'U12 Rangers',
        'age_group' => 'under-12s',
    ]);

    Player::create([
        'team_id' => $team->id,
        'name' => 'Aiden Green',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0123456789',
        'guardian_id' => $guardian->id,
    ]);

    $this->actingAs($guardian)
        ->get($uri)
        ->assertOk();
})->with([
    '/squad',
    '/events',
    '/announcements',
]);

it('ignores an invalid team query and falls back to an accessible team', function () {
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
        ->get('/announcements?team='.$unownedTeam->id);

    $response->assertOk();
    $response->assertSee($ownedTeam->name);
    $response->assertDontSee($unownedTeam->name);
});

it('does not show team overview links on section pages', function (string $uri) {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U9 Panthers',
        'age_group' => 'under-9s',
    ]);

    $response = $this->actingAs($coach)->get($uri.'?team='.$team->id);

    $response->assertOk();
    $response->assertDontSee('Open Team Overview');
})->with([
    '/squad',
    '/events',
    '/announcements',
]);

it('keeps selected team in breadcrumb links on section pages', function (string $uri) {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U8 Tigers',
        'age_group' => 'under-8s',
    ]);

    $response = $this->actingAs($coach)->get($uri.'?team='.$team->id);

    $response->assertOk();
    $response->assertSee($uri.'?team='.$team->id, false);
})->with([
    '/squad',
    '/events',
    '/announcements',
]);

it('respects selected team query on section pages', function (string $uri) {
    $coach = User::factory()->create(['role' => 'coach']);
    $coach->teams()->create([
        'name' => 'U10 Stars',
        'age_group' => 'under-10s',
    ]);
    $selectedTeam = $coach->teams()->create([
        'name' => 'U11 Strikers',
        'age_group' => 'under-11s',
    ]);

    $response = $this->actingAs($coach)->get($uri.'?team='.$selectedTeam->id);

    $response->assertOk();
    $response->assertSee($selectedTeam->name);
    $response->assertSee($uri.'?team='.$selectedTeam->id, false);
})->with([
    '/squad',
    '/events',
    '/announcements',
]);
