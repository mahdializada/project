<?php

namespace Database\Factories\Landing\Master\Product\Skill;


use App\Models\Landing\Master\Product\Skill\SkillCategory as SkillCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillSubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $sub = SkillCategory::inRandomOrder()->first();
        return [
            "name" => $this->faker->unique->name,
            "description" => $this->faker->unique->text,
            "icon" => "https://picsum.photos/id/11/10/6",
            "code" => rand(100000, 9999999999),
            "category_id" => $sub->id,
            // 'created_by' => $user->id
        ];
    }
}
