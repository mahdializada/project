<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\StudyCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudySubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category = StudyCategory::inRandomOrder()->first();
        return [
            "name" => $this->faker->name,
            "category_id" => $category->id
        ];
    }
}
