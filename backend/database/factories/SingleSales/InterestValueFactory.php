<?php

namespace Database\Factories\SingleSales;

use App\Models\SingleSales\Category;
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
        $subCategory=Category::inRandomOrder()->first();
        return [
            "interest" => $this->faker->name,
            'category_id'=>$subCategory->id,
        ];
    }
}
