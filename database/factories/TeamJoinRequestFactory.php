<?php

namespace Database\Factories;

use App\Enums\JoinRequestStatus;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Support\InviteCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamJoinRequest>
 */
class TeamJoinRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'player_id' => null,
            'user_id' => null,
            'requested_player_name' => fake()->name(),
            'guardian_name' => fake()->name(),
            'guardian_email' => fake()->unique()->safeEmail(),
            'guardian_phone' => fake()->numerify('07#########'),
            'status' => JoinRequestStatus::Pending,
            'expires_at' => InviteCode::joinRequestExpiresAt(),
        ];
    }
}
