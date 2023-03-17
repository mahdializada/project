<?php

namespace Database\Factories\Landing\Master\Product\Requirement;

use App\Models\Landing\Master\Product\Requirement\RequirementCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequirementSubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sub = RequirementCategory::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
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
