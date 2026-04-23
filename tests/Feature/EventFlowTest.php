<?php

use App\Models\Event;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('coach can create event for own team and all players are attached', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U11 Panthers',
        'age_group' => 'under-11s',
    ]);

    $firstPlayer = Player::create([
        'team_id' => $team->id,
        'name' => 'P1',
        'guardian_name' => 'G1',
        'guardian_email' => 'g1@example.com',
        'guardian_phone' => '0001',
        'guardian_id' => User::factory()->create(['role' => 'guardian'])->id,
        'position' => 'cm',
    ]);
    $secondPlayer = Player::create([
        'team_id' => $team->id,
        'name' => 'P2',
        'guardian_name' => 'G2',
        'guardian_email' => 'g2@example.com',
        'guardian_phone' => '0002',
        'guardian_id' => User::factory()->create(['role' => 'guardian'])->id,
        'position' => 'st',
    ]);

    $response = $this->actingAs($coach)->post(route('events.store', $team), [
        'type' => 'training',
        'occurs_at' => now()->addDay()->toISOString(),
        'location' => 'Training Ground',
        'details' => 'Bring kit',
    ]);

    $response->assertRedirect(route('events.index', ['team' => $team->id], false));
    $event = Event::query()->where('team_id', $team->id)->firstOrFail();
    expect($event->players()->where('players.id', $firstPlayer->id)->exists())->toBeTrue();
    expect($event->players()->where('players.id', $secondPlayer->id)->exists())->toBeTrue();
});

test('coach cannot create event for foreign team', function () {
    $owner = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $owner->teams()->create([
        'name' => 'U15 Falcons',
        'age_group' => 'under-15s',
    ]);

    $response = $this->actingAs($otherCoach)->post(route('events.store', $team), [
        'type' => 'match',
        'occurs_at' => now()->addDay()->toISOString(),
        'location' => 'Away',
        'details' => 'Details',
    ]);

    $response->assertForbidden();
});

test('guardian can update own child event attendance while unrelated guardian cannot', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);
    $otherGuardian = User::factory()->create(['role' => 'guardian']);
    $team = $coach->teams()->create([
        'name' => 'U10 Sharks',
        'age_group' => 'under-10s',
    ]);

    $player = Player::create([
        'team_id' => $team->id,
        'name' => 'Player Three',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '1111',
        'guardian_id' => $guardian->id,
        'position' => 'rb',
    ]);

    $event = Event::create([
        'team_id' => $team->id,
        'type' => 'match',
        'occurs_at' => now()->addDay(),
        'location' => 'Home',
        'details' => 'League match',
    ]);
    $event->players()->attach($player->id);

    $this->actingAs($guardian)->post(route('events.update', [$event, $player]), [
        'player_response' => 'attending',
    ])->assertRedirect(route('event.show', $event, false));

    expect($event->players()->first()->pivot->player_response)->toBe('attending');

    $this->actingAs($otherGuardian)->post(route('events.update', [$event, $player]), [
        'player_response' => 'unavailable',
    ])->assertForbidden();
});

test('event response update rejects mismatched event and player pairing', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $teamA = $coach->teams()->create([
        'name' => 'U8 A',
        'age_group' => 'under-8s',
    ]);
    $teamB = $coach->teams()->create([
        'name' => 'U8 B',
        'age_group' => 'under-8s',
    ]);

    $player = Player::create([
        'team_id' => $teamB->id,
        'name' => 'Wrong Team Player',
        'guardian_name' => 'Guardian',
        'guardian_email' => 'guardian3@example.com',
        'guardian_phone' => '3333',
        'guardian_id' => User::factory()->create(['role' => 'guardian'])->id,
        'position' => 'lb',
    ]);
    $event = Event::create([
        'team_id' => $teamA->id,
        'type' => 'training',
        'occurs_at' => now()->addDay(),
        'location' => 'Pitch',
        'details' => 'Training',
    ]);

    $this->actingAs($coach)->post(route('events.update', [$event, $player]), [
        'player_response' => 'attending',
    ])->assertNotFound();
});
