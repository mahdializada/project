<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
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
            "status" => $this->faker->randomElement(Role::getTypes()),
            "schedule_type" => "unlimited",
            "created_by" => User::inRandomOrder()->first()->id,
            "updated_by" => User::inRandomOrder()->first()->id,
        ];
    }
}
