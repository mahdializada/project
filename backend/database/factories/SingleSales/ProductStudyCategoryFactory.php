<?php

namespace Database\Factories\SingleSales;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStudyCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
           "category_name"=>$this->faker->unique->name
        ];
    }
}
