<?php

namespace Database\Factories\ProductManagement;

use App\Models\ProductManagement\InterestCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterestValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category = InterestCategory::inRandomOrder()->first();
        return [
            "interest" => $this->faker->text(40),
            "interest_category_id" => $category->id
        ];
    }
}
