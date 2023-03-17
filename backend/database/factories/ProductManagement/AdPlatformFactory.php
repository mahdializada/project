<?php

namespace Database\Factories\ProductManagement;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdPlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name,
            "is_active" => rand(0, 1)
        ];
    }
}
