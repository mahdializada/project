<?php

namespace Database\Factories\Advertisement;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformNameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name(),
            "code" => rand(1000, 99999999),
        ];
    }
}
