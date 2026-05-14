<?php

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('coach can post message to own team', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $response = $this->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Training moved to 6pm.',
    ]);

    $response->assertRedirect(route('announcements.index', ['team' => $team->id], false));
    $this->assertDatabaseHas('messages', [
        'team_id' => $team->id,
        'user_id' => $coach->id,
        'message' => 'Training moved to 6pm.',
    ]);
});

test('coach cannot post message to foreign team', function () {
    $owner = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $owner->teams()->create([
        'name' => 'U11 Blues',
        'age_group' => 'under-11s',
    ]);

    $response = $this->actingAs($otherCoach)->post(route('teams.messages.store', $team), [
        'message' => 'Unauthorized message',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('messages', [
        'team_id' => $team->id,
        'message' => 'Unauthorized message',
    ]);
});

test('message author can edit and delete while non-author cannot', function () {
    $author = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $author->teams()->create([
        'name' => 'U10 Stars',
        'age_group' => 'under-10s',
    ]);
    $message = Message::create([
        'user_id' => $author->id,
        'team_id' => $team->id,
        'message' => 'Original message',
    ]);

    $this->actingAs($author)->put(route('messages.update', $message), [
        'message' => 'Updated message',
    ])->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
        'message' => 'Updated message',
    ]);

    $this->actingAs($otherCoach)->put(route('messages.update', $message), [
        'message' => 'Malicious update',
    ])->assertForbidden();

    $this->actingAs($otherCoach)->delete(route('messages.destroy', $message))
        ->assertForbidden();

    $this->actingAs($author)->delete(route('messages.destroy', $message))
        ->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    $this->assertDatabaseMissing('messages', [
        'id' => $message->id,
    ]);
});
