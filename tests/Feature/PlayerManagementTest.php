<?php

use App\Models\Event;
use App\Models\User;
use App\Notifications\WelcomeToTeamNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('coach can add player to own team and reuse existing guardian', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);

    $response = $this->actingAs($coach)->post(route('players.store', $team), [
        'name' => 'Player One',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0123400000',
        'position' => 'cm',
        'squad_number' => 8,
    ]);

    $response->assertRedirect(route('teams.show', $team, false));
    $this->assertDatabaseHas('players', [
        'team_id' => $team->id,
        'name' => 'Player One',
        'guardian_id' => $guardian->id,
        'squad_number' => 8,
        'position' => 'cm',
    ]);

    Notification::assertSentTo($guardian, WelcomeToTeamNotification::class);
});

test('coach cannot add player to another coach team', function () {
    $owner = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $owner->teams()->create([
        'name' => 'U14 Lions',
        'age_group' => 'under-14s',
    ]);

    $response = $this->actingAs($otherCoach)->post(route('players.store', $team), [
        'name' => 'Blocked Player',
        'guardian_name' => 'Guardian',
        'guardian_email' => 'guardian@example.com',
        'guardian_phone' => '0000000000',
        'position' => 'st',
        'squad_number' => 9,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('players', [
        'team_id' => $team->id,
        'name' => 'Blocked Player',
    ]);
});

test('newly added player is attached only to future events', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U9 Wolves',
        'age_group' => 'under-9s',
    ]);

    $pastEvent = Event::create([
        'team_id' => $team->id,
        'type' => 'match',
        'starts_at' => now()->subDay()->setTime(18, 0),
        'ends_at' => now()->subDay()->setTime(19, 30),
        'location' => 'Past Ground',
        'details' => 'Past event',
    ]);

    $futureEvent = Event::create([
        'team_id' => $team->id,
        'type' => 'training',
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Future Ground',
        'details' => 'Future event',
    ]);

    $this->actingAs($coach)->post(route('players.store', $team), [
        'name' => 'Player Two',
        'guardian_name' => 'Guardian Two',
        'guardian_email' => 'guardian-two@example.com',
        'guardian_phone' => '0123499999',
        'position' => 'gk',
        'squad_number' => 1,
    ])->assertRedirect(route('teams.show', $team, false));

    $playerId = (int) \DB::table('players')->where('name', 'Player Two')->value('id');

    expect($futureEvent->players()->where('players.id', $playerId)->exists())->toBeTrue();
    expect($pastEvent->players()->where('players.id', $playerId)->exists())->toBeFalse();
});
