<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "code" => rand(100000, 9999999999),
            "name" => $this->faker->name,
            "number_of_people" => $this->faker->randomDigitNotNull,
            "note" => $this->faker->unique->text,
            "status" => $this->faker->randomElement(Team::getTypes()),
            "schedule_type" => "unlimited",
            "created_by" => User::inRandomOrder()->first()->id,
            "updated_by" => User::inRandomOrder()->first()->id,
        ];
    }
}
