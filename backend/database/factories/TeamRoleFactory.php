<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamRole>
 */
class TeamRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $team=Team::inRandomOrder()->first()->id;
        $role=Role::inRandomOrder()->first()->id;
        return [
            'team_id'=>$team,
            'role_id'=>$role,
        ];
    }
}
