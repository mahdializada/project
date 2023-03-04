<?php

namespace Database\Factories\Landing\Master\Product\Requirement;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequirementCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        return [
            "name" => $this->faker->unique->name,
            "description" => $this->faker->unique->text,
            "icon" => "https://picsum.photos/id/11/10/6",
            "code" => rand(100000, 9999999999)
        ];
    }
}
