<?php

use App\Enums\JoinRequestStatus;
use App\Models\Player;
use App\Models\TeamJoinRequest;
use App\Models\User;
use App\Notifications\JoinRequestReceivedNotification;
use App\Notifications\WelcomeToTeamNotification;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

test('new teams receive a unique invite code', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);

    expect($team->invite_code)->toHaveLength(8)
        ->and($team->invite_code_expires_at)->not->toBeNull();
});

test('join page stays reachable from public and auth screens', function () {
    $this->get(route('home'))->assertOk();
    $this->get(route('faq'))->assertOk();
    $this->get(route('login'))->assertOk();
    $this->get(route('register'))->assertOk();
    $this->get(route('join.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Join'));
});

test('parents can open a join page and type a code as a fallback', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);

    $this->get(route('join.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Join')
            ->where('valid', null)
            ->where('code', null)
        );

    $this->post(route('join.lookup'), [
        'code' => strtolower($team->invite_code),
    ])->assertRedirect(route('join.show', $team->invite_code));
});

test('whatsapp message includes the magic link and where to type the code', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);

    $message = $team->whatsappMessage();

    expect($message)->toContain($team->joinUrl())
        ->and($message)->toContain($team->joinPageUrl())
        ->and($message)->toContain($team->invite_code)
        ->and($message)->toContain('enter this code');
});

test('coach can view invite code on squad and other coaches cannot rotate it', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U11 Lions',
        'age_group' => 'under-11s',
    ]);

    $this->actingAs($coach)
        ->get(route('squad.index', ['team' => $team->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Squad')
            ->where('invite.code', $team->invite_code)
            ->where('invite.url', $team->joinUrl())
        );

    $this->actingAs($otherCoach)
        ->post(route('teams.invite.rotate', $team))
        ->assertForbidden();
});

test('guardian cannot see the invite code or rotate it', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);
    $team = $coach->teams()->create([
        'name' => 'U10 Sharks',
        'age_group' => 'under-10s',
    ]);

    Player::create([
        'team_id' => $team->id,
        'name' => 'Jamie Patel',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '07111111111',
        'guardian_id' => $guardian->id,
    ]);

    $this->actingAs($guardian)
        ->get(route('squad.index', ['team' => $team->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Squad')
            ->where('invite', null)
            ->where('pendingJoins', [])
        )
        ->assertDontSee($team->invite_code);

    $this->actingAs($guardian)
        ->post(route('teams.invite.rotate', $team))
        ->assertForbidden();
});

