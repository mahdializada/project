<?php

namespace Database\Factories\ProductManagement;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudyCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name
        ];
    }
}
