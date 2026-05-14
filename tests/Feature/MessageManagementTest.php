<?php

use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Player;
use App\Models\User;
use App\Notifications\NewTeamAnnouncementNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

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

test('coach can post message with valid attachments', function () {
    Storage::fake('public');

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $attachments = [
        UploadedFile::fake()->create('notice.pdf', 500, 'application/pdf'),
        UploadedFile::fake()->create('photo.jpg', 500, 'image/jpeg'),
    ];

    $response = $this->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Please read the attachments.',
        'attachments' => $attachments,
    ]);

    $response->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    $message = Message::query()->where('team_id', $team->id)->firstOrFail();
    expect($message->attachments()->count())->toBe(2);
    Storage::disk('public')->assertExists($message->attachments()->first()->file_path);
});

test('invalid message attachment type is rejected', function () {
    Storage::fake('public');

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $response = $this->from(route('announcements.index', ['team' => $team->id]))->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Hello',
        'attachments' => [
            UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
        ],
    ]);

    $response->assertRedirect(route('announcements.index', ['team' => $team->id], false));
    $response->assertSessionHasErrors('attachments.0');
});

test('oversized message attachment is rejected', function () {
    Storage::fake('public');

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $response = $this->from(route('announcements.index', ['team' => $team->id]))->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Hello',
        'attachments' => [
            UploadedFile::fake()->create('large.pdf', 11000, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect(route('announcements.index', ['team' => $team->id], false));
    $response->assertSessionHasErrors('attachments.0');
});

test('more than three message attachments is rejected', function () {
    Storage::fake('public');

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $response = $this->from(route('announcements.index', ['team' => $team->id]))->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Hello',
        'attachments' => [
            UploadedFile::fake()->create('one.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('two.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('three.png', 100, 'image/png'),
            UploadedFile::fake()->create('four.pdf', 100, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect(route('announcements.index', ['team' => $team->id], false));
    $response->assertSessionHasErrors('attachments');
});

test('coach can update message attachments removing one and adding another', function () {
    Storage::fake('public');

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $this->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Original',
        'attachments' => [
            UploadedFile::fake()->create('keep.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('remove.jpg', 100, 'image/jpeg'),
        ],
    ]);

    $message = Message::query()->where('team_id', $team->id)->firstOrFail();
    $toRemove = $message->attachments()->where('original_name', 'remove.jpg')->firstOrFail();

    $this->actingAs($coach)->post(route('messages.update', $message), [
        'message' => 'Updated body',
        'removed_attachment_ids' => [$toRemove->id],
        'attachments' => [
            UploadedFile::fake()->create('new.png', 100, 'image/png'),
        ],
        '_method' => 'PUT',
    ])->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    $message->refresh();
    expect($message->attachments()->count())->toBe(2);
    expect($message->attachments()->pluck('original_name')->all())->toContain('keep.pdf')->toContain('new.png');
    expect($message->attachments()->where('original_name', 'remove.jpg')->exists())->toBeFalse();
});

test('message attachment preview and download require team access', function () {
    Storage::fake('public');

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $message = Message::create([
        'user_id' => $coach->id,
        'team_id' => $team->id,
        'message' => 'Notice',
    ]);

    Storage::disk('public')->put('message_attachments/doc.pdf', 'fake-pdf');
    $attachment = $message->attachments()->create([
        'file_path' => 'message_attachments/doc.pdf',
        'original_name' => 'doc.pdf',
        'mime_type' => 'application/pdf',
        'size' => 8,
    ]);

    $this->actingAs($coach)->get(route('messages.attachments.preview', $attachment))->assertOk();
    $this->actingAs($coach)->get(route('messages.attachments.download', $attachment))->assertOk();

    $stranger = User::factory()->create(['role' => 'guardian']);
    $this->actingAs($stranger)->get(route('messages.attachments.preview', $attachment))->assertForbidden();

    $guardian = User::factory()->create(['role' => 'guardian']);
    Player::create([
        'team_id' => $team->id,
        'name' => 'Child',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0123456789',
        'guardian_id' => $guardian->id,
        'position' => 'cm',
    ]);

    $this->actingAs($guardian)->get(route('messages.attachments.preview', $attachment))->assertOk();
});

test('deleting message removes attachment files from storage', function () {
    Storage::fake('public');

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $this->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'With file',
        'attachments' => [
            UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ],
    ]);

    $message = Message::query()->where('team_id', $team->id)->firstOrFail();
    $path = $message->attachments()->first()->file_path;

    $this->actingAs($coach)->delete(route('messages.destroy', $message))
        ->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    Storage::disk('public')->assertMissing($path);
    expect(MessageAttachment::query()->where('message_id', $message->id)->exists())->toBeFalse();
});

test('new announcement notifies each distinct guardian once', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Reds',
        'age_group' => 'under-12s',
    ]);

    $guardianA = User::factory()->create(['role' => 'guardian']);
    $guardianB = User::factory()->create(['role' => 'guardian']);

    Player::create([
        'team_id' => $team->id,
        'name' => 'Player One',
        'guardian_name' => $guardianA->name,
        'guardian_email' => $guardianA->email,
        'guardian_phone' => '0111111111',
        'guardian_id' => $guardianA->id,
        'position' => 'cm',
    ]);
    Player::create([
        'team_id' => $team->id,
        'name' => 'Player Two',
        'guardian_name' => $guardianB->name,
        'guardian_email' => $guardianB->email,
        'guardian_phone' => '0222222222',
        'guardian_id' => $guardianB->id,
        'position' => 'st',
    ]);

    $this->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Team BBQ this Saturday.',
    ])->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    Notification::assertSentTo($guardianA, NewTeamAnnouncementNotification::class);
    Notification::assertSentTo($guardianB, NewTeamAnnouncementNotification::class);
    Notification::assertSentToTimes($guardianA, NewTeamAnnouncementNotification::class, 1);
    Notification::assertSentToTimes($guardianB, NewTeamAnnouncementNotification::class, 1);
});