test('guest can open a valid join link and see only that team squad names', function () {
    $coach = User::factory()->create(['role' => 'coach', 'name' => 'Alex Coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);
    $otherTeam = $otherCoach->teams()->create([
        'name' => 'U13 Tigers',
        'age_group' => 'under-13s',
    ]);

    Player::create([
        'team_id' => $team->id,
        'name' => 'Jamie Patel',
        'guardian_name' => 'Sam Patel',
        'guardian_email' => 'sam@example.com',
        'guardian_phone' => '07000000001',
        'guardian_id' => User::factory()->create(['role' => 'guardian'])->id,
        'squad_number' => 7,
    ]);
    Player::create([
        'team_id' => $otherTeam->id,
        'name' => 'Secret Other Team Child',
        'guardian_name' => 'Other Parent',
        'guardian_email' => 'other-parent@example.com',
        'guardian_phone' => '07000000002',
        'guardian_id' => User::factory()->create(['role' => 'guardian'])->id,
        'squad_number' => 9,
    ]);

    $this->get(route('join.show', $team->invite_code))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Join')
            ->where('valid', true)
            ->where('team.name', 'U12 Eagles')
            ->has('players', 1)
            ->where('players.0.name', 'Jamie Patel')
            ->missing('players.1')
        )
        ->assertDontSee('Secret Other Team Child')
        ->assertDontSee('U13 Tigers')
        ->assertDontSee('sam@example.com');
});

test('parent can request to join an existing child and coach can approve a second guardian', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach', 'name' => 'Alex Coach']);
    $firstGuardian = User::factory()->create(['role' => 'guardian', 'name' => 'Sam Patel']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);
    $player = Player::create([
        'team_id' => $team->id,
        'name' => 'Jamie Patel',
        'guardian_name' => $firstGuardian->name,
        'guardian_email' => $firstGuardian->email,
        'guardian_phone' => '07000000001',
        'guardian_id' => $firstGuardian->id,
        'squad_number' => 7,
        'position' => 'cm',
    ]);

    $response = $this->post(route('join.store', $team->invite_code), [
        'guardian_name' => 'Riley Patel',
        'guardian_email' => 'riley@example.com',
        'guardian_phone' => '07000000099',
        'child_mode' => 'existing',
        'player_id' => $player->id,
    ]);

    $response->assertRedirect(route('join.submitted'));
    $this->assertDatabaseHas('team_join_requests', [
        'team_id' => $team->id,
        'player_id' => $player->id,
        'guardian_email' => 'riley@example.com',
        'status' => JoinRequestStatus::Pending->value,
    ]);
    Notification::assertSentTo($coach, JoinRequestReceivedNotification::class);

    $joinRequest = TeamJoinRequest::query()->first();

    $this->actingAs($coach)
        ->get(route('squad.index', ['team' => $team->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Squad')
            ->has('pendingJoins', 1)
            ->where('pendingJoins.0.guardian_name', 'Riley Patel')
            ->where('pendingJoins.0.player_name', 'Jamie Patel')
            ->where('pendingJoins.0.is_new_player', false)
        );

    $this->actingAs($coach)
        ->post(route('team-join-requests.approve', $joinRequest))
        ->assertRedirect(route('squad.index', ['team' => $team->id]));

    $secondGuardian = User::query()->where('email', 'riley@example.com')->first();
    expect($secondGuardian)->not->toBeNull()
        ->and($secondGuardian->role)->toBe('guardian')
        ->and($player->fresh()->isGuardedBy($secondGuardian))->toBeTrue()
        ->and($player->fresh()->guardian_id)->toBe($firstGuardian->id);

    $this->assertDatabaseHas('player_guardians', [
        'player_id' => $player->id,
        'user_id' => $firstGuardian->id,
    ]);
    $this->assertDatabaseHas('player_guardians', [
        'player_id' => $player->id,
        'user_id' => $secondGuardian->id,
    ]);

    Notification::assertSentTo($secondGuardian, WelcomeToTeamNotification::class);

    $this->actingAs($secondGuardian)
        ->get(route('squad.index', ['team' => $team->id]))
        ->assertOk()
        ->assertSee('Jamie Patel');
});

test('parent can request a child who is not listed and coach approval creates the player', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach', 'name' => 'Alex Coach']);
    $team = $coach->teams()->create([
        'name' => 'U9 Wolves',
        'age_group' => 'under-9s',
    ]);

    $this->post(route('join.store', $team->invite_code), [
        'guardian_name' => 'Jordan Lee',
        'guardian_email' => 'jordan@example.com',
        'guardian_phone' => '07000000077',
        'child_mode' => 'new',
        'requested_player_name' => 'Avery Lee',
    ])->assertRedirect(route('join.submitted'));

    $joinRequest = TeamJoinRequest::query()->first();

    $this->actingAs($coach)
        ->post(route('team-join-requests.approve', $joinRequest))
        ->assertRedirect(route('squad.index', ['team' => $team->id]));

    $this->assertDatabaseHas('players', [
        'team_id' => $team->id,
        'name' => 'Avery Lee',
        'guardian_email' => 'jordan@example.com',
    ]);

    $guardian = User::query()->where('email', 'jordan@example.com')->first();
    $player = Player::query()->where('name', 'Avery Lee')->first();

    expect($guardian)->not->toBeNull()
        ->and($player->isGuardedBy($guardian))->toBeTrue();
});

test('invalid join codes are rejected', function () {
    $this->get(route('join.show', 'NOTREAL1'))
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Join')
            ->where('valid', false)
            ->where('reason', 'invalid')
        );
});

test('expired join codes are rejected', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U8 Owls',
        'age_group' => 'under-8s',
    ]);
    $team->forceFill([
        'invite_code_expires_at' => now()->subDay(),
    ])->save();

    $this->get(route('join.show', $team->invite_code))
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Join')
            ->where('valid', false)
            ->where('reason', 'expired')
            ->where('team.name', 'U8 Owls')
        );

    $this->post(route('join.store', $team->invite_code), [
        'guardian_name' => 'Parent',
        'guardian_email' => 'parent@example.com',
        'guardian_phone' => '07000000000',
        'child_mode' => 'new',
        'requested_player_name' => 'Child',
    ])->assertSessionHasErrors('code');
});

test('rotated join codes no longer work', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U15 Rockets',
        'age_group' => 'under-15s',
    ]);
    $oldCode = $team->invite_code;

    $this->actingAs($coach)
        ->post(route('teams.invite.rotate', $team))
        ->assertRedirect(route('squad.index', ['team' => $team->id]));

    $team->refresh();
    expect($team->invite_code)->not->toBe($oldCode);

    $this->get(route('join.show', $oldCode))
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Join')
            ->where('valid', false)
            ->where('reason', 'invalid')
        );

    $this->get(route('join.show', $team->invite_code))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Join')
            ->where('valid', true)
            ->where('team.name', 'U15 Rockets')
        );
});

test('join request cannot attach a player from another team', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);
    $otherTeam = $otherCoach->teams()->create([
        'name' => 'U13 Tigers',
        'age_group' => 'under-13s',
    ]);
    $foreignPlayer = Player::create([
        'team_id' => $otherTeam->id,
        'name' => 'Foreign Child',
        'guardian_name' => 'Foreign Parent',
        'guardian_email' => 'foreign@example.com',
        'guardian_phone' => '07000000003',
        'guardian_id' => User::factory()->create(['role' => 'guardian'])->id,
    ]);

    $this->post(route('join.store', $team->invite_code), [
        'guardian_name' => 'Riley Patel',
        'guardian_email' => 'riley@example.com',
        'guardian_phone' => '07000000099',
        'child_mode' => 'existing',
        'player_id' => $foreignPlayer->id,
    ])->assertSessionHasErrors('player_id');

    $this->assertDatabaseMissing('team_join_requests', [
        'guardian_email' => 'riley@example.com',
    ]);
});

