<?php

namespace Database\Factories\SingleSales;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActionClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "type" => $this->faker->randomElement(["instruction", "preprations"]),
            "statement" => $this->faker->text,
              
        ];
    }
}
