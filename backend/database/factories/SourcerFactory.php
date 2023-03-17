<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SourcerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {


        return [

            'code'      => rand(100000, 9999999999),
            'name'      => $this->faker->name,
        ];
    }
}
