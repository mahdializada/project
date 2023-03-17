<?php

namespace Database\Factories\SingleSales;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "question" => $this->faker->unique->text
        ];
    }
}
