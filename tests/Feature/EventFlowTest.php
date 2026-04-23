<?php

use App\Models\Event;
use App\Models\Player;
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
        'starts_at' => now()->addDay()->setTime(18, 0)->toISOString(),
        'ends_at' => now()->addDay()->setTime(19, 30)->toISOString(),
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
        'starts_at' => now()->addDay()->setTime(18, 0)->toISOString(),
        'ends_at' => now()->addDay()->setTime(19, 30)->toISOString(),
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
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Home',
        'details' => 'League match',
    ]);
    $event->players()->attach($player->id);

    $this->actingAs($guardian)->post(route('events.players.update', [$event, $player]), [
        'player_response' => 'attending',
    ])->assertRedirect(route('event.show', $event, false));

    expect($event->players()->first()->pivot->player_response)->toBe('attending');

    $this->actingAs($otherGuardian)->post(route('events.players.update', [$event, $player]), [
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
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Pitch',
        'details' => 'Training',
    ]);

    $this->actingAs($coach)->post(route('events.players.update', [$event, $player]), [
        'player_response' => 'attending',
    ])->assertNotFound();
});

test('coach cannot create event when finish time is before start time', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U11 Panthers',
        'age_group' => 'under-11s',
    ]);

    $response = $this->from(route('events.index', ['team' => $team->id]))->actingAs($coach)->post(route('events.store', $team), [
        'type' => 'training',
        'starts_at' => now()->addDay()->setTime(19, 30)->toISOString(),
        'ends_at' => now()->addDay()->setTime(18, 0)->toISOString(),
        'location' => 'Training Ground',
        'details' => 'Bring kit',
    ]);

    $response->assertRedirect(route('events.index', ['team' => $team->id], false));
    $response->assertSessionHasErrors('ends_at');
});

test('coach can update and delete future own event', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U11 Panthers',
        'age_group' => 'under-11s',
    ]);

    $event = Event::create([
        'team_id' => $team->id,
        'type' => 'training',
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Pitch 1',
        'details' => 'Bring kit',
    ]);

    $this->actingAs($coach)->patch(route('events.manage.update', $event), [
        'type' => 'match',
        'starts_at' => now()->addDay()->setTime(19, 0)->toISOString(),
        'ends_at' => now()->addDay()->setTime(20, 30)->toISOString(),
        'location' => 'Pitch 2',
        'details' => 'Arrive early',
    ])->assertRedirect(route('event.show', $event, false));

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'type' => 'match',
        'location' => 'Pitch 2',
        'details' => 'Arrive early',
    ]);

    $this->actingAs($coach)->delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index', ['team' => $team->id], false));

    $this->assertDatabaseMissing('events', [
        'id' => $event->id,
    ]);
});

test('coach cannot update or delete past event', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U11 Panthers',
        'age_group' => 'under-11s',
    ]);

    $event = Event::create([
        'team_id' => $team->id,
        'type' => 'training',
        'starts_at' => now()->subHour(),
        'ends_at' => now()->addHour(),
        'location' => 'Pitch 1',
        'details' => 'Bring kit',
    ]);

    $this->actingAs($coach)->patch(route('events.manage.update', $event), [
        'type' => 'match',
        'starts_at' => now()->addDay()->setTime(19, 0)->toISOString(),
        'ends_at' => now()->addDay()->setTime(20, 30)->toISOString(),
        'location' => 'Pitch 2',
        'details' => 'Arrive early',
    ])->assertForbidden();

    $this->actingAs($coach)->delete(route('events.destroy', $event))->assertForbidden();
});

test('non-owner coach and guardian cannot update or delete event', function () {
    $ownerCoach = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);
    $team = $ownerCoach->teams()->create([
        'name' => 'U11 Panthers',
        'age_group' => 'under-11s',
    ]);

    $event = Event::create([
        'team_id' => $team->id,
        'type' => 'training',
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Pitch 1',
        'details' => 'Bring kit',
    ]);

    $payload = [
        'type' => 'match',
        'starts_at' => now()->addDay()->setTime(19, 0)->toISOString(),
        'ends_at' => now()->addDay()->setTime(20, 30)->toISOString(),
        'location' => 'Pitch 2',
        'details' => 'Arrive early',
    ];

    $this->actingAs($otherCoach)->patch(route('events.manage.update', $event), $payload)->assertForbidden();
    $this->actingAs($guardian)->patch(route('events.manage.update', $event), $payload)->assertForbidden();

    $this->actingAs($otherCoach)->delete(route('events.destroy', $event))->assertForbidden();
    $this->actingAs($guardian)->delete(route('events.destroy', $event))->assertForbidden();
});
