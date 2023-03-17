<?php

namespace Database\Factories\SingleSales;

use Illuminate\Database\Eloquent\Factories\Factory;

class SellingGoalsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "goals" => $this->faker->unique->text,
        ];
    }
}