test('coach cannot approve another team join request', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);
    $joinRequest = TeamJoinRequest::factory()->create([
        'team_id' => $team->id,
        'requested_player_name' => 'Avery Lee',
        'guardian_name' => 'Jordan Lee',
        'guardian_email' => 'jordan@example.com',
    ]);

    $this->actingAs($otherCoach)
        ->post(route('team-join-requests.approve', $joinRequest))
        ->assertForbidden();

    $this->actingAs($otherCoach)
        ->post(route('team-join-requests.reject', $joinRequest))
        ->assertForbidden();

    expect($joinRequest->fresh()->status)->toBe(JoinRequestStatus::Pending);
});

test('guest cannot rotate a join code', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);

    $this->post(route('teams.invite.rotate', $team))
        ->assertRedirect(route('login'));
});

test('duplicate pending join requests are rejected', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);
    $player = Player::create([
        'team_id' => $team->id,
        'name' => 'Jamie Patel',
        'guardian_name' => 'Sam Patel',
        'guardian_email' => 'sam@example.com',
        'guardian_phone' => '07000000001',
        'guardian_id' => User::factory()->create(['role' => 'guardian'])->id,
        'squad_number' => 7,
    ]);

    $payload = [
        'guardian_name' => 'Riley Patel',
        'guardian_email' => 'riley@example.com',
        'guardian_phone' => '07000000099',
        'child_mode' => 'existing',
        'player_id' => $player->id,
    ];

    $this->post(route('join.store', $team->invite_code), $payload)
        ->assertRedirect(route('join.submitted'));

    $this->post(route('join.store', $team->invite_code), $payload)
        ->assertSessionHasErrors('player_id');
});

test('coach email cannot be used to join as a parent', function () {
    $coach = User::factory()->create(['role' => 'coach', 'email' => 'coach@example.com']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);

    $this->post(route('join.store', $team->invite_code), [
        'guardian_name' => 'Not A Parent',
        'guardian_email' => 'coach@example.com',
        'guardian_phone' => '07000000000',
        'child_mode' => 'new',
        'requested_player_name' => 'Some Child',
    ])->assertSessionHasErrors('guardian_email');
});

test('second guardian can rsvp for the same child after approval', function () {
    Notification::fake();

    $coach = User::factory()->create(['role' => 'coach']);
    $firstGuardian = User::factory()->create(['role' => 'guardian']);
    $team = $coach->teams()->create([
        'name' => 'U10 Sharks',
        'age_group' => 'under-10s',
    ]);
    $player = Player::create([
        'team_id' => $team->id,
        'name' => 'Jamie Patel',
        'guardian_name' => $firstGuardian->name,
        'guardian_email' => $firstGuardian->email,
        'guardian_phone' => '07000000001',
        'guardian_id' => $firstGuardian->id,
        'position' => 'st',
    ]);

    $this->post(route('join.store', $team->invite_code), [
        'guardian_name' => 'Riley Patel',
        'guardian_email' => 'riley@example.com',
        'guardian_phone' => '07000000099',
        'child_mode' => 'existing',
        'player_id' => $player->id,
    ]);

    $joinRequest = TeamJoinRequest::query()->first();
    $this->actingAs($coach)->post(route('team-join-requests.approve', $joinRequest));
    $secondGuardian = User::query()->where('email', 'riley@example.com')->first();

    $event = $team->events()->create([
        'type' => 'training',
        'starts_at' => now()->addDay()->setTime(18, 0),
        'ends_at' => now()->addDay()->setTime(19, 30),
        'location' => 'Home pitch',
        'details' => 'Tuesday training',
    ]);
    $event->players()->attach($player->id);

    $this->actingAs($secondGuardian)->post(route('events.players.update', [$event, $player]), [
        'player_response' => 'attending',
    ])->assertRedirect(route('event.show', $event, false));

    expect($event->players()->first()->pivot->player_response)->toBe('attending');
});

test('parents already linked to a child cannot submit another join request', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $guardian = User::factory()->create(['role' => 'guardian']);
    $team = $coach->teams()->create([
        'name' => 'U12 Eagles',
        'age_group' => 'under-12s',
    ]);
    $player = Player::create([
        'team_id' => $team->id,
        'name' => 'Jamie Patel',
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '07000000001',
        'guardian_id' => $guardian->id,
    ]);

    $this->post(route('join.store', $team->invite_code), [
        'guardian_name' => $guardian->name,
        'guardian_email' => $guardian->email,
        'guardian_phone' => '07000000001',
        'child_mode' => 'existing',
        'player_id' => $player->id,
    ])->assertSessionHasErrors('player_id');
});