test('new announcement dedupes when multiple players share the same guardian', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U9 Greens',
        'age_group' => 'under-9s',
    ]);

    $guardian = User::factory()->create(['role' => 'guardian']);

    Player::create([
        'team_id' => $team->id,
        'name' => 'Sibling One',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0333333333',
        'guardian_id' => $guardian->id,
        'position' => 'gk',
    ]);
    Player::create([
        'team_id' => $team->id,
        'name' => 'Sibling Two',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0333333333',
        'guardian_id' => $guardian->id,
        'position' => 'cb',
    ]);

    $this->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'One email per household.',
    ])->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    Notification::assertSentToTimes($guardian, NewTeamAnnouncementNotification::class, 1);
});

test('new announcement does not notify when team has no players', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'Empty Roster FC',
        'age_group' => 'under-8s',
    ]);

    $this->actingAs($coach)->post(route('teams.messages.store', $team), [
        'message' => 'Pitch closed.',
    ])->assertRedirect(route('announcements.index', ['team' => $team->id], false));

    Notification::assertNothingSent();
});

test('foreign coach posting message does not send announcement notifications', function () {
    Notification::fake();

    $owner = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $owner->teams()->create([
        'name' => 'U11 Blues',
        'age_group' => 'under-11s',
    ]);

    $guardian = User::factory()->create(['role' => 'guardian']);
    Player::create([
        'team_id' => $team->id,
        'name' => 'Kid',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '0444444444',
        'guardian_id' => $guardian->id,
        'position' => 'lm',
    ]);

    $this->actingAs($otherCoach)->post(route('teams.messages.store', $team), [
        'message' => 'Should not exist',
    ])->assertForbidden();

    Notification::assertNothingSent();
});
