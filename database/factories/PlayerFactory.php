<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Player>
 */
class PlayerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $guardian = User::factory()->state(['role' => 'guardian']);

        return [
            'team_id' => Team::factory(),
            'name' => fake()->name(),
            'guardian_name' => fake()->name(),
            'guardian_email' => fake()->unique()->safeEmail(),
            'guardian_phone' => fake()->numerify('07#########'),
            'guardian_id' => $guardian,
            'squad_number' => fake()->numberBetween(1, 99),
            'position' => fake()->randomElement(Player::POSITIONS),
        ];
    }
}
