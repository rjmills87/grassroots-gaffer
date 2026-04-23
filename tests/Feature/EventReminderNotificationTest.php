<?php

use App\Models\Event;
use App\Models\Player;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('reminders are only sent to guardians without a response', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U13 United',
        'age_group' => 'under-13s',
    ]);
    $noResponseGuardian = User::factory()->create(['role' => 'guardian']);
    $attendingGuardian = User::factory()->create(['role' => 'guardian']);

    $noResponsePlayer = Player::create([
        'team_id' => $team->id,
        'name' => 'No Response',
        'guardian_name' => $noResponseGuardian->name,
        'guardian_email' => $noResponseGuardian->email,
        'guardian_phone' => '1000',
        'guardian_id' => $noResponseGuardian->id,
        'position' => 'cm',
    ]);
    $attendingPlayer = Player::create([
        'team_id' => $team->id,
        'name' => 'Attending',
        'guardian_name' => $attendingGuardian->name,
        'guardian_email' => $attendingGuardian->email,
        'guardian_phone' => '2000',
        'guardian_id' => $attendingGuardian->id,
        'position' => 'st',
    ]);

    $event = Event::create([
        'team_id' => $team->id,
        'type' => 'match',
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Main pitch',
        'details' => 'League game',
    ]);
    $event->players()->attach($noResponsePlayer->id);
    $event->players()->attach($attendingPlayer->id, ['player_response' => 'attending']);

    $response = $this->actingAs($coach)->post(route('events.sendReminders', $event));

    $response->assertRedirect(route('event.show', $event, false));
    Notification::assertSentTo($noResponseGuardian, EventReminderNotification::class);
    Notification::assertNotSentTo($attendingGuardian, EventReminderNotification::class);
});

test('unauthorized coach cannot send reminders for foreign team event', function () {
    Notification::fake();

    $owner = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $owner->teams()->create([
        'name' => 'U14 City',
        'age_group' => 'under-14s',
    ]);
    $event = Event::create([
        'team_id' => $team->id,
        'type' => 'training',
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Ground',
        'details' => 'Session',
    ]);

    $this->actingAs($otherCoach)->post(route('events.sendReminders', $event))->assertForbidden();
    Notification::assertNothingSent();
});
