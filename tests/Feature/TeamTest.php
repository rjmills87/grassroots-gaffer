<?php

use App\Models\User;

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
    $response->assertSessionHasErrors([
        'team-name' => 'The team-name field is required.',
    ]);
    $response->assertSessionHasErrors([
        'age-group' => 'The age-group field is required.',
    ]);
});
