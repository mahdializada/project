<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamMember>
 */
class TeamMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $team=Team::inRandomOrder()->first()->id;
        $user=User::inRandomOrder()->first()->id;
       
        return [
            'team_id'=>$team,
            'user_id'=>$user,
            'status' => $this->faker->randomElement(['pending ','active','inactive','blocked']),

        ];
    }
}
