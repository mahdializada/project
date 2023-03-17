<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\StudySubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sub_category = StudySubCategory::inRandomOrder()->first();
        return [
            "name" => $this->faker->name,
            "sub_category_id" => $sub_category->id
        ];
    }
}
