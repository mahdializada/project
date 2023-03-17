<?php

namespace Database\Factories\Landing;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Landing\Master\Product\Skill\SkillSubCategory;

class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sub = SkillSubCategory::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        return [
            "name" => $this->faker->unique->name,
            "description" => $this->faker->unique->text,
            "sub_category_id" => $sub->id,
            "code" => rand(100000, 9999999999),
            "icon" => "https://picsum.photos/id/11/10/6",
            // "created_by" => $user->id
        ];
    }
}
